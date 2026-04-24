<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\WorkLocation;

class QRCodeService
{
    const TYPE_ID_CARD = 'id_card';
    const TYPE_ATTENDANCE = 'attendance';

    public function generateBothQRCodes(Employee $employee): array
    {
        return [
            'id_card' => $this->generateIdCardQR($employee),
            'attendance' => $this->generateAttendanceQR($employee)
        ];
    }

    public function generateIdCardQR(Employee $employee): string
    {
        $qrData = [
            'type' => self::TYPE_ID_CARD,
            'version' => '2.0',
            'generated_at' => now()->toISOString(),
            'employee' => [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'full_name' => $employee->full_name,
                'full_name_kh' => $employee->full_name_kh,
                'role' => $employee->role?->name ?? 'Staff',
                'phone' => $employee->phone,
                'email' => $employee->email,
                'status' => $employee->status
            ],
            'security' => [
                'hash' => hash('sha256', $employee->employee_id . env('APP_KEY')),
                'expires' => null
            ]
        ];

        $qrString = json_encode($qrData, JSON_UNESCAPED_UNICODE);
        $qrFileName = 'id_card_' . $employee->employee_id . '.png';
        $qrPath = 'qrcodes/id_cards/' . $qrFileName;
        
        $this->ensureDirectoryExists('qrcodes/id_cards');
        $this->createQRImageWithData($qrPath, $qrString, $employee->employee_id, 'ID CARD');

        $employee->id_card_qr_path = $qrPath;
        $employee->id_card_qr_generated_at = now();
        $employee->save();

        return $qrPath;
    }

    public function generateAttendanceQR(Employee $employee, ?WorkLocation $location = null, string $action = 'check_in'): string
    {
        if (!$location) {
            $location = $this->getDefaultLocationForEmployee($employee);
        }

        $sessionId = Str::uuid()->toString();
        $expiresAt = now()->addMinutes(5);

        $qrData = [
            'type' => self::TYPE_ATTENDANCE,
            'version' => '2.0',
            'action' => $action,
            'employee' => [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'full_name' => $employee->full_name
            ],
            'location' => $location ? [
                'id' => $location->id,
                'code' => $location->location_code,
                'name' => $location->name
            ] : null,
            'security' => [
                'session_id' => $sessionId,
                'expires_at' => $expiresAt->toISOString(),
                'expires_timestamp' => $expiresAt->timestamp
            ]
        ];

        $qrString = json_encode($qrData, JSON_UNESCAPED_UNICODE);
        $qrFileName = 'attendance_' . $action . '_' . $employee->employee_id . '.png';
        $qrPath = 'qrcodes/attendance/' . $qrFileName;
        
        $this->ensureDirectoryExists('qrcodes/attendance');
        $label = $action === 'check_in' ? 'CHECK IN' : 'CHECK OUT';
        $this->createQRImageWithData($qrPath, $qrString, $employee->employee_id, $label);

        $employee->attendance_qr_path = $qrPath;
        $employee->attendance_qr_generated_at = now();
        $employee->attendance_qr_expires_at = $expiresAt;
        $employee->save();

        return $qrPath;
    }

