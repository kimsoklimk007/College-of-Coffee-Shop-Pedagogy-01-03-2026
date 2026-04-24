<?php

/**
 * AttendanceController - API សម្រាប់កត់ត្រាអវត្តមាន
 * 
 * API នេះគ្រប់គ្រង៖
 * - កត់ត្រាពេលចូលធ្វើការ (check-in)
 * - កត់ត្រាពេលចេញធ្វើការ (check-out)
 * - ពិនិត្យទីតាំង GPS (geo-fencing)
 * - បង្ហាញប្រវត្តិអវត្តមាន
 * 
 * ទំព័រនេះសរសេរជាភាសាខ្មែរដើម្បីងាយស្រួលថែទាំ
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WorkLocation;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected QRCodeService $qrService;

    public function __construct(QRCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * កត់ត្រាពេលចូលធ្វើការតាមរយៈ QR Code (Check-in)
     * 
     * POST /api/attendance/check-in
     * 
     * Body: {
     *   "qr_data": "...",
     *   "latitude": 11.5564,
     *   "longitude": 104.9282,
     *   "wifi_ssid": "CoffeeShop-WiFi",
     *   "device_info": "iPhone 14 - iOS 16"
     * }
     */
    public function checkIn(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'qr_data' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'wifi_ssid' => 'nullable|string',
                'device_info' => 'nullable|string',
                'check_in_photo' => 'nullable|string' // Base64 encoded photo
            ], [
                'qr_data.required' => 'ត្រូវការទិន្នន័យ QR Code',
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

            // ពិនិត្យ QR Code
            $validation = $this->qrService->validateAttendanceQR($request->qr_data);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code មិនត្រឹមត្រូវ',
                    'error' => $validation['error']
                ], 400);
            }

            $employee = $validation['employee'];
            $qrData = $validation['data'];
            
            // ពិនិត្យថាតើប្រភេទសកម្មភាពត្រឹមត្រូវឬអត់
            if ($qrData['action'] !== 'check_in') {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code នេះមិនមែនសម្រាប់ចូលធ្វើការទេ',
                    'error' => 'Wrong QR type. This is for ' . $qrData['action']
                ], 400);
            }

            // ពិនិត្យថាបុគ្គលិកបាន check-in ហើយឬអត់
            $today = now()->toDateString();
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->where('attendance_date', $today)
                ->whereNotNull('check_in_time')
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'អ្នកបានចូលធ្វើការហើយនៅថ្ងៃនេះ',
                    'data' => [
                        'check_in_time' => $existingAttendance->check_in_time->format('H:i:s'),
                        'already_checked_in' => true
                    ]
                ], 409);
            }

            // ពិនិត្យទីតាំង GPS
            $locationValidation = $this->validateEmployeeLocation(
                $employee,
                $request->latitude,
                $request->longitude,
                $request->wifi_ssid,
                $qrData['location']['id'] ?? null
            );

            if (!$locationValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'ទីតាំងមិនត្រឹមត្រូវ',
                    'error' => $locationValidation['message'],
                    'location_data' => $locationValidation['data']
                ], 403);
            }

            // រក្សាទុករូបថតប្រសិនបើមាន
            $checkInPhoto = null;
            if ($request->has('check_in_photo')) {
                $checkInPhoto = $this->saveBase64Photo(
                    $request->check_in_photo,
                    'attendance',
                    $employee->employee_id . '_checkin_' . time()
                );
            }

            // បង្កើតការកត់ត្រាអវត្តមាន
            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'shift_id' => $employee->shift_id,
                'attendance_date' => $today,
                'check_in_time' => now(),
                'check_in_location' => $request->latitude . ',' . $request->longitude,
                'device_info' => $request->device_info,
                'ip_address' => $request->ip(),
                'check_in_photo' => $checkInPhoto,
                'status' => $locationValidation['late'] ? 'Late' : 'Present',
                'late_minutes' => $locationValidation['late_minutes'] ?? 0,
                'approval_status' => 'Approved' // អនុញ្ញាតដោយស្វ័យប្រវត្តិ
            ]);

            return response()->json([
                'success' => true,
                'message' => 'បានកត់ត្រាពេលចូលធ្វើការដោយជោគជ័យ',
                'data' => [
                    'attendance' => [
                        'id' => $attendance->id,
                        'employee_id' => $employee->employee_id,
                        'employee_name' => $employee->full_name,
                        'check_in_time' => $attendance->check_in_time->format('H:i:s'),
                        'date' => $attendance->attendance_date,
                        'status' => $attendance->status,
                        'status_kh' => $this->getStatusKhmer($attendance->status),
                        'late_minutes' => $attendance->late_minutes,
                        'location' => $locationValidation['location_name'] ?? 'Unknown'
                    ],
                    'validation' => [
                        'location_verified' => true,
                        'distance_meters' => $locationValidation['distance'] ?? null,
                        'is_late' => $locationValidation['late'] ?? false
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការកត់ត្រាពេលចូល',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * កត់ត្រាពេលចេញធ្វើការតាមរយៈ QR Code (Check-out)
     * 
     * POST /api/attendance/check-out
     */
    public function checkOut(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'qr_data' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'wifi_ssid' => 'nullable|string',
                'device_info' => 'nullable|string',
                'check_out_photo' => 'nullable|string'
            ], [
                'qr_data.required' => 'ត្រូវការទិន្នន័យ QR Code',
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

            // ពិនិត្យ QR Code
            $validation = $this->qrService->validateAttendanceQR($request->qr_data);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code មិនត្រឹមត្រូវ',
                    'error' => $validation['error']
                ], 400);
            }

            $employee = $validation['employee'];
            $qrData = $validation['data'];
            
            // ពិនិត្យថាតើប្រភេទសកម្មភាពត្រឹមត្រូវឬអត់
            if ($qrData['action'] !== 'check_out') {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code នេះមិនមែនសម្រាប់ចេញធ្វើការទេ',
                    'error' => 'Wrong QR type'
                ], 400);
            }

            // រកការកត់ត្រាអវត្តមានថ្ងៃនេះ
            $today = now()->toDateString();
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('attendance_date', $today)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'អ្នកមិនទាន់បានចូលធ្វើការថ្ងៃនេះទេ',
                    'error' => 'No check-in record found'
                ], 400);
            }

            if ($attendance->check_out_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'អ្នកបានចេញធ្វើការហើយនៅថ្ងៃនេះ',
                    'data' => [
                        'check_out_time' => $attendance->check_out_time->format('H:i:s'),
                        'already_checked_out' => true
                    ]
                ], 409);
            }

            // ពិនិត្យទីតាំង GPS
            $locationValidation = $this->validateEmployeeLocation(
                $employee,
                $request->latitude,
                $request->longitude,
                $request->wifi_ssid,
                $qrData['location']['id'] ?? null
            );

            if (!$locationValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'ទីតាំងមិនត្រឹមត្រូវ',
                    'error' => $locationValidation['message']
                ], 403);
            }

            // រក្សាទុករូបថតប្រសិនបើមាន
            $checkOutPhoto = null;
            if ($request->has('check_out_photo')) {
                $checkOutPhoto = $this->saveBase64Photo(
                    $request->check_out_photo,
                    'attendance',
                    $employee->employee_id . '_checkout_' . time()
                );
            }

            // គណនាពេលវេលាធ្វើការសរុប
            $checkOutTime = now();
            $totalMinutes = $attendance->check_in_time->diffInMinutes($checkOutTime);
            
            // កែប្រែការកត់ត្រា
            $attendance->update([
                'check_out_time' => $checkOutTime,
                'check_out_location' => $request->latitude . ',' . $request->longitude,
                'check_out_photo' => $checkOutPhoto,
                'total_work_minutes' => $totalMinutes,
                'early_departure_minutes' => $locationValidation['early_minutes'] ?? 0,
                'status' => $this->calculateStatus($attendance, $locationValidation['early_minutes'] ?? 0)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'បានកត់ត្រាពេលចេញធ្វើការដោយជោគជ័យ',
                'data' => [
                    'attendance' => [
                        'id' => $attendance->id,
                        'employee_id' => $employee->employee_id,
                        'employee_name' => $employee->full_name,
                        'check_in_time' => $attendance->check_in_time->format('H:i:s'),
                        'check_out_time' => $checkOutTime->format('H:i:s'),
                        'total_work_hours' => round($totalMinutes / 60, 2),
                        'date' => $attendance->attendance_date,
                        'status' => $attendance->status,
                        'status_kh' => $this->getStatusKhmer($attendance->status)
                    ],
                    'validation' => [
                        'location_verified' => true,
                        'total_work_minutes' => $totalMinutes
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការកត់ត្រាពេលចេញ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ពិនិត្យទីតាំងរបស់បុគ្គលិក (Geo-fencing)
     */
    private function validateEmployeeLocation(
        Employee $employee, 
        float $latitude, 
        float $longitude, 
        ?string $wifiSsid,
        ?int $expectedLocationId
    ): array {
        // រកទីតាំងដែលបុគ្គលិកបានចំណង់
        $employeeLocations = \DB::table('employee_locations')
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->where('can_check_in', true)
            ->get();

        if ($employeeLocations->isEmpty()) {
            return [
                'valid' => false,
                'message' => 'អ្នកមិនត្រូវបានចំណង់ទីតាំងធ្វើការទេ'
            ];
        }

        // ពិនិត្យទីតាំងនីមួយៗ
        foreach ($employeeLocations as $empLocation) {
            $location = WorkLocation::find($empLocation->location_id);
            
            if (!$location || $location->status !== 'active') {
                continue;
            }

            // ប្រសិនបើបានបញ្ជាក់ location ID ជាក់លាក់ ត្រួវតែត្រូវគ្នា
            if ($expectedLocationId && $location->id != $expectedLocationId) {
                continue;
            }

            // ពិនិត្យ GPS
            $radius = $empLocation->custom_radius_meters ?? $location->radius_meters;
            $gpsValidation = $location->validateLocation($latitude, $longitude, $radius);

            // ពិនិត្យ WiFi
            $wifiValidation = $location->validateWifi($wifiSsid);

            if ($gpsValidation['valid'] && $wifiValidation['valid']) {
                // ពិនិត្យថាតើយឺតពេលឬអត់
                $shift = $employee->shift;
                $isLate = false;
                $lateMinutes = 0;

                if ($shift && $shift->start_time) {
                    $startTime = Carbon::parse($shift->start_time);
                    $currentTime = now();
                    
                    if ($currentTime->gt($startTime)) {
                        $lateMinutes = $currentTime->diffInMinutes($startTime);
                        $isLate = $lateMinutes > 5; // យឺតលើស 5 នាទីគិតថាយឺត
                    }
                }

                return [
                    'valid' => true,
                    'location_id' => $location->id,
                    'location_name' => $location->name,
                    'distance' => $gpsValidation['distance'],
                    'late' => $isLate,
                    'late_minutes' => $lateMinutes
                ];
            }

            // បើអត់ត្រឹមត្រូវ បង្ហាញមូលហេតុ
            if (!$gpsValidation['valid']) {
                return [
                    'valid' => false,
                    'message' => $gpsValidation['message'],
                    'data' => [
                        'user_latitude' => $latitude,
                        'user_longitude' => $longitude,
                        'location_latitude' => $location->latitude,
                        'location_longitude' => $location->longitude,
                        'distance' => $gpsValidation['distance'],
                        'allowed_radius' => $gpsValidation['allowed_radius']
                    ]
                ];
            }

            if (!$wifiValidation['valid']) {
                return [
                    'valid' => false,
                    'message' => $wifiValidation['error']
                ];
            }
        }

        return [
            'valid' => false,
            'message' => 'អ្នកមិននៅក្នុងទីតាំងដែលបានអនុញ្ញាតទេ'
        ];
    }

    /**
     * បង្ហាញប្រវត្តិអវត្តមានរបស់បុគ្គលិក
     * 
     * GET /api/attendance/history/{employeeId}
     */
    public function getHistory(int $employeeId, Request $request): JsonResponse
    {
        try {
            $employee = Employee::findOrFail($employeeId);

            $query = Attendance::where('employee_id', $employeeId)
                ->orderBy('attendance_date', 'desc');

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('attendance_date', [$request->start_date, $request->end_date]);
            } else {
                // Default: last 30 days
                $query->where('attendance_date', '>=', now()->subDays(30));
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $attendance = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'បានទទួលប្រវត្តិអវត្តមាន',
                'data' => [
                    'employee' => [
                        'id' => $employee->id,
                        'name' => $employee->full_name,
                        'employee_id' => $employee->employee_id
                    ],
                    'summary' => [
                        'total_days' => $attendance->count(),
                        'present_days' => $attendance->where('status', 'Present')->count(),
                        'late_days' => $attendance->where('status', 'Late')->count(),
                        'absent_days' => $attendance->where('status', 'Absent')->count()
                    ],
                    'records' => $attendance->map(function ($record) {
                        return [
                            'id' => $record->id,
                            'date' => $record->attendance_date,
                            'check_in' => $record->check_in_time?->format('H:i:s'),
                            'check_out' => $record->check_out_time?->format('H:i:s'),
                            'work_hours' => $record->total_work_minutes > 0 
                                ? round($record->total_work_minutes / 60, 2) 
                                : null,
                            'status' => $record->status,
                            'status_kh' => $this->getStatusKhmer($record->status),
                            'late_minutes' => $record->late_minutes > 0 ? $record->late_minutes : null,
                            'early_minutes' => $record->early_departure_minutes > 0 
                                ? $record->early_departure_minutes 
                                : null
                        ];
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
                'message' => 'មានបញ្ហាក្នុងការទទួលប្រវត្តិ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * រក្សាទុករូបថត Base64
     */
    private function saveBase64Photo(string $base64Data, string $directory, string $filename): string
    {
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Data));
        $path = "{$directory}/{$filename}.png";
        
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory, 0755, true);
        }
        
        Storage::disk('public')->put($path, $imageData);
        
        return $path;
    }

    /**
     * គណនាស្ថានភាពអវត្តមាន
     */
    private function calculateStatus(Attendance $attendance, int $earlyMinutes): string
    {
        if ($earlyMinutes > 0) {
            return 'Half Day';
        }
        
        return $attendance->status;
    }

    /**
     * ទទួលស្ថានភាពជាភាសាខ្មែរ
     */
    private function getStatusKhmer(string $status): string
    {
        $statuses = [
            'Present' => 'វត្តមាន',
            'Late' => 'យឺត',
            'Absent' => 'អវត្តមាន',
            'Half Day' => 'ការងារពាក់កណ្ដាលថ្ងៃ',
            'On Leave' => 'ឈប់សម្រាក'
        ];

        return $statuses[$status] ?? $status;
    }
}
