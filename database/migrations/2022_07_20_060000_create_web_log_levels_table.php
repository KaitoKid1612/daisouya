<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWebLogLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_log_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ログレベルの名前');
        });
        DB::statement("ALTER TABLE web_log_levels COMMENT 'ログのレベル'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_log_levels');
    }
}
