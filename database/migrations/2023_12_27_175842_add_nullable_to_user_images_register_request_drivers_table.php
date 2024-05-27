<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableToUserImagesRegisterRequestDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_request_drivers', function (Blueprint $table) {
            $table->string('avatar')->nullable()->change();
            $table->string('bank')->nullable()->change();
            $table->string('driving_license_front')->nullable()->change();
            $table->string('driving_license_back')->nullable()->change();
            $table->string('auto_insurance')->nullable()->change();
            $table->string('voluntary_insurance')->nullable()->change();
            $table->string('inspection_certificate')->nullable()->change();
            $table->string('license_plate_front')->nullable()->change();
            $table->string('license_plate_back')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('register_request_drivers', function (Blueprint $table) {
            //
        });
    }
}