<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * カラムコメント 変更
 */
class EditColumnCommentDriverTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->integer('basic_price')->comment('システム利用料金')->change();
            $table->integer('option_soon_price')->comment('緊急依頼料金')->change();
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
            $table->integer('basic_price')->comment('基本料金')->change();
            $table->integer('option_soon_price')->comment('直前依頼オプション料金')->change();
        });
    }
}
