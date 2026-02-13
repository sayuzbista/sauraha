<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('vendor_services', function (Blueprint $table) {
            $table->string('promotion_category')->default('Silver')->after('description');
            $table->string('payment_screenshot')->nullable()->after('promotion_category');
            $table->string('qr_code_image')->nullable()->after('payment_screenshot');
        });
    }

    public function down(): void {
        Schema::table('vendor_services', function (Blueprint $table) {
            $table->dropColumn(['promotion_category', 'payment_screenshot', 'qr_code_image']);
        });
    }
};
