<?php

/**
 * ម៉ូដែល WorkLocation - តារាងទីតាំងធ្វើការ
 * 
 * ម៉ូដែលនេះគ្រប់គ្រងព័ត៌មានទីតាំងធ្វើការទាំងអស់ រួមមាន:
 * - ព័ត៌មានទូទៅនៃទីតាំង (ឈ្មោះ, អាស័យដ្ឋាន, ប្រភេទ)
 * - ព័ត៌មាន GPS សម្រាប់កំណត់ដែន (geo-fencing)
 * - ការកំណត់សុវត្ថិភាព (WiFi, Bluetooth)
 * - ព័ត៌មានពេលវេលាធ្វើការ
 * - ទំនាក់ទំនងជាមួយបុគ្គលិក
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'work_locations';

    protected $fillable = [
        // ព័ត៌មានទូទៅ
        'location_code',
        'name',
        'name_kh',
        'description',
        'address',
        'city',
        'province',
        'location_type',
        
        // ព័ត៌មាន GPS
        'latitude',
        'longitude',
        'radius_meters',
        
        // ការកំណត់សុវត្ថិភាព
        'requires_wifi_ssid',
        'allowed_wifi_ssid',
        'requires_bluetooth',
        'bluetooth_uuid',
        
        // ស្ថានភាព
        'status',
        'is_default',
        
        // ពេលវេលាធ្វើការ
        'opening_time',
        'closing_time',
        'working_days',
        
        // អ្នកគ្រប់គ្រង
        'manager_id',
        'created_by',
        'updated_by',
        
        // រូបថតនិងឯកសារ
        'location_photo',
        'floor_plan',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meters' => 'integer',
        'requires_wifi_ssid' => 'boolean',
        'requires_bluetooth' => 'boolean',
        'is_default' => 'boolean',
        'working_days' => 'array',
        'opening_time' => 'datetime:H:i:s',
        'closing_time' => 'datetime:H:i:s',
    ];

    /**
     * ទំនាក់ទំនង - អ្នកគ្រប់គ្រងទីតាំង
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * ទំនាក់ទំនង - បុគ្គលិកដែលបានចំណង់នៅទីតាំងនេះ
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_locations', 'location_id', 'employee_id')
            ->withPivot([
                'assignment_type',
                'start_date',
                'end_date',
                'can_check_in',
                'can_check_out',
                'is_location_manager',
                'require_strict_location',
                'custom_radius_meters',
                'status',
                'notes',
                'assigned_by'
            ])
            ->withTimestamps();
    }

    /**
     * ទំនាក់ទំនង - បុគ្គលិកធ្វើការនៅទីតាំងនេះ (active only)
     */
    public function activeEmployees()
    {
        return $this->employees()
            ->wherePivot('status', 'active')
            ->where(function ($query) {
                $query->whereNull('employee_locations.end_date')
                    ->orWhere('employee_locations.end_date', '>=', now());
            });
    }

    /**
     * ទំនាក់ទំនង - អ្នកបង្កើត
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ទំនាក់ទំនង - អ្នកកែប្រែចុងក្រោយ
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * រកទីតាំងតាមលេខកូដ
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('location_code', $code);
    }

    /**
     * រកទីតាំងដែលកំពុងសកម្ម
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * រកទីតាំងតាមប្រភេទ
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('location_type', $type);
    }

    /**
     * រកទីតាំងគោល (default)
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * គណនាចម្ងាយពី GPS coordinates (Haversine formula)
     * 
     * @param float $lat រយៈទទឹងចំណុចដែលត្រូវពិនិត្យ
     * @param float $lng រយៈបណ្ដោយចំណុចដែលត្រូវពិនិត្យ
     * @return float ចម្ងាយជាម៉ែត្រ
     */
    public function calculateDistance(float $lat, float $lng): float
    {
        if (is_null($this->latitude) || is_null($this->longitude)) {
            return -1; // ទីតាំងនេះមិនមាន GPS coordinates
        }

        $earthRadius = 6371000; // កាំផែនដីជាម៉ែត្រ

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    /**
     * ពិនិត្យថាតើចំណុច GPS នៅក្នុងកម្រាស់ដែលអនុញ្ញាតឬអត់
     * 
     * @param float $lat រយៈទទឹង
     * @param float $lng រយៈបណ្ដោយ
     * @param int|null $customRadius កម្រាស់ផ្ទាល់ខ្លួន (ប្រសិនបើ null នឹងប្រើ default)
     * @return array លទ្ធផលពិនិត្យ [valid, distance, allowed_radius]
     */
    public function validateLocation(float $lat, float $lng, ?int $customRadius = null): array
    {
        $distance = $this->calculateDistance($lat, $lng);
        
        if ($distance < 0) {
            return [
                'valid' => false,
                'error' => 'ទីតាំងនេះមិនទាន់បានកំណត់ GPS coordinates',
                'distance' => null,
                'allowed_radius' => $customRadius ?? $this->radius_meters
            ];
        }

        $allowedRadius = $customRadius ?? $this->radius_meters;
        $isValid = $distance <= $allowedRadius;

        return [
            'valid' => $isValid,
            'distance' => round($distance, 2), // ចម្ងាយជាម៉ែត្រ (បង្គត់២កន្លែង)
            'allowed_radius' => $allowedRadius,
            'message' => $isValid 
                ? 'ទីតាំងត្រឹមត្រូវ' 
                : 'អ្នកស្ថិតនៅឆ្ងាយពីទីតាំងធ្វើការដែលកំណត់ (' . round($distance, 0) . 'm ពី ' . $allowedRadius . 'm ដែលអនុញ្ញាត)'
        ];
    }

    /**
     * ពិនិត្យ WiFi SSID
     * 
     * @param string|null $connectedSsid ឈ្មោះ WiFi ដែលកំពុងភ្ជាប់
     * @return array លទ្ធផលពិនិត្យ
     */
    public function validateWifi(?string $connectedSsid): array
    {
        if (!$this->requires_wifi_ssid) {
            return ['required' => false, 'valid' => true];
        }

        if (empty($connectedSsid)) {
            return [
                'required' => true,
                'valid' => false,
                'error' => 'ត្រូវការភ្ជាប់ WiFi របស់ហាងសម្រាប់កត់ត្រាអវត្តមាន'
            ];
        }

        $isValid = $connectedSsid === $this->allowed_wifi_ssid;

        return [
            'required' => true,
            'valid' => $isValid,
            'expected' => $this->allowed_wifi_ssid,
            'connected' => $connectedSsid,
            'error' => $isValid ? null : 'អ្នកត្រូវភ្ជាប់ WiFi ឈ្មោះ "' . $this->allowed_wifi_ssid . '"'
        ];
    }

    /**
     * ទទួលឈ្មោះប្រភេទទីតាំងជាភាសាខ្មែរ
     */
    public function getLocationTypeKhmer(): string
    {
        $types = [
            'main_shop' => 'ហាងស្នាក់ការកណ្ដាល',
            'branch' => 'សាខា',
            'warehouse' => 'ឃ្លាំង',
            'kitchen' => 'ផ្ទះបាយ',
            'office' => 'ការិយាល័យ',
            'other' => 'កន្លែងផ្សេងៗ'
        ];

        return $types[$this->location_type] ?? $this->location_type;
    }

    /**
     * ទទួលស្ថានភាពជាភាសាខ្មែរ
     */
    public function getStatusKhmer(): string
    {
        $statuses = [
            'active' => 'សកម្ម',
            'inactive' => 'អសកម្ម',
            'maintenance' => 'កំពុងថែទាំ'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * ទទួល URL រូបថតទីតាំង
     */
    public function getLocationPhotoUrl(): ?string
    {
        return $this->location_photo ? asset('storage/' . $this->location_photo) : null;
    }

    /**
     * ទទួលព័ត៌មានទីតាំងសង្ខេបសម្រាប់ API
     */
    public function toArrayForApi(): array
    {
        return [
            'id' => $this->id,
            'location_code' => $this->location_code,
            'name' => $this->name,
            'name_kh' => $this->name_kh,
            'location_type' => $this->location_type,
            'location_type_kh' => $this->getLocationTypeKhmer(),
            'address' => $this->address,
            'city' => $this->city,
            'gps' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius_meters' => $this->radius_meters
            ],
            'working_hours' => [
                'opening' => $this->opening_time?->format('H:i'),
                'closing' => $this->closing_time?->format('H:i'),
                'days' => $this->working_days
            ],
            'status' => $this->status,
            'status_kh' => $this->getStatusKhmer(),
            'photo_url' => $this->getLocationPhotoUrl()
        ];
    }
}
