<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'employee_leave_balances';

    protected $fillable = [
        'employee_id',
        'leave_type',
        'total_days',
        'used_days',
        'remaining_days',
        'pending_days',
        'year',
        'reset_date',
    ];

    protected $casts = [
        'total_days' => 'decimal:2',
        'used_days' => 'decimal:2',
        'remaining_days' => 'decimal:2',
        'pending_days' => 'decimal:2',
        'reset_date' => 'date',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('leave_type', $type);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeExpiringSoon($query, $months = 3)
    {
        return $query->whereNotNull('reset_date')
                    ->where('reset_date', '<=', now()->addMonths($months))
                    ->where('reset_date', '>', now());
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->leave_type . ' (' . $this->year . ')';
    }

    public function getFormattedTotalDaysAttribute()
    {
        return number_format($this->total_days, 1);
    }

    public function getFormattedUsedDaysAttribute()
    {
        return number_format($this->used_days, 1);
    }

    public function getFormattedRemainingDaysAttribute()
    {
        return number_format($this->remaining_days, 1);
    }

    public function getFormattedPendingDaysAttribute()
    {
        return number_format($this->pending_days, 1);
    }

    public function getUsagePercentageAttribute()
    {
        if ($this->total_days == 0) return 0;
        return round(($this->used_days / $this->total_days) * 100, 2);
    }

    public function getAvailableDaysAttribute()
    {
        return max(0, $this->remaining_days - $this->pending_days);
    }

    // Methods
    public function useDays($days)
    {
        if ($days > $this->remaining_days) {
            return false; // Insufficient balance
        }

        $this->used_days += $days;
        $this->remaining_days = max(0, $this->total_days - $this->used_days);
        
        return $this->save();
    }

    public function restoreDays($days)
    {
        $this->used_days = max(0, $this->used_days - $days);
        $this->remaining_days = min($this->total_days, $this->total_days - $this->used_days);
        
        return $this->save();
    }

    public function addPendingDays($days)
    {
        $this->pending_days += $days;
        return $this->save();
    }

    public function removePendingDays($days)
    {
        $this->pending_days = max(0, $this->pending_days - $days);
        return $this->save();
    }

    public function approvePendingDays($days)
    {
        if ($this->useDays($days)) {
            $this->removePendingDays($days);
            return true;
        }
        return false;
    }

    public function rejectPendingDays($days)
    {
        $this->removePendingDays($days);
        return $this->save();
    }

    public function resetBalance($newTotalDays = null)
    {
        $this->used_days = 0;
        $this->remaining_days = $newTotalDays ?? $this->total_days;
        $this->pending_days = 0;
        $this->reset_date = now()->addYear();
        
        return $this->save();
    }

    public function canUseDays($days)
    {
        return $this->available_days >= $days;
    }

    public function getLeaveTypeInfo()
    {
        $leaveTypes = [
            'Annual' => [
                'name' => 'Annual Leave',
                'name_kh' => 'Congé Annuel',
                'default_days' => 18,
                'paid' => true,
                'accumulates' => true,
                'max_accumulation' => 36,
                'reset_month' => 1, // January
            ],
            'Sick' => [
                'name' => 'Sick Leave',
                'name_kh' => 'Congé Maladie',
                'default_days' => 10,
                'paid' => true,
                'accumulates' => false,
                'max_accumulation' => 0,
                'reset_month' => 1,
            ],
            'Personal' => [
                'name' => 'Personal Leave',
                'name_kh' => 'Congé Personnel',
                'default_days' => 5,
                'paid' => false,
                'accumulates' => false,
                'max_accumulation' => 0,
                'reset_month' => 1,
            ],
            'Maternity' => [
                'name' => 'Maternity Leave',
                'name_kh' => 'Congé Maternité',
                'default_days' => 90,
                'paid' => true,
                'accumulates' => false,
                'max_accumulation' => 0,
                'reset_month' => null, // One-time
            ],
            'Paternity' => [
                'name' => 'Paternity Leave',
                'name_kh' => 'Congé Paternité',
                'default_days' => 14,
                'paid' => true,
                'accumulates' => false,
                'max_accumulation' => 0,
                'reset_month' => null, // One-time
            ],
        ];

        return $leaveTypes[$this->leave_type] ?? [
            'name' => $this->leave_type,
            'name_kh' => $this->leave_type,
            'default_days' => 0,
            'paid' => true,
            'accumulates' => false,
            'max_accumulation' => 0,
            'reset_month' => 1,
        ];
    }

    public function getDisplayName()
    {
        $info = $this->getLeaveTypeInfo();
        return app()->getLocale() === 'km' ? $info['name_kh'] : $info['name'];
    }

    public function isPaid()
    {
        return $this->getLeaveTypeInfo()['paid'];
    }

    public function accumulates()
    {
        return $this->getLeaveTypeInfo()['accumulates'];
    }

    public function getMaxAccumulation()
    {
        return $this->getLeaveTypeInfo()['max_accumulation'];
    }

    public function getResetMonth()
    {
        return $this->getLeaveTypeInfo()['reset_month'];
    }

    public function shouldReset()
    {
        $resetMonth = $this->getResetMonth();
        if (!$resetMonth) return false;

        $resetDate = $this->reset_date ?? now()->month($resetMonth)->day(1);
        return now()->greaterThanOrEqualTo($resetDate);
    }

    public function handleReset()
    {
        if (!$this->shouldReset()) {
            return false;
        }

        $info = $this->getLeaveTypeInfo();
        
        if ($this->accumulates()) {
            // Accumulate unused days up to max
            $accumulatedDays = min($this->remaining_days, $this->getMaxAccumulation());
            $this->total_days = $info['default_days'] + $accumulatedDays;
        } else {
            // Reset to default
            $this->total_days = $info['default_days'];
        }

        $this->used_days = 0;
        $this->remaining_days = $this->total_days;
        $this->pending_days = 0;
        $this->reset_date = now()->month($this->getResetMonth())->day(1)->addYear();
        
        return $this->save();
    }

    public static function initializeBalances($employee, $year = null)
    {
        $year = $year ?: now()->year;
        $leaveTypes = ['Annual', 'Sick', 'Personal'];

        foreach ($leaveTypes as $type) {
            $existing = LeaveBalance::where('employee_id', $employee->id)
                ->where('leave_type', $type)
                ->where('year', $year)
                ->first();

            if (!$existing) {
                $balance = new self();
                $balance->employee_id = $employee->id;
                $balance->leave_type = $type;
                $balance->year = $year;
                
                $info = $balance->getLeaveTypeInfo();
                $balance->total_days = $info['default_days'];
                $balance->used_days = 0;
                $balance->remaining_days = $info['default_days'];
                $balance->pending_days = 0;
                
                if ($info['reset_month']) {
                    $balance->reset_date = now()->year($year)->month($info['reset_month'])->day(1)->addYear();
                }
                
                $balance->save();
            }
        }
    }

    public static function getEmployeeBalances($employeeId, $year = null)
    {
        $year = $year ?: now()->year;
        
        return self::where('employee_id', $employeeId)
            ->where('year', $year)
            ->orderBy('leave_type')
            ->get();
    }

    public static function checkAllBalancesForReset()
    {
        return self::where('reset_date', '<=', now())
            ->get()
            ->each(function ($balance) {
                $balance->handleReset();
            });
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($balance) {
            // Ensure remaining_days is calculated correctly
            $balance->remaining_days = max(0, $balance->total_days - $balance->used_days);
        });
    }
}
