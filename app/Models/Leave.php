<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory;

    protected $table = 'employee_leaves';

    protected $fillable = [
        'employee_id',
        'leave_type',
        'reason',
        'reason_kh',
        'start_date',
        'end_date',
        'total_days',
        'status',
        'rejection_reason',
        'rejection_reason_kh',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'attachment',
        'notes',
        'paid_leave',
        'deducted_from_balance',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_leave' => 'boolean',
        'deducted_from_balance' => 'decimal:2',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                  $subQuery->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('leave_type', $type);
    }

    // Accessors
    public function getDisplayReasonAttribute()
    {
        return app()->getLocale() === 'km' && $this->reason_kh ? $this->reason_kh : $this->reason;
    }

    public function getDisplayRejectionReasonAttribute()
    {
        return app()->getLocale() === 'km' && $this->rejection_reason_kh ? $this->rejection_reason_kh : $this->rejection_reason;
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('d/m/Y');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->format('d/m/Y');
    }

    public function getDurationAttribute()
    {
        if ($this->start_date->eq($this->end_date)) {
            return $this->total_days . ' ' . __('day');
        }
        return $this->total_days . ' ' . __('days');
    }

    // Methods
    public function calculateTotalDays()
    {
        $totalDays = $this->start_date->diffInDays($this->end_date) + 1;
        
        // Subtract weekends if it's not sick leave
        if ($this->leave_type !== 'Sick') {
            $weekends = 0;
            $current = $this->start_date->copy();
            
            while ($current <= $this->end_date) {
                if ($current->isWeekend()) {
                    $weekends++;
                }
                $current->addDay();
            }
            
            $totalDays -= $weekends;
        }
        
        $this->total_days = $totalDays;
        return $this->total_days;
    }

    public function approve($approvedBy = null)
    {
        $this->status = 'Approved';
        $this->approved_by = $approvedBy ?: auth()->id();
        $this->approved_at = now();

        // Update leave balance if it's a paid leave
        if ($this->paid_leave) {
            $this->employee->updateLeaveBalance($this->leave_type, $this->total_days, 'subtract');
        }

        // Create attendance records for the leave period
        $this->createLeaveAttendanceRecords();

        return $this->save();
    }

    public function reject($rejectionReason = null, $rejectedBy = null)
    {
        $this->status = 'Rejected';
        $this->rejection_reason = $rejectionReason;
        $this->rejection_reason_kh = $rejectionReason; // You may want to translate this
        $this->rejected_by = $rejectedBy ?: auth()->id();
        $this->rejected_at = now();

        return $this->save();
    }

    public function cancel()
    {
        $this->status = 'Cancelled';

        // Restore leave balance if it was previously approved
        if ($this->paid_leave && $this->status === 'Approved') {
            $this->employee->updateLeaveBalance($this->leave_type, $this->total_days, 'add');
        }

        return $this->save();
    }

    private function createLeaveAttendanceRecords()
    {
        $current = $this->start_date->copy();
        
        while ($current <= $this->end_date) {
            // Skip weekends for non-sick leave
            if ($this->leave_type !== 'Sick' && $current->isWeekend()) {
                $current->addDay();
                continue;
            }

            // Check if attendance record already exists
            $existingAttendance = Attendance::where('employee_id', $this->employee_id)
                ->where('attendance_date', $current)
                ->first();

            if (!$existingAttendance) {
                Attendance::create([
                    'employee_id' => $this->employee_id,
                    'attendance_date' => $current,
                    'status' => 'On Leave',
                    'approval_status' => 'Approved',
                    'notes' => 'Leave ID: ' . $this->id . ' - ' . $this->leave_type,
                ]);
            } else {
                $existingAttendance->markOnLeave($this->id);
            }

            $current->addDay();
        }
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['Pending', 'Approved']) && 
               $this->start_date > now();
    }

    public function isOverlapping($startDate, $endDate)
    {
        return $this->start_date <= $endDate && $this->end_date >= $startDate;
    }

    public function hasValidLeaveBalance($employee)
    {
        if (!$this->paid_leave) {
            return true;
        }

        $balance = $employee->getCurrentLeaveBalance($this->leave_type);
        return $balance && $balance->remaining_days >= $this->total_days;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leave) {
            $leave->calculateTotalDays();
            
            // Set default values
            $leave->status = $leave->status ?? 'Pending';
            $leave->paid_leave = $leave->paid_leave ?? true;
            $leave->deducted_from_balance = $leave->deducted_from_balance ?? $leave->total_days;
        });

        static::updating(function ($leave) {
            if ($leave->isDirty(['start_date', 'end_date'])) {
                $leave->calculateTotalDays();
            }
        });
    }
}