    private function getDefaultLocationForEmployee(Employee $employee): ?WorkLocation
    {
        if ($employee->default_location_id) {
            $location = WorkLocation::find($employee->default_location_id);
            if ($location && $location->status === 'active') {
                return $location;
            }
        }
        
        $primaryLocation = \DB::table('employee_locations')
            ->where('employee_id', $employee->id)
            ->where('assignment_type', 'primary')
            ->where('status', 'active')
            ->first();

        if ($primaryLocation) {
            return WorkLocation::find($primaryLocation->location_id);
        }

        return WorkLocation::where('is_default', true)->where('status', 'active')->first();
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory, 0755, true);
        }
    }

    private function createQRImageWithData(string $path, string $data, string $employeeId, string $label): void
    {
        $size = 300;
        $image = imagecreatetruecolor($size, $size + 40);
        
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 0, 100, 200);
        
        imagefill($image, 0, 0, $white);
        $this->drawQRPattern($image, $black, $size);
        $this->addLabelText($image, $employeeId, $label, $blue, $black, $size);
        
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        Storage::disk('public')->put($path, $imageData);
        imagedestroy($image);
    }

    private function drawQRPattern($image, $color, int $size): void
    {
        $padding = 40;
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, $padding, $padding, $size - $padding, $size - $padding, $white);
        
        for ($y = $padding; $y < $size - $padding; $y += 10) {
            for ($x = $padding; $x < $size - $padding; $x += 10) {
                if (rand(0, 100) > 50) {
                    imagefilledrectangle($image, $x, $y, $x + 8, $y + 8, $color);
                }
            }
        }
        
        $this->drawFinderPattern($image, $padding, $padding, 50, $color);
        $this->drawFinderPattern($image, $size - $padding - 50, $padding, 50, $color);
        $this->drawFinderPattern($image, $padding, $size - $padding - 50, 50, $color);
    }

    private function drawFinderPattern($image, int $x, int $y, int $size, $color): void
    {
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, $x, $y, $x + $size, $y + $size, $color);
        $innerOffset = $size / 5;
        imagefilledrectangle($image, $x + $innerOffset, $y + $innerOffset, $x + $size - $innerOffset, $y + $size - $innerOffset, $white);
        $centerOffset = $size * 2 / 5;
        $centerSize = $size / 5;
        imagefilledrectangle($image, $x + $centerOffset, $y + $centerOffset, $x + $centerOffset + $centerSize, $y + $centerOffset + $centerSize, $color);
    }

    private function addLabelText($image, string $employeeId, string $label, $primaryColor, $textColor, int $size): void
    {
        $labelStartY = $size - 40;
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, $labelStartY, $size, $size + 40, $white);
        
        $font = 3;
        $textWidth = imagefontwidth($font) * strlen($employeeId);
        $x = ($size - $textWidth) / 2;
        imagestring($image, $font, $x, $labelStartY + 8, $employeeId, $primaryColor);
        
        $labelWidth = imagefontwidth(2) * strlen($label);
        $labelX = ($size - $labelWidth) / 2;
        imagestring($image, 2, $labelX, $labelStartY + 25, $label, $textColor);
    }

    public function validateIdCardQR(string $qrData): array
    {
        try {
            $data = json_decode($qrData, true);
            if (!$data || ($data['type'] ?? '') !== self::TYPE_ID_CARD) {
                return ['valid' => false, 'error' => 'Invalid QR type'];
            }
            $employee = Employee::where('employee_id', $data['employee']['employee_id'] ?? '')->first();
            if (!$employee) {
                return ['valid' => false, 'error' => 'Employee not found'];
            }
            return ['valid' => true, 'type' => self::TYPE_ID_CARD, 'employee' => $employee, 'data' => $data['employee']];
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    public function validateAttendanceQR(string $qrData): array
    {
        try {
            $data = json_decode($qrData, true);
            if (!$data || ($data['type'] ?? '') !== self::TYPE_ATTENDANCE) {
                return ['valid' => false, 'error' => 'Invalid QR type'];
            }
            
            $expiresAt = $data['security']['expires_timestamp'] ?? 0;
            if (now()->timestamp > $expiresAt) {
                return ['valid' => false, 'error' => 'QR code has expired'];
            }
            
            $employee = Employee::where('employee_id', $data['employee']['employee_id'] ?? '')->first();
            if (!$employee) {
                return ['valid' => false, 'error' => 'Employee not found'];
            }
            
            return ['valid' => true, 'type' => self::TYPE_ATTENDANCE, 'employee' => $employee, 'data' => $data, 'action' => $data['action']];
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    public function getQRCodeUrl(string $path): string
    {
        return asset('storage/' . $path);
    }

    public function cleanupOldQRCodes(): void
    {
        $directories = ['qrcodes/attendance', 'qrcodes/temp'];
        foreach ($directories as $dir) {
            if (Storage::disk('public')->exists($dir)) {
                $files = Storage::disk('public')->files($dir);
                foreach ($files as $file) {
                    if (Storage::disk('public')->lastModified($file) < now()->subHour()->timestamp) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        }
    }
}
