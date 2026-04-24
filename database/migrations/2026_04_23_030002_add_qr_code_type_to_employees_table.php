<?php

/**
 * មីហ្គេរស្យុងសម្រាប់បន្ថែមប្រភេទ QR Code ទៅតារាងបុគ្គលិក
 * 
 * ប្រភេទ QR Code មាន២ប្រភេទ:
 * 1. ប្រភេទទី១ - អត្តសញ្ណាណបុគ្គលិក (ID Card) - សម្រាប់បង្ហាញព័ត៌មានផ្ទាល់ខ្លួន
 * 2. ប្រភេទទី២ - អវត្តមាន (Attendance) - សម្រាប់ស្កេនកត់ត្រាអវត្តមាន
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * បន្ថែមវាល QR Code ប្រភេទទី១ (ID Card)
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // QR Code ប្រភេទទី១ - អត្តសញ្ណាណបុគ្គលិក (ID Card QR)
            $table->string('id_card_qr_path')->nullable()->after('qr_code_path'); // ផ្លូវទៅរូប QR អត្តសញ្ណាណ
            $table->timestamp('id_card_qr_generated_at')->nullable()->after('id_card_qr_path'); // ពេលវេលាបង្កើត
            
            // QR Code ប្រភេទទី២ - អវត្តមាន (Attendance QR)
            $table->string('attendance_qr_path')->nullable()->after('id_card_qr_generated_at'); // ផ្លូវទៅរូប QR អវត្តមាន
            $table->timestamp('attendance_qr_generated_at')->nullable()->after('attendance_qr_path'); // ពេលវេលាបង្កើត
            $table->timestamp('attendance_qr_expires_at')->nullable()->after('attendance_qr_generated_at'); // ពេលវេលាផុតកំណត់
            
            // ទីតាំងគោលសម្រាប់កត់ត្រាអវត្តមាន
            $table->foreignId('default_location_id')->nullable()->after('shift_id')->constrained('work_locations')->onDelete('set null');
            
            // ស្ថានភាព QR Code
            $table->enum('qr_code_status', ['active', 'suspended', 'revoked'])->default('active')->after('attendance_qr_expires_at');
        });
    }

    /**
     * លុបវាល QR Code ប្រភេទ
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['default_location_id']);
            $table->dropColumn([
                'id_card_qr_path',
                'id_card_qr_generated_at',
                'attendance_qr_path',
                'attendance_qr_generated_at',
                'attendance_qr_expires_at',
                'default_location_id',
                'qr_code_status'
            ]);
        });
    }
};
