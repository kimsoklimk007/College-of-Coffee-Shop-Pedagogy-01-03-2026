<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'employee_activity_logs';

    protected $fillable = [
        'user_id',
        'employee_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'description_kh',
        'target_type',
        'target_id',
        'target_name',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'device_info',
        'location',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Accessors
    public function getDisplayDescriptionAttribute()
    {
        return app()->getLocale() === 'km' && $this->description_kh ? $this->description_kh : $this->description;
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getActionBadgeAttribute()
    {
        $badges = [
            'create' => 'bg-success',
            'update' => 'bg-warning',
            'delete' => 'bg-danger',
            'login' => 'bg-info',
            'logout' => 'bg-secondary',
            'approve' => 'bg-primary',
            'reject' => 'bg-danger',
            'export' => 'bg-dark',
            'import' => 'bg-dark',
        ];

        return $badges[$this->action] ?? 'bg-secondary';
    }

    public function getActionIconAttribute()
    {
        $icons = [
            'create' => 'fa-plus',
            'update' => 'fa-edit',
            'delete' => 'fa-trash',
            'login' => 'fa-sign-in-alt',
            'logout' => 'fa-sign-out-alt',
            'approve' => 'fa-check',
            'reject' => 'fa-times',
            'export' => 'fa-download',
            'import' => 'fa-upload',
        ];

        return $icons[$this->action] ?? 'fa-circle';
    }

    // Methods
    public static function log($action, $module, $description, $target = null, $oldValues = null, $newValues = null, $employeeId = null)
    {
        $log = [
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'description_kh' => $description, // You may want to translate this
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_info' => self::getDeviceInfo(),
            'location' => self::getLocation(),
        ];

        if (auth()->check()) {
            $user = auth()->user();
            $log['user_id'] = $user->id;
            $log['user_name'] = $user->name;
            $log['user_role'] = $user->role;
        }

        if ($employeeId) {
            $log['employee_id'] = $employeeId;
        }

        if ($target) {
            $log['target_type'] = get_class($target);
            $log['target_id'] = $target->id;
            $log['target_name'] = method_exists($target, 'getDisplayName') 
                ? $target->getDisplayName() 
                : (method_exists($target, 'name') ? $target->name : "ID: {$target->id}");
        }

        return self::create($log);
    }

    public static function logEmployeeAction($action, $description, $employee, $oldValues = null, $newValues = null)
    {
        return self::log($action, 'employee', $description, $employee, $oldValues, $newValues, $employee->id);
    }

    public static function logAttendanceAction($action, $description, $attendance, $oldValues = null, $newValues = null)
    {
        return self::log($action, 'attendance', $description, $attendance, $oldValues, $newValues, $attendance->employee_id);
    }

    public static function logLeaveAction($action, $description, $leave, $oldValues = null, $newValues = null)
    {
        return self::log($action, 'leave', $description, $leave, $oldValues, $newValues, $leave->employee_id);
    }

    public static function logPayrollAction($action, $description, $payroll, $oldValues = null, $newValues = null)
    {
        return self::log($action, 'payroll', $description, $payroll, $oldValues, $newValues, $payroll->employee_id);
    }

    public static function logDocumentAction($action, $description, $document, $oldValues = null, $newValues = null)
    {
        return self::log($action, 'document', $description, $document, $oldValues, $newValues, $document->employee_id);
    }

    public static function logLogin()
    {
        if (auth()->check()) {
            $user = auth()->user();
            return self::log('login', 'auth', "User {$user->name} logged in", null, null, null);
        }
    }

    public static function logLogout()
    {
        if (auth()->check()) {
            $user = auth()->user();
            return self::log('logout', 'auth', "User {$user->name} logged out", null, null, null);
        }
    }

    private static function getDeviceInfo()
    {
        $userAgent = request()->userAgent();
        
        // Simple device detection
        $device = 'Unknown';
        if (strpos($userAgent, 'Mobile') !== false) {
            $device = 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            $device = 'Tablet';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $device = 'Windows PC';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $device = 'Mac';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $device = 'Linux';
        }

        return $device;
    }

    private static function getLocation()
    {
        // You can integrate with a geolocation service here
        // For now, return null
        return null;
    }

    public function getChangesSummary()
    {
        if (!$this->old_values && !$this->new_values) {
            return 'No changes recorded';
        }

        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'from' => $oldValue,
                        'to' => $newValue,
                    ];
                }
            }
        }

        return empty($changes) ? 'No changes recorded' : $changes;
    }

    public function getFormattedChanges()
    {
        $changes = $this->getChangesSummary();
        
        if (!is_array($changes)) {
            return $changes;
        }

        $formatted = [];
        foreach ($changes as $field => $change) {
            $formatted[] = "{$field}: {$change['from']} -> {$change['to']}";
        }

        return implode(', ', $formatted);
    }

    public function canBeViewedBy($user)
    {
        // Admin can view all logs
        if ($user->role === 'admin') {
            return true;
        }

        // Users can only view their own logs
        if ($this->user_id === $user->id) {
            return true;
        }

        // Managers can view logs of employees they manage
        if ($user->role === 'manager') {
            // You would need to implement manager-employee relationship
            return false;
        }

        return false;
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-log user login/logout
        if (auth()->check()) {
            // This would be better handled in middleware or event listeners
        }
    }
}
