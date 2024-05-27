<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWebNoticeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_notice_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('web_log_level_id')->comment('ログレベル 外部キー')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('web_notice_type_id')->comment('通知の種類 外部キー')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('task_id')->nullable()->comment('稼働ID');
            $table->integer('to_user_id')->nullable()->comment('受信者のユーザーID');
            $table->string('to_user_info')->comment('受信者の情報  管理者/営業所/ドライバー email SNSIDなど');
            $table->integer('user_id')->nullable()->comment('通知を発火させたユーザのID');
            $table->string('user_info')->comment('通知を発火させたユーザの情報  管理者/営業所/ドライバー');
            $table->string('text')->comment('通知の内容');
            $table->string('remote_addr')->comment('ユーザIPアドレス');
            $table->string('http_user_agent')->comment('ユーザ OSブラウザ');
            $table->string('url')->comment('実行URL');
            $table->dateTime('created_at');
        });
        DB::statement("ALTER TABLE web_notice_logs COMMENT '通知ログ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_notice_logs');
    }
}
