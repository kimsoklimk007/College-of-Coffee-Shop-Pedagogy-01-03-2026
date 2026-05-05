<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'shift_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'break_start_time',
        'break_end_time',
        'status',
        'approval_status',
        'total_work_minutes',
        'overtime_minutes',
        'late_minutes',
        'early_departure_minutes',
        'check_in_location',
        'check_out_location',
        'device_info',
        'ip_address',
        'check_in_photo',
        'check_out_photo',
        'notes',
        'admin_notes',
        'approved_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'break_start_time' => 'datetime',
        'break_end_time' => 'datetime',
        'approved_at' => 'datetime',
        'total_work_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'late_minutes' => 'integer',
        'early_departure_minutes' => 'integer',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'Present');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'Late');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'Absent');
    }

    public function scopeOnLeave($query)
    {
        return $query->where('status', 'On Leave');
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'Approved');
    }

    // Accessors
    public function getFormattedCheckInTimeAttribute()
    {
        return $this->check_in_time ? $this->check_in_time->format('H:i') : null;
    }

    public function getFormattedCheckOutTimeAttribute()
    {
        return $this->check_out_time ? $this->check_out_time->format('H:i') : null;
    }

    public function getTotalWorkHoursAttribute()
    {
        return $this->total_work_minutes / 60;
    }

    public function getOvertimeHoursAttribute()
    {
        return $this->overtime_minutes / 60;
    }

    public function getLateHoursAttribute()
    {
        return $this->late_minutes / 60;
    }

    // Methods
    public function checkIn($checkInTime = null, $location = null, $deviceInfo = null, $photo = null)
    {
        $checkInTime = $checkInTime ?: now();
        
        $this->check_in_time = $checkInTime;
        $this->check_in_location = $location;
        $this->device_info = $deviceInfo;
        $this->check_in_photo = $photo;
        $this->ip_address = request()->ip();

        // Determine status based on shift
        if ($this->shift) {
            $this->late_minutes = $this->shift->calculateLateMinutes($checkInTime);
            $this->status = $this->late_minutes > 0 ? 'Late' : 'Present';
        } else {
            $this->status = 'Present';
        }

        return $this->save();
    }

    public function checkOut($checkOutTime = null, $location = null, $photo = null)
    {
        $checkOutTime = $checkOutTime ?: now();
        
        $this->check_out_time = $checkOutTime;
        $this->check_out_location = $location;
        $this->check_out_photo = $photo;

        // Calculate work time and overtime
        if ($this->check_in_time && $this->shift) {
            $this->total_work_minutes = $this->check_in_time->diffInMinutes($checkOutTime);
            $this->overtime_minutes = $this->shift->calculateOvertimeMinutes($checkOutTime);
            $this->early_departure_minutes = $this->shift->isEarlyCheckOut($checkOutTime) 
                ? $this->shift->end_time->diffInMinutes($checkOutTime) 
                : 0;
        }

        return $this->save();
    }

    public function markAbsent($reason = null)
    {
        $this->status = 'Absent';
        $this->notes = $reason;
        $this->approval_status = 'Approved';
        return $this->save();
    }

    public function markOnLeave($leaveId = null)
    {
        $this->status = 'On Leave';
        $this->notes = 'Leave ID: ' . $leaveId;
        $this->approval_status = 'Approved';
        return $this->save();
    }

    public function approve($approvedBy = null)
    {
        $this->approval_status = 'Approved';
        $this->approved_by = $approvedBy ?: auth()->id();
        return $this->save();
    }

    public function reject($rejectionReason = null, $rejectedBy = null)
    {
        $this->approval_status = 'Rejected';
        $this->admin_notes = $rejectionReason;
        $this->approved_by = $rejectedBy ?: auth()->id();
        return $this->save();
    }

    public function isComplete()
    {
        return !is_null($this->check_in_time) && !is_null($this->check_out_time);
    }

    public function getWorkSummary()
    {
        return [
            'date' => $this->attendance_date->format('Y-m-d'),
            'check_in' => $this->formatted_check_in_time,
            'check_out' => $this->formatted_check_out_time,
            'status' => $this->status,
            'total_hours' => round($this->total_work_hours, 2),
            'overtime_hours' => round($this->overtime_hours, 2),
            'late_minutes' => $this->late_minutes,
            'is_complete' => $this->isComplete(),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attendance) {
            if (empty($attendance->attendance_date)) {
                $attendance->attendance_date = now()->toDateString();
            }
        });
    }
}
