<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserImagesToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->after('icon_img', function ($table) { $table->string('avatar'); });
            $table->after('avatar', function ($table) { $table->string('bank'); });
            $table->after('bank', function ($table) { $table->string('driving_license_front'); });
            $table->after('driving_license_front', function ($table) { $table->string('driving_license_back'); });
            $table->after('driving_license_back', function ($table) { $table->string('auto_insurance'); });
            $table->after('auto_insurance', function ($table) { $table->string('voluntary_insurance'); });
            $table->after('voluntary_insurance', function ($table) { $table->string('inspection_certificate'); });
            $table->after('inspection_certificate', function ($table) { $table->string('license_plate_front'); });
            $table->after('license_plate_front', function ($table) { $table->string('license_plate_back'); });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('bank');
            $table->dropColumn('driving_license_front');
            $table->dropColumn('driving_license_back');
            $table->dropColumn('auto_insurance');
            $table->dropColumn('voluntary_insurance');
            $table->dropColumn('inspection_certificate');
            $table->dropColumn('license_plate_front');
            $table->dropColumn('license_plate_back');
        });
    }
}