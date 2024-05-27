<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverPlanIdToRegisterRequestDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('register_request_drivers', function (Blueprint $table) {
            $table->after('register_request_status_id', function ($table) {
                $table->foreignId('driver_plan_id')->nullable()->comment("ドライバーブラン")->constrained();
            });
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
            $table->dropColumn('driver_plan_id');
        });
    }
}
