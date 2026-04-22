<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Employee;

class QRCodeService
{
    /**
     * Generate QR code for employee
     */
    public function generateEmployeeQR(Employee $employee): string
    {
        // QR data containing employee information
        $qrData = [
            'employee_id' => $employee->employee_id,
            'name' => $employee->full_name,
            'name_kh' => $employee->full_name_kh,
            'role' => $employee->role->name ?? null,
            'phone' => $employee->phone,
            'email' => $employee->email,
            'department' => $employee->role->name ?? 'General Staff',
            'generated_at' => now()->toISOString(),
            'company' => 'Coffee Shop POS',
            'version' => '1.0'
        ];

        // Convert to JSON string
        $qrString = json_encode($qrData);

        // Generate QR code image (using simple text-based approach for now)
        // In a real implementation, you would use a QR code library like "simplesoftwareio/simple-qrcode"
        $qrImagePath = $this->createQRImage($employee->employee_id, $qrString);

        return $qrImagePath;
    }

    /**
     * Create QR code image
     */
    private function createQRImage(string $employeeId, string $data): string
    {
        // For now, we'll create a placeholder QR code
        // In production, you should use a proper QR code library
        
        $qrDirectory = 'qrcodes';
        
        // Ensure directory exists
        if (!Storage::disk('public')->exists($qrDirectory)) {
            Storage::disk('public')->makeDirectory($qrDirectory);
        }

        $qrFileName = $employeeId . '.png';
        $qrPath = $qrDirectory . '/' . $qrFileName;

        // Create a simple placeholder QR code image
        $this->createPlaceholderQR($qrPath, $employeeId);

        return $qrPath;
    }

    /**
     * Create a placeholder QR code image
     */
    private function createPlaceholderQR(string $path, string $employeeId): void
    {
        // Create a simple 200x200 placeholder image
        $width = 200;
        $height = 200;
        
        // Create image resource
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 0, 100, 200);
        
        // Fill background
        imagefill($image, 0, 0, $white);
        
        // Draw a simple QR-like pattern
        $this->drawQRPattern($image, $black);
        
        // Add employee ID text
        $fontSize = 12;
        $text = $employeeId;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        $x = ($width - $textWidth) / 2;
        $y = $height - $textHeight - 10;
        
        imagestring($image, $fontSize, $x, $y, $text, $blue);
        
        // Save image
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        Storage::disk('public')->put($path, $imageData);
        
        // Free memory
        imagedestroy($image);
    }

    /**
     * Draw a simple QR-like pattern
     */
    private function drawQRPattern($image, $color): void
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $moduleSize = 8;
        
        // Generate a simple pattern based on the image dimensions
        for ($y = 0; $y < $height; $y += $moduleSize) {
            for ($x = 0; $x < $width; $x += $moduleSize) {
                // Create a pseudo-random pattern
                if (($x + $y) % ($moduleSize * 2) == 0) {
                    imagefilledrectangle($image, $x, $y, $x + $moduleSize - 1, $y + $moduleSize - 1, $color);
                }
            }
        }
        
        // Add corner squares (typical QR code feature)
        $this->drawCornerSquare($image, 10, 10, 60, $color);
        $this->drawCornerSquare($image, $width - 70, 10, 60, $color);
        $this->drawCornerSquare($image, 10, $height - 70, 60, $color);
    }

    /**
     * Draw a corner square pattern
     */
    private function drawCornerSquare($image, int $x, int $y, int $size, $color): void
    {
        // Outer square
        imagefilledrectangle($image, $x, $y, $x + $size - 1, $y + $size - 1, $color);
        
        // Inner white square
        $white = imagecolorallocate($image, 255, 255, 255);
        $innerSize = $size - 20;
        $innerOffset = 10;
        imagefilledrectangle($image, $x + $innerOffset, $y + $innerOffset, 
                           $x + $innerOffset + $innerSize - 1, $y + $innerOffset + $innerSize - 1, $white);
        
        // Center black square
        $centerSize = $innerSize - 20;
        $centerOffset = $innerOffset + 10;
        imagefilledrectangle($image, $x + $centerOffset, $y + $centerOffset, 
                           $x + $centerOffset + $centerSize - 1, $y + $centerOffset + $centerSize - 1, $color);
    }

    /**
     * Validate QR code data
     */
    public function validateQRData(string $qrData): array
    {
        try {
            $data = json_decode($qrData, true);
            
            if (!$data) {
                return ['valid' => false, 'error' => 'Invalid QR data format'];
            }
            
            // Check required fields
            $requiredFields = ['employee_id', 'name', 'generated_at'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return ['valid' => false, 'error' => "Missing required field: {$field}"];
                }
            }
            
            // Check if employee exists
            $employee = Employee::where('employee_id', $data['employee_id'])->first();
            if (!$employee) {
                return ['valid' => false, 'error' => 'Employee not found'];
            }
            
            return ['valid' => true, 'employee' => $employee, 'data' => $data];
            
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => 'Error validating QR data: ' . $e->getMessage()];
        }
    }

    /**
     * Generate QR code for attendance check-in
     */
    public function generateAttendanceQR(Employee $employee, string $type = 'checkin'): string
    {
        $qrData = [
            'type' => 'attendance',
            'action' => $type,
            'employee_id' => $employee->employee_id,
            'timestamp' => now()->timestamp,
            'shift_id' => $employee->shift_id,
            'location' => 'Coffee Shop',
            'session_id' => Str::uuid(),
            'expires_at' => now()->addMinutes(5)->timestamp
        ];

        $qrString = json_encode($qrData);
        $qrFileName = 'attendance_' . $type . '_' . $employee->employee_id . '_' . time() . '.png';
        $qrPath = 'qrcodes/temp/' . $qrFileName;

        // Ensure temp directory exists
        if (!Storage::disk('public')->exists('qrcodes/temp')) {
            Storage::disk('public')->makeDirectory('qrcodes/temp');
        }

        $this->createPlaceholderQR($qrPath, $employee->employee_id . ' - ' . $type);

        return $qrPath;
    }

    /**
     * Clean up temporary QR codes
     */
    public function cleanupTempQRs(): void
    {
        $tempDirectory = 'qrcodes/temp';
        
        if (Storage::disk('public')->exists($tempDirectory)) {
            $files = Storage::disk('public')->files($tempDirectory);
            
            foreach ($files as $file) {
                // Delete files older than 1 hour
                $lastModified = Storage::disk('public')->lastModified($file);
                if ($lastModified < now()->subHour()->timestamp) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }

    /**
     * Get QR code URL
     */
    public function getQRCodeUrl(string $path): string
    {
        return asset('storage/' . $path);
    }

    /**
     * Download QR code
     */
    public function downloadQRCode(string $path): string
    {
        if (!Storage::disk('public')->exists($path)) {
            throw new \Exception('QR code not found');
        }

        return Storage::disk('public')->path($path);
    }

    /**
     * Batch generate QR codes for multiple employees
     */
    public function batchGenerateQRCodes(array $employeeIds): array
    {
        $results = [];
        
        foreach ($employeeIds as $employeeId) {
            try {
                $employee = Employee::findOrFail($employeeId);
                $qrPath = $this->generateEmployeeQR($employee);
                $employee->qr_code_path = $qrPath;
                $employee->has_qr_code = true;
                $employee->save();
                
                $results[$employeeId] = [
                    'success' => true,
                    'path' => $qrPath,
                    'url' => $this->getQRCodeUrl($qrPath)
                ];
            } catch (\Exception $e) {
                $results[$employeeId] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}
