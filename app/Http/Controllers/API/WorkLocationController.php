<?php

/**
 * WorkLocationController - កន្លែងគ្រប់គ្រង API ទីតាំងធ្វើការ
 * 
 * កន្លែងនេះផ្តល់ API endpoints សម្រាប់៖
 * - ទិញយកទីតាំងធ្វើការទាំងអស់
 * - បង្ហាញព័ត៌មានលម្អិតទីតាំង
 * - ពិនិត្យទីតាំង GPS (validate location)
 * - កំណត់ទីតាំងគោលសម្រាប់បុគ្គលិក
 * 
 * ទំព័រនេះសរសេរជាភាសាខ្មែរដើម្បីងាយស្រួលថែទាំ
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkLocation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WorkLocationController extends Controller
{
    /**
     * ទិញយកទីតាំងធ្វើការទាំងអស់
     * 
     * GET /api/locations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = WorkLocation::with('manager');

            // ត្រងតាមប្រភេទទីតាំង
            if ($request->has('type')) {
                $query->byType($request->type);
            }

            // ត្រងតាមស្ថានភាព
            if ($request->has('status')) {
                $query->where('status', $request->status);
            } else {
                $query->active(); // Default បង្ហាញតែសកម្ម
            }

            // ត្រងតាមឈ្មោះ
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('name_kh', 'like', "%{$search}%")
                        ->orWhere('location_code', 'like', "%{$search}%");
                });
            }

            $locations = $query->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'message' => 'ទទួលបានទីតាំងធ្វើការចំនួន ' . $locations->count(),
                'data' => [
                    'locations' => $locations->map->toArrayForApi()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការទទួលទីតាំង',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * បង្ហាញព័ត៌មានលម្អិតទីតាំង
     * 
     * GET /api/locations/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $location = WorkLocation::with(['manager', 'activeEmployees'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'ទទួលបានព័ត៌មានទីតាំង',
                'data' => [
                    'location' => array_merge($location->toArrayForApi(), [
                        'description' => $location->description,
                        'province' => $location->province,
                        'security_settings' => [
                            'requires_wifi' => $location->requires_wifi_ssid,
                            'wifi_ssid' => $location->allowed_wifi_ssid,
                            'requires_bluetooth' => $location->requires_bluetooth,
                            'bluetooth_uuid' => $location->bluetooth_uuid
                        ],
                        'employee_count' => $location->activeEmployees->count(),
                        'manager' => $location->manager ? [
                            'id' => $location->manager->id,
                            'name' => $location->manager->full_name,
                            'employee_id' => $location->manager->employee_id
                        ] : null
                    ])
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញទីតាំងនេះទេ',
                'error' => 'Location not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការទទួលព័ត៌មានទីតាំង',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * បង្ហាញព័ត៌មានទីតាំងតាមលេខកូដ
     * 
     * GET /api/locations/code/{code}
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $location = WorkLocation::byCode($code)->first();

            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'រកមិនឃើញទីតាំងដែលមានលេខកូដ ' . $code,
                    'error' => 'Location not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'ទទួលបានព័ត៌មានទីតាំង',
                'data' => [
                    'location' => $location->toArrayForApi()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការទទួលទីតាំង',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ពិនិត្យថាតើចំណុច GPS នៅក្នុងកម្រាស់ដែលអនុញ្ញាតឬអត់ (Geo-fencing)
     * 
     * POST /api/locations/validate
     * 
     * Body: {
     *   "location_id": 1,
     *   "latitude": 11.5564,
     *   "longitude": 104.9282,
     *   "wifi_ssid": "CoffeeShop-WiFi" (optional)
     * }
     */
    public function validateLocation(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'location_id' => 'required|exists:work_locations,id',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'wifi_ssid' => 'nullable|string',
                'employee_id' => 'nullable|exists:employees,id'
            ], [
                'location_id.required' => 'ត្រូវការបញ្ជាក់លេខសម្គាល់ទីតាំង',
                'location_id.exists' => 'ទីតាំងនេះមិនមានក្នុងប្រព័ន្ធទេ',
                'latitude.required' => 'ត្រូវការបញ្ជាក់រយៈទទឹង GPS',
                'longitude.required' => 'ត្រូវការបញ្ជាក់រយៈបណ្ដោយ GPS',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ទិន្នន័យមិនត្រឹមត្រូវ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $location = WorkLocation::findOrFail($request->location_id);
            
            // ពិនិត្យបុគ្គលិកមានសិទ្ធិនៅទីតាំងនេះឬអត់
            $customRadius = null;
            if ($request->has('employee_id')) {
                $employeeLocation = \DB::table('employee_locations')
                    ->where('employee_id', $request->employee_id)
                    ->where('location_id', $request->location_id)
                    ->where('status', 'active')
                    ->where('can_check_in', true)
                    ->first();

                if (!$employeeLocation) {
                    return response()->json([
                        'success' => false,
                        'message' => 'អ្នកមិនមានសិទ្ធិកត់ត្រាអវត្តមាននៅទីតាំងនេះទេ',
                        'error' => 'Employee not assigned to this location or cannot check in'
                    ], 403);
                }

                $customRadius = $employeeLocation->custom_radius_meters;
            }

            // ពិនិត្យ GPS location
            $gpsValidation = $location->validateLocation(
                $request->latitude, 
                $request->longitude, 
                $customRadius
            );

            // ពិនិត្យ WiFi (ប្រសិនបើទាមទារ)
            $wifiValidation = $location->validateWifi($request->wifi_ssid);

            $isValid = $gpsValidation['valid'] && $wifiValidation['valid'];

            return response()->json([
                'success' => true,
                'message' => $isValid ? 'ទីតាំងត្រឹមត្រូវ' : 'ទីតាំងមិនត្រឹមត្រូវ',
                'data' => [
                    'is_valid' => $isValid,
                    'location' => [
                        'id' => $location->id,
                        'code' => $location->location_code,
                        'name' => $location->name,
                        'name_kh' => $location->name_kh
                    ],
                    'validation' => [
                        'gps' => [
                            'valid' => $gpsValidation['valid'],
                            'distance_meters' => $gpsValidation['distance'],
                            'allowed_radius' => $gpsValidation['allowed_radius'],
                            'user_latitude' => $request->latitude,
                            'user_longitude' => $request->longitude,
                            'location_latitude' => $location->latitude,
                            'location_longitude' => $location->longitude,
                            'message' => $gpsValidation['message'] ?? null
                        ],
                        'wifi' => [
                            'required' => $wifiValidation['required'],
                            'valid' => $wifiValidation['valid'],
                            'error' => $wifiValidation['error'] ?? null
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការពិនិត្យទីតាំង',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ទិញយកទីតាំងដែលបុគ្គលិកបានចំណង់
     * 
     * GET /api/employees/{employeeId}/locations
     */
    public function getEmployeeLocations(int $employeeId): JsonResponse
    {
        try {
            $employee = Employee::findOrFail($employeeId);
            
            $locations = $employee->workLocations()
                ->wherePivot('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('employee_locations.end_date')
                        ->orWhere('employee_locations.end_date', '>=', now());
                })
                ->withPivot([
                    'assignment_type',
                    'can_check_in',
                    'can_check_out',
                    'require_strict_location',
                    'custom_radius_meters'
                ])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'ទទួលបានទីតាំងធ្វើការរបស់បុគ្គលិក',
                'data' => [
                    'employee' => [
                        'id' => $employee->id,
                        'name' => $employee->full_name,
                        'employee_id' => $employee->employee_id
                    ],
                    'locations' => $locations->map(function ($location) {
                        $assignmentTypes = [
                            'primary' => 'ទីតាំងគោល',
                            'secondary' => 'ទីតាំងជំនួស',
                            'temporary' => 'ទីតាំងបណ្ដោះអាសន្ន',
                            'all' => 'គ្រប់ទីតាំង'
                        ];

                        return array_merge($location->toArrayForApi(), [
                            'assignment' => [
                                'type' => $location->pivot->assignment_type,
                                'type_kh' => $assignmentTypes[$location->pivot->assignment_type] ?? $location->pivot->assignment_type,
                                'can_check_in' => (bool) $location->pivot->can_check_in,
                                'can_check_out' => (bool) $location->pivot->can_check_out,
                                'strict_location' => (bool) $location->pivot->require_strict_location,
                                'custom_radius' => $location->pivot->custom_radius_meters
                            ]
                        ]);
                    })
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញបុគ្គលិកនេះទេ',
                'error' => 'Employee not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការទទួលទីតាំងបុគ្គលិក',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * កំណត់ទីតាំងគោលសម្រាប់បុគ្គលិក
     * 
     * POST /api/employees/{employeeId}/default-location
     */
    public function setDefaultLocation(Request $request, int $employeeId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'location_id' => 'required|exists:work_locations,id'
            ], [
                'location_id.required' => 'ត្រូវការបញ្ជាក់លេខសម្គាល់ទីតាំង',
                'location_id.exists' => 'ទីតាំងនេះមិនមានក្នុងប្រព័ន្ធទេ'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ទិន្នន័យមិនត្រឹមត្រូវ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $employee = Employee::findOrFail($employeeId);
            $location = WorkLocation::findOrFail($request->location_id);

            // ពិនិត្យថាបុគ្គលិកបានចំណង់នៅទីតាំងនេះឬអត់
            $hasAssignment = \DB::table('employee_locations')
                ->where('employee_id', $employeeId)
                ->where('location_id', $request->location_id)
                ->where('status', 'active')
                ->exists();

            if (!$hasAssignment) {
                // បង្កើតការចំណង់ថ្មី
                \DB::table('employee_locations')->insert([
                    'employee_id' => $employeeId,
                    'location_id' => $request->location_id,
                    'assignment_type' => 'primary',
                    'can_check_in' => true,
                    'can_check_out' => true,
                    'status' => 'active',
                    'assigned_by' => auth()->id(),
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // កែប្រែ default_location_id
            $employee->default_location_id = $request->location_id;
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'បានកំណត់ទីតាំងគោលដោយជោគជ័យ',
                'data' => [
                    'employee' => [
                        'id' => $employee->id,
                        'name' => $employee->full_name
                    ],
                    'default_location' => $location->toArrayForApi()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការកំណត់ទីតាំងគោល',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ទិញយកទីតាំងដែលនៅជិត GPS coordinates ដែលបានផ្ដល់
     * 
     * POST /api/locations/nearby
     * 
     * Body: {
     *   "latitude": 11.5564,
     *   "longitude": 104.9282,
     *   "radius": 1000 // កម្រាស់ជាម៉ែត្រ (default 1000m)
     * }
     */
    public function getNearbyLocations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:1|max:50000' // Max 50km
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ទិន្នន័យ GPS មិនត្រឹមត្រូវ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userLat = $request->latitude;
            $userLng = $request->longitude;
            $radius = $request->radius ?? 1000; // Default 1km

            // ទិញយកទីតាំងដែលមាន GPS ហើយសកម្ម
            $locations = WorkLocation::active()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->map(function ($location) use ($userLat, $userLng) {
                    $distance = $location->calculateDistance($userLat, $userLng);
                    $location->distance_meters = round($distance, 2);
                    return $location;
                })
                ->filter(function ($location) use ($radius) {
                    return $location->distance_meters <= $radius;
                })
                ->sortBy('distance_meters')
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'រកឃើញទីតាំងចំនួន ' . $locations->count() . ' នៅជិតអ្នក',
                'data' => [
                    'user_location' => [
                        'latitude' => $userLat,
                        'longitude' => $userLng
                    ],
                    'search_radius_meters' => $radius,
                    'locations' => $locations->map(function ($location) {
                        return array_merge($location->toArrayForApi(), [
                            'distance_meters' => $location->distance_meters,
                            'distance_formatted' => $location->distance_meters < 1000 
                                ? round($location->distance_meters) . ' ម៉ែត្រ'
                                : round($location->distance_meters / 1000, 1) . ' គីឡូម៉ែត្រ'
                        ]);
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការស្វែងរកទីតាំងជិត',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
