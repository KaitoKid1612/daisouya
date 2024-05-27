<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 運賃を追加
 */
class AddFreightCostDriverTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->after('basic_price', function ($table) {
                $table->integer('freight_cost')->nullable()->comment('運賃');
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
            $table->dropColumn('freight_cost');
        });
    }
}
