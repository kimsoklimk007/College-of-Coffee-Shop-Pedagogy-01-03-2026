<?php

/**
 * មីហ្គេរស្យុងសម្រាប់បង្កើតតារាងទីតាំងធ្វើការ
 * 
 * តារាងនេះប្រើសម្រាប់គ្រប់គ្រងទីតាំងដែលបុគ្គលិកអាចស្កេន QR កត់ត្រាអវត្តមាន
 * វាមានប្រព័ន្ធកំណត់ដែន (geo-fencing) ដើម្បីការពារការស្កេនពីចម្ងាយ
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * បង្កើតតារាងទីតាំងធ្វើការ
     */
    public function up(): void
    {
        Schema::create('work_locations', function (Blueprint $table) {
            $table->id();
            
            // ព័ត៌មានទូទៅរបស់ទីតាំង
            $table->string('location_code')->unique(); // លេខកូដទីតាំង (ឧទាហរណ៍: LOC001, MAIN_SHOP)
            $table->string('name'); // ឈ្មោះទីតាំង (ភាសាអង់គ្លេស)
            $table->string('name_kh')->nullable(); // ឈ្មោះទីតាំង (ភាសាខ្មែរ)
            $table->text('description')->nullable(); // ការពិពណ៌នាលម្អិត
            $table->text('address')->nullable(); // អាស័យដ្ឋាន
            $table->string('city')->default('Phnom Penh'); // ទីក្រុង
            $table->string('province')->nullable(); // ខេត្ត
            
            // ប្រភេទទីតាំង (ហាងស្នាក់ការកណ្ដាល, សាខា, ឃ្លាំង, កន្លែងផ្សេងៗ)
            $table->enum('location_type', [
                'main_shop',      // ហាងស្នាក់ការកណ្ដាល
                'branch',         // សាខា
                'warehouse',      // ឃ្លាំង
                'kitchen',        // ផ្ទះបាយ
                'office',         // ការិយាល័យ
                'other'           // កន្លែងផ្សេងៗ
            ])->default('main_shop');
            
            // ព័ត៌មាន GPS សម្រាប់កំណត់ដែន (geo-fencing)
            $table->decimal('latitude', 10, 8)->nullable(); // រយៈទទឹង
            $table->decimal('longitude', 11, 8)->nullable(); // រយៈបណ្ដោយ
            $table->integer('radius_meters')->default(100); // កម្រាស់អនុញ្ញាតជាម៉ែត្រ (default 100m)
            
            // ការកំណត់សុវត្ថិភាព
            $table->boolean('requires_wifi_ssid')->default(false); // តម្រូវឲ្យភ្ជាប់ WiFi ជាក់លាក់
            $table->string('allowed_wifi_ssid')->nullable(); // ឈ្មោះ WiFi ដែលអនុញ្ញាត
            $table->boolean('requires_bluetooth')->default(false); // តម្រូវឲ្យប្រើ Bluetooth beacon
            $table->string('bluetooth_uuid')->nullable(); // UUID របស់ Bluetooth beacon
            
            // ស្ថានភាពទីតាំង
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->boolean('is_default')->default(false); // ទីតាំងគោលដែលបុគ្គលិកត្រូវបានចាត់តាំង
            
            // ព័ត៌មានពេលវេលាធ្វើការ
            $table->time('opening_time')->default('06:00:00'); // ម៉ោងបើក
            $table->time('closing_time')->default('22:00:00'); // ម៉ោងបិទ
            $table->json('working_days')->nullable(); // ថ្ងៃធ្វើការ [1,2,3,4,5,6,7]
            
            // ម្ចាស់ជំនាញ និងអ្នកគ្រប់គ្រង
            $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null'); // អ្នកគ្រប់គ្រងទីតាំង
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            // រូបថតនិងឯកសារ
            $table->string('location_photo')->nullable(); // រូបថតទីតាំង
            $table->string('floor_plan')->nullable(); // ផែនទីជាន់
            
            // Notes ផ្សេងៗ
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('location_code');
            $table->index('status');
            $table->index('location_type');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * លុបតារាងទីតាំងធ្វើការ
     */
    public function down(): void
    {
        Schema::dropIfExists('work_locations');
    }
};
