<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_kh',
        'start_time',
        'end_time',
        'duration_minutes',
        'overtime_rate',
        'is_active',
        'late_threshold',
        'description',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    protected $attributes = [
        'is_active' => true,
        'duration_minutes' => 480, // Default 8 hours
        'overtime_rate' => 1.5,
        'late_threshold' => 15, // Default 15 minutes
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return __($this->name);
    }

    public function getStatusAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time->format('H:i');
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time->format('H:i');
    }

    public function getDurationHoursAttribute()
    {
        return $this->duration_minutes / 60;
    }

    // Methods
    public function isLateCheckIn($checkInTime)
    {
        $gracePeriod = 15; // 15 minutes grace period
        return $checkInTime > $this->start_time->addMinutes($gracePeriod);
    }

    public function isEarlyCheckOut($checkOutTime)
    {
        $gracePeriod = 15; // 15 minutes grace period
        return $checkOutTime < $this->end_time->subMinutes($gracePeriod);
    }

    public function calculateOvertimeMinutes($checkOutTime)
    {
        if ($checkOutTime > $this->end_time) {
            return $checkOutTime->diffInMinutes($this->end_time);
        }
        return 0;
    }

    public function calculateLateMinutes($checkInTime)
    {
        $gracePeriod = 15; // 15 minutes grace period
        $startTimeWithGrace = $this->start_time->addMinutes($gracePeriod);
        if ($checkInTime > $startTimeWithGrace) {
            return $checkInTime->diffInMinutes($startTimeWithGrace);
        }
        return 0;
    }
}
