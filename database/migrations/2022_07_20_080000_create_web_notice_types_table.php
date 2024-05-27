<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class CreateWebNoticeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_notice_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('通知の名前');
        });
        DB::statement("ALTER TABLE web_notice_types COMMENT '通知の種類'");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_notice_types');
    }
}
