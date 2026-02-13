<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_service_id')->constrained()->onDelete('cascade');
            $table->integer('booked_units')->default(1); // 1 unit per booking
            $table->integer('persons');
            $table->date('booking_date');
            $table->string('time_slot'); // e.g., "09:00 - 12:00" or "Full Day"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};