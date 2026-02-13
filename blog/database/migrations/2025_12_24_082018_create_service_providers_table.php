<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('owner_name');
            $table->string('location');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->json('services_provided'); // array of services
            $table->time('opening_time');
            $table->time('closing_time');
            $table->string('password');
            $table->string('pan_number');
            $table->string('company_doc'); // file path
            $table->string('building_photo'); // file path
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
