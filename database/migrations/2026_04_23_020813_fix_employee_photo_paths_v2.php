<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $disk = Storage::disk('public');

        // Get all employees with profile photos
        $employees = Employee::whereNotNull('profile_photo')
            ->orWhereNotNull('id_card_photo')
            ->orWhereNotNull('contract_document')
            ->get();

        foreach ($employees as $employee) {
            $needsUpdate = false;
            $updates = [];

            // Fix profile_photo
            if ($employee->profile_photo) {
                $newPath = $this->fixPath($employee->profile_photo, $disk);
                if ($newPath !== $employee->profile_photo) {
                    $updates['profile_photo'] = $newPath;
                    $needsUpdate = true;
                }
            }

            // Fix id_card_photo
            if ($employee->id_card_photo) {
                $newPath = $this->fixPath($employee->id_card_photo, $disk);
                if ($newPath !== $employee->id_card_photo) {
                    $updates['id_card_photo'] = $newPath;
                    $needsUpdate = true;
                }
            }

            // Fix contract_document
            if ($employee->contract_document) {
                $newPath = $this->fixPath($employee->contract_document, $disk);
                if ($newPath !== $employee->contract_document) {
                    $updates['contract_document'] = $newPath;
                    $needsUpdate = true;
                }
            }

            // Update the employee record if needed
            if ($needsUpdate) {
                $employee->update($updates);
                echo "Updated employee {$employee->id}: {$employee->full_name}\n";
            }
        }
    }

    /**
     * Fix a single path
     */
    private function fixPath(string $oldPath, $disk): string
    {
        // Path mappings for normalization
        $pathMappings = [
            // Handle "file Staff picture" issue
            'file Staff picture/' => 'employee/staff_pictures/',
            'file Special documents/' => 'employee/special_documents/',
            'file Special documents 1/' => 'employee/special_documents/',
            // Handle old paths
            'employee/Staff picture/' => 'employee/staff_pictures/',
            'employee/Special documents/' => 'employee/special_documents/',
            'employee/Special documents 1/' => 'employee/special_documents/',
        ];

        $newPath = $oldPath;

        // Apply path mappings
        foreach ($pathMappings as $old => $new) {
            if (str_starts_with($oldPath, $old)) {
                $newPath = str_replace($old, $new, $oldPath);
                break;
            }
        }

        // If path doesn't start with employee/, add it
        if (!str_starts_with($newPath, 'employee/')) {
            // Try to determine the type from the old path
            if (str_contains($newPath, 'Staff') || str_contains($newPath, 'picture') || str_contains($newPath, 'profile')) {
                $filename = basename($newPath);
                $newPath = 'employee/staff_pictures/' . $filename;
            } elseif (str_contains($newPath, 'Special') || str_contains($newPath, 'id_card')) {
                $filename = basename($newPath);
                $newPath = 'employee/special_documents/' . $filename;
            } elseif (str_contains($newPath, 'contract')) {
                $filename = basename($newPath);
                $newPath = 'employee/special_documents/' . $filename;
            } else {
                // Default to staff_pictures
                $filename = basename($newPath);
                $newPath = 'employee/staff_pictures/' . $filename;
            }
        }

        // Move the file if it exists at the old location
        if ($oldPath !== $newPath) {
            $this->moveFile($oldPath, $newPath, $disk);
        }

        return $newPath;
    }

    /**
     * Move file from old path to new path
     */
    private function moveFile(string $oldPath, string $newPath, $disk): void
    {
        // Check if old file exists
        if ($disk->exists($oldPath)) {
            // Create directory if needed
            $directory = dirname($newPath);
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            // Check if new path already exists (don't overwrite)
            if (!$disk->exists($newPath)) {
                try {
                    // Read old file content
                    $content = $disk->get($oldPath);

                    // Store in new location
                    $disk->put($newPath, $content);

                    // Delete old file
                    $disk->delete($oldPath);

                    echo "Moved: {$oldPath} -> {$newPath}\n";
                } catch (\Exception $e) {
                    echo "Error moving {$oldPath}: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented for safety
    }
};
