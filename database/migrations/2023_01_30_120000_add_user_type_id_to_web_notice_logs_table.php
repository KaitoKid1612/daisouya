<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserTypeIdToWebNoticeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_notice_logs', function (Blueprint $table) {
            $table->after('to_user_id', function ($table) {
                $table->unsignedBigInteger('to_user_type_id')->nullable()->comment('受信者ユーザータイプ');
            });
            $table->after('user_id', function ($table) {
                $table->unsignedBigInteger('user_type_id')->nullable()->comment('発火者ユーザータイプ');
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
        Schema::table('web_notice_logs', function (Blueprint $table) {
            $table->dropColumn('to_user_type_id');
            $table->dropColumn('user_type_id');
        });
    }
}
