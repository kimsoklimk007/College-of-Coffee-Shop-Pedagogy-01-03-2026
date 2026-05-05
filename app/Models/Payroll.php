<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'employee_payroll';

    protected $fillable = [
        'employee_id',
        'payroll_period',
        'period_start',
        'period_end',
        'payment_date',
        'base_salary',
        'daily_rate',
        'hourly_rate',
        'working_days',
        'present_days',
        'late_days',
        'absent_days',
        'leave_days',
        'holidays',
        'basic_earnings',
        'overtime_hours',
        'overtime_rate',
        'overtime_earnings',
        'holiday_earnings',
        'bonus',
        'allowances',
        'total_earnings',
        'absent_deductions',
        'late_deductions',
        'tax_deductions',
        'social_security',
        'other_deductions',
        'total_deductions',
        'net_salary',
        'currency',
        'status',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'payment_method',
        'transaction_reference',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'payment_date' => 'date',
        'base_salary' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'working_days' => 'integer',
        'present_days' => 'integer',
        'late_days' => 'integer',
        'absent_days' => 'integer',
        'leave_days' => 'integer',
        'holidays' => 'integer',
        'basic_earnings' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'overtime_earnings' => 'decimal:2',
        'holiday_earnings' => 'decimal:2',
        'bonus' => 'decimal:2',
        'allowances' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'absent_deductions' => 'decimal:2',
        'late_deductions' => 'decimal:2',
        'tax_deductions' => 'decimal:2',
        'social_security' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
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

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('payroll_period', $period);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate]);
    }

    // Accessors
    public function getFormattedPeriodAttribute()
    {
        return $this->period_start->format('M Y');
    }

    public function getFormattedNetSalaryAttribute()
    {
        return number_format($this->net_salary, 2);
    }

    public function getAttendanceRateAttribute()
    {
        if ($this->working_days == 0) return 0;
        return round(($this->present_days / $this->working_days) * 100, 2);
    }

    // Methods
    public function calculatePayroll()
    {
        $this->calculateRates();
        $this->calculateAttendanceSummary();
        $this->calculateEarnings();
        $this->calculateDeductions();
        $this->calculateNetSalary();
        
        return $this;
    }

    private function calculateRates()
    {
        // Daily rate: Base salary / 22 working days (Cambodia standard)
        $this->daily_rate = $this->base_salary / 22;
        
        // Hourly rate: Daily rate / 8 working hours
        $this->hourly_rate = $this->daily_rate / 8;
    }

    private function calculateAttendanceSummary()
    {
        $attendance = $this->employee->attendance()
            ->whereBetween('attendance_date', [$this->period_start, $this->period_end])
            ->get();

        $this->present_days = $attendance->whereIn('status', ['Present', 'Late'])->count();
        $this->late_days = $attendance->where('status', 'Late')->count();
        $this->absent_days = $attendance->where('status', 'Absent')->count();
        $this->leave_days = $attendance->where('status', 'On Leave')->count();
        
        // Calculate working days (excluding weekends)
        $this->working_days = 0;
        $current = $this->period_start->copy();
        while ($current <= $this->period_end) {
            if (!$current->isWeekend()) {
                $this->working_days++;
            }
            $current->addDay();
        }
    }

    private function calculateEarnings()
    {
        // Basic earnings based on present days
        $this->basic_earnings = $this->daily_rate * $this->present_days;
        
        // Overtime earnings
        $totalOvertimeMinutes = $this->employee->attendance()
            ->whereBetween('attendance_date', [$this->period_start, $this->period_end])
            ->sum('overtime_minutes');
        
        $this->overtime_hours = $totalOvertimeMinutes / 60;
        $this->overtime_rate = $this->employee->shift->overtime_rate ?? 1.5;
        $this->overtime_earnings = ($this->hourly_rate * $this->overtime_hours) * $this->overtime_rate;
        
        // Holiday earnings (if any holidays worked)
        $this->holiday_earnings = 0; // Calculate based on holiday attendance
        
        // Total earnings
        $this->total_earnings = $this->basic_earnings + 
                               $this->overtime_earnings + 
                               $this->holiday_earnings + 
                               $this->bonus + 
                               $this->allowances;
    }

    private function calculateDeductions()
    {
        // Absent deductions
        $this->absent_deductions = $this->daily_rate * $this->absent_days;
        
        // Late deductions (15 minutes grace period, then deduct hourly rate)
        $totalLateMinutes = $this->employee->attendance()
            ->whereBetween('attendance_date', [$this->period_start, $this->period_end])
            ->sum('late_minutes');
        
        $lateDeductibleMinutes = max(0, $totalLateMinutes - ($this->late_days * 15));
        $this->late_deductions = ($this->hourly_rate / 60) * $lateDeductibleMinutes;
        
        // Tax deductions (Cambodia tax calculation)
        $this->tax_deductions = $this->calculateTax();
        
        // Social security (1.5% employee contribution in Cambodia)
        $this->social_security = $this->total_earnings * 0.015;
        
        // Total deductions
        $this->total_deductions = $this->absent_deductions + 
                                 $this->late_deductions + 
                                 $this->tax_deductions + 
                                 $this->social_security + 
                                 $this->other_deductions;
    }

    private function calculateTax()
    {
        // Cambodia tax brackets (2024 rates)
        $taxableIncome = $this->total_earnings - 120000; // 120,000 KHR exemption
        
        if ($taxableIncome <= 0) return 0;
        
        // Convert to KHR for tax calculation
        $taxableKHR = $taxableIncome * 4100; // Approximate exchange rate
        
        $tax = 0;
        
        if ($taxableKHR <= 1300000) {
            $tax = $taxableKHR * 0.05;
        } elseif ($taxableKHR <= 2100000) {
            $tax = 65000 + ($taxableKHR - 1300000) * 0.10;
        } elseif ($taxableKHR <= 3100000) {
            $tax = 145000 + ($taxableKHR - 2100000) * 0.15;
        } elseif ($taxableKHR <= 4100000) {
            $tax = 295000 + ($taxableKHR - 3100000) * 0.20;
        } elseif ($taxableKHR <= 5100000) {
            $tax = 495000 + ($taxableKHR - 4100000) * 0.25;
        } else {
            $tax = 745000 + ($taxableKHR - 5100000) * 0.30;
        }
        
        // Convert back to USD
        return $tax / 4100;
    }

    private function calculateNetSalary()
    {
        $this->net_salary = $this->total_earnings - $this->total_deductions;
    }

    public function approve($approvedBy = null)
    {
        $this->status = 'Approved';
        $this->approved_by = $approvedBy ?: auth()->id();
        $this->approved_at = now();
        return $this->save();
    }

    public function markAsPaid($paidBy = null, $transactionReference = null)
    {
        $this->status = 'Paid';
        $this->paid_by = $paidBy ?: auth()->id();
        $this->paid_at = now();
        $this->payment_date = now();
        $this->transaction_reference = $transactionReference;
        return $this->save();
    }

    public function generatePayrollSlip()
    {
        return [
            'employee_name' => $this->employee->full_name,
            'employee_id' => $this->employee->employee_id,
            'position' => $this->employee->role->name ?? 'N/A',
            'period' => $this->formatted_period,
            'payment_date' => $this->payment_date?->format('d/m/Y'),
            'earnings' => [
                'basic' => $this->basic_earnings,
                'overtime' => $this->overtime_earnings,
                'holiday' => $this->holiday_earnings,
                'bonus' => $this->bonus,
                'allowances' => $this->allowances,
                'total' => $this->total_earnings,
            ],
            'deductions' => [
                'absent' => $this->absent_deductions,
                'late' => $this->late_deductions,
                'tax' => $this->tax_deductions,
                'social_security' => $this->social_security,
                'other' => $this->other_deductions,
                'total' => $this->total_deductions,
            ],
            'net_salary' => $this->net_salary,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'transaction_reference' => $this->transaction_reference,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payroll) {
            if (empty($payroll->payroll_period)) {
                $payroll->payroll_period = $payroll->period_start->format('Y-m');
            }
            if (empty($payroll->currency)) {
                $payroll->currency = 'USD';
            }
            if (empty($payroll->status)) {
                $payroll->status = 'Draft';
            }
        });
    }
}
