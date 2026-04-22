<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $table = 'employee_files';

    protected $fillable = [
        'employee_id',
        'document_type',
        'document_type_kh',
        'document_name',
        'document_path',
        'original_filename',
        'mime_type',
        'file_size',
        'status',
        'issue_date',
        'expiry_date',
        'requires_renewal',
        'is_verified',
        'verified_by',
        'verified_at',
        'verification_notes',
        'uploaded_by',
        'description',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
        'requires_renewal' => 'boolean',
        'is_verified' => 'boolean',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'Expired');
    }

    public function scopeReplaced($query)
    {
        return $query->where('status', 'Replaced');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now())
                    ->where('status', 'Active');
    }

    public function scopeRequiresRenewal($query)
    {
        return $query->where('requires_renewal', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    // Accessors
    public function getDisplayDocumentTypeAttribute()
    {
        return app()->getLocale() === 'km' && $this->document_type_kh ? $this->document_type_kh : $this->document_type;
    }

    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFormattedIssueDateAttribute()
    {
        return $this->issue_date ? $this->issue_date->format('d/m/Y') : null;
    }

    public function getFormattedExpiryDateAttribute()
    {
        return $this->expiry_date ? $this->expiry_date->format('d/m/Y') : null;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) return null;
        
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getFileExtensionAttribute()
    {
        return pathinfo($this->document_name, PATHINFO_EXTENSION);
    }

    // Methods
    public function verify($verifiedBy = null, $notes = null)
    {
        $this->is_verified = true;
        $this->verified_by = $verifiedBy ?: auth()->id();
        $this->verified_at = now();
        $this->verification_notes = $notes;
        
        return $this->save();
    }

    public function unverify()
    {
        $this->is_verified = false;
        $this->verified_by = null;
        $this->verified_at = null;
        $this->verification_notes = null;
        
        return $this->save();
    }

    public function markAsExpired()
    {
        $this->status = 'Expired';
        return $this->save();
    }

    public function markAsReplaced($replacementDocumentId = null)
    {
        $this->status = 'Replaced';
        if ($replacementDocumentId) {
            $this->verification_notes = "Replaced by document ID: {$replacementDocumentId}";
        }
        return $this->save();
    }

    public function markAsActive()
    {
        $this->status = 'Active';
        return $this->save();
    }

    public function canBeVerified()
    {
        return !$this->is_verified && $this->status === 'Active';
    }

    public function needsRenewalReminder()
    {
        return $this->requires_renewal && 
               $this->status === 'Active' && 
               $this->days_until_expiry !== null && 
               $this->days_until_expiry <= 30 && 
               $this->days_until_expiry > 0;
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date && 
               $this->expiry_date->isAfter(now()) && 
               $this->expiry_date->lte(now()->addDays($days));
    }

    public function getDocumentUrl()
    {
        return asset($this->document_path);
    }

    public function getDownloadUrl()
    {
        return route('employee.documents.download', $this->id);
    }

    public function getPreviewUrl()
    {
        return route('employee.documents.preview', $this->id);
    }

    public function isImage()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        return in_array(strtolower($this->file_extension), $imageExtensions);
    }

    public function isPdf()
    {
        return strtolower($this->file_extension) === 'pdf';
    }

    public function isDocument()
    {
        $documentExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        return in_array(strtolower($this->file_extension), $documentExtensions);
    }

    public function getDocumentIcon()
    {
        if ($this->isImage()) return 'fa-image';
        if ($this->isPdf()) return 'fa-file-pdf';
        if ($this->isDocument()) return 'fa-file-word';
        
        return 'fa-file';
    }

    public function getValidationRules()
    {
        $rules = [
            'Contract' => 'required|max:10240', // 10MB
            'ID Card' => 'required|max:5120',   // 5MB
            'Passport' => 'required|max:5120', // 5MB
            'Medical Certificate' => 'max:5120', // 5MB
            'Experience Certificate' => 'max:5120', // 5MB
            'Education Certificate' => 'max:5120', // 5MB
            'Police Clearance' => 'max:5120', // 5MB
            'Other' => 'max:10240', // 10MB
        ];

        return $rules[$this->document_type] ?? 'max:10240';
    }

    public function getRequiredDocuments()
    {
        return [
            'Contract' => [
                'name' => 'Employment Contract',
                'name_kh' => 'Contract de Travail',
                'required' => true,
                'requires_renewal' => true,
                'expiry_required' => true,
            ],
            'ID Card' => [
                'name' => 'National ID Card',
                'name_kh' => 'Carte d\'Identité Nationale',
                'required' => true,
                'requires_renewal' => true,
                'expiry_required' => true,
            ],
            'Passport' => [
                'name' => 'Passport',
                'name_kh' => 'Passeport',
                'required' => false,
                'requires_renewal' => true,
                'expiry_required' => true,
            ],
            'Medical Certificate' => [
                'name' => 'Medical Certificate',
                'name_kh' => 'Certificat Médical',
                'required' => false,
                'requires_renewal' => true,
                'expiry_required' => true,
            ],
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (auth()->check()) {
                $document->uploaded_by = auth()->id();
            }
        });

        // Check for expiry and update status
        static::saving(function ($document) {
            if ($document->expiry_date && $document->status === 'Active') {
                if ($document->expiry_date->isPast()) {
                    $document->status = 'Expired';
                }
            }
        });
    }
}
