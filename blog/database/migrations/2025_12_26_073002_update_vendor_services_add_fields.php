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
    public function up()
    {
        Schema::table('vendor_services', function (Blueprint $table) {
              $table->decimal('duration_hours', 4, 2)->after('price')->default(1);
            $table->integer('offer_percentage')->nullable()->after('duration_hours');
            $table->string('free_person_offer')->nullable()->after('offer_percentage');
            $table->longText('description')->nullable()->after('offer');
    
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_services', function (Blueprint $table) {
            //
              $table->dropColumn(['duration_hours', 'offer_percentage', 'free_person_offer', 'description']);
        });
    }
};
