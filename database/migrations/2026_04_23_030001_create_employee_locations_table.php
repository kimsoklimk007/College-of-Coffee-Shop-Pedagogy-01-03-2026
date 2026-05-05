<?php

/**
 * មីហ្គេរស្យុងសម្រាប់បង្កើតតារាងចំណង់ទីតាំងបុគ្គលិក
 * 
 * តារាងនេះប្រើសម្រាប់ចំណង់ទីតាំងធ្វើការរវាងបុគ្គលិកនិងទីតាំង
 * បុគ្គលិកម្នាក់អាចធ្វើការនៅទីតាំងជាច្រើនបាន និងមានទីតាំងគោលសម្រាប់កត់ត្រាអវត្តមាន
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * បង្កើតតារាងចំណង់ទីតាំងបុគ្គលិក
     */
    public function up(): void
    {
        Schema::create('employee_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained('work_locations')->onDelete('cascade');
            
            // ប្រភេទនៃការចំណង់ (ទីតាំងគោល ឬ ទីតាំងជំនួស)
            $table->enum('assignment_type', [
                'primary',    // ទីតាំងគោល - ទីតាំងធ្វើការចម្បង់
                'secondary',  // ទីតាំងជំនួស - អាចទៅជួយនៅពេលចាំបាច់
                'temporary',  // ទីតាំងបណ្ដោះអាសន្ន - កំណត់សម្រាប់រយៈពេលកំណត់
                'all'         // អាចធ្វើការនៅគ្រប់ទីតាំង
            ])->default('primary');
            
            // រយៈពេលនៃការចំណង់ (សម្រាប់ temporary)
            $table->date('start_date')->nullable(); // ថ្ងៃចាប់ផ្ដើម
            $table->date('end_date')->nullable(); // ថ្ងៃបញ្ចប់
            
            // សិទ្ធិពិសេសនៅទីតាំងនេះ
            $table->boolean('can_check_in')->default(true); // អាចស្កេនចូលធ្វើការ
            $table->boolean('can_check_out')->default(true); // អាចស្កេនចេញធ្វើការ
            $table->boolean('is_location_manager')->default(false); // ជាអ្នកគ្រប់គ្រងទីតាំង
            
            // កំណត់សម្រាប់ការស្កេន QR
            $table->boolean('require_strict_location')->default(true); // តម្រូវឲ្យនៅក្នុងកម្រាស់ដែលកំណត់
            $table->integer('custom_radius_meters')->nullable(); // កម្រាស់ផ្ទាល់ខ្លួន (ប្រសិនបើខុសពី default)
            
            // ស្ថានភាព
            $table->enum('status', ['active', 'inactive', 'transferred'])->default('active');
            
            // Notes
            $table->text('notes')->nullable();
            
            // អ្នកបង្កើតនិងកែប្រែ
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Unique constraint - បុគ្គលិកម្នាក់មិនអាចមានទីតាំងគោលច្រើនជាងមួយបានទេ
            $table->unique(['employee_id', 'location_id']);
            
            // Indexes
            $table->index('employee_id');
            $table->index('location_id');
            $table->index('assignment_type');
            $table->index('status');
        });
    }

    /**
     * លុបតារាងចំណង់ទីតាំងបុគ្គលិក
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_locations');
    }
};
