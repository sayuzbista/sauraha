<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vendor_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('service_providers')->onDelete('cascade'); // link to vendor
            $table->string('title');
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->integer('duration_hours')->nullable();
            $table->integer('maxPersons')->nullable();
            $table->string('offer_percentage')->nullable();
            $table->string('free_person_offer')->nullable();
            $table->text('description')->nullable();       // NEW
            $table->string('availability')->default('Available');
            $table->json('images')->nullable();           // store multiple images as JSON
            $table->string('promotion_category')->default('Silver');   // NEW
            $table->string('payment_screenshot')->nullable();          // NEW
            $table->string('qr_code_image')->nullable();               // NEW
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vendor_services');
    }
};
