<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * カラム名変更  直前依頼料金 -> 緊急依頼料金
 */
class RenameOptionSoonPriceToEmergencyPriceInDriverTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->renameColumn('option_soon_price', 'emergency_price');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->renameColumn('emergency_price', 'option_soon_price');
        });
    }
}
