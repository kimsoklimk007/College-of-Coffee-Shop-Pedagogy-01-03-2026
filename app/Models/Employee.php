<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'user_id',
        'role_id',
        'shift_id',
        'default_location_id',
        'first_name',
        'last_name',
        'first_name_kh',
        'last_name_kh',
        'gender',
        'date_of_birth',
        'nationality',
        'id_card_number',
        'passport_number',
        'phone',
        'email',
        'address',
        'address_kh',
        'city',
        'province',
        'emergency_contact_name',
        'emergency_contact_phone',
        'hire_date',
        'end_date',
        'base_salary',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'employment_type',
        'has_qr_code',
        'qr_code_path',
        'id_card_qr_path',
        'id_card_qr_generated_at',
        'attendance_qr_path',
        'attendance_qr_generated_at',
        'attendance_qr_expires_at',
        'qr_code_status',
        'profile_photo',
        'id_card_photo',
        'contract_document',
        'other_documents',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'end_date' => 'date',
        'base_salary' => 'decimal:2',
        'has_qr_code' => 'boolean',
        'other_documents' => 'array',
        'id_card_qr_generated_at' => 'datetime',
        'attendance_qr_generated_at' => 'datetime',
        'attendance_qr_expires_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(EmployeeRole::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * ទំនាក់ទំនង - ទីតាំងគោល (default location)
     */
    public function defaultLocation()
    {
        return $this->belongsTo(WorkLocation::class, 'default_location_id');
    }

    /**
     * ទំនាក់ទំនង - ទីតាំងធ្វើការទាំងអស់ (work locations)
     */
    public function workLocations()
    {
        return $this->belongsToMany(WorkLocation::class, 'employee_locations', 'employee_id', 'location_id')
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
                'notes'
            ])
            ->withTimestamps();
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function payroll()
    {
        return $this->hasMany(Payroll::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullNameKhAttribute()
    {
        if ($this->first_name_kh && $this->last_name_kh) {
            return $this->first_name_kh . ' ' . $this->last_name_kh;
        }
        return $this->full_name;
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth->age;
    }

    public function getServiceYearsAttribute()
    {
        return $this->hire_date->diffInYears(now());
    }

    public function getServiceDaysAttribute()
    {
        return $this->hire_date->diffInDays(now());
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    public function scopeResigned($query)
    {
        return $query->where('status', 'Resigned');
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function scopeByShift($query, $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    // Methods
    public function generateEmployeeId()
    {
        $lastEmployee = Employee::withTrashed()->orderBy('id', 'desc')->first();
        $lastNumber = $lastEmployee ? (int)substr($lastEmployee->employee_id, 3) : 0;
        $newNumber = $lastNumber + 1;
        $this->employee_id = 'EMP' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        return $this->employee_id;
    }

    public function generateQrCode()
    {
        $qrService = app(\App\Services\QRCodeService::class);
        $qrPath = $qrService->generateEmployeeQR($this);
        
        $this->has_qr_code = true;
        $this->qr_code_path = $qrPath;
        
        return $this->qr_code_path;
    }

    public function getTodayAttendance()
    {
        return $this->attendance()->where('attendance_date', today())->first();
    }

    public function getMonthlyAttendance($year, $month)
    {
        return $this->attendance()
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get();
    }

    public function getCurrentLeaveBalance($leaveType)
    {
        return $this->leaveBalances()
            ->where('leave_type', $leaveType)
            ->where('year', now()->year)
            ->first();
    }

    public function updateLeaveBalance($leaveType, $daysUsed, $operation = 'subtract')
    {
        $balance = $this->getCurrentLeaveBalance($leaveType);
        
        if (!$balance) {
            $balance = LeaveBalance::create([
                'employee_id' => $this->id,
                'leave_type' => $leaveType,
                'total_days' => $this->getAnnualLeaveDays($leaveType),
                'used_days' => 0,
                'remaining_days' => $this->getAnnualLeaveDays($leaveType),
                'year' => now()->year,
            ]);
        }

        if ($operation === 'subtract') {
            $balance->used_days += $daysUsed;
            $balance->remaining_days = max(0, $balance->total_days - $balance->used_days);
        } else {
            $balance->used_days = max(0, $balance->used_days - $daysUsed);
            $balance->remaining_days = $balance->total_days - $balance->used_days;
        }

        $balance->save();
        return $balance;
    }

    private function getAnnualLeaveDays($leaveType)
    {
        $leaveDays = [
            'Annual' => 18,
            'Sick' => 10,
            'Personal' => 5,
            'Maternity' => 90,
            'Paternity' => 14,
        ];

        return $leaveDays[$leaveType] ?? 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_id)) {
                $employee->generateEmployeeId();
            }
            if (auth()->check()) {
                $employee->created_by = auth()->id();
            }
        });

        static::updating(function ($employee) {
            if (auth()->check()) {
                $employee->updated_by = auth()->id();
            }
        });
    }
}
