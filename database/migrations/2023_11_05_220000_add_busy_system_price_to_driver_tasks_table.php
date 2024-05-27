<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusySystemPriceToDriverTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->after('system_price', function ($table) {
                $table->integer('busy_system_price')->default(0)->comment("システム利用料金(繁忙期)");
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
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->dropColumn('busy_system_price');
        });
    }
}
