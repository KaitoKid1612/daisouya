<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * カラム名変更 基本料金 -> システム利用料金
 */
class RenameBasicPriceToSystemPriceInDriverTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->renameColumn('basic_price', 'system_price');
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
            $table->renameColumn('system_price', 'basic_price');
        });
    }
}
