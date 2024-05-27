<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWebConfigSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_config_systems', function (Blueprint $table) {
            $table->id();
            $table->string('email_notice')->comment('通知用メールアドレス');
            $table->string('email_from')->comment('送信元メールアドレス(From)');
            $table->string('email_reply_to')->comment('返信受付メールアドレス(ReplyTo)');
            $table->string('email_no_reply')->comment('返信を受け付けないときの架空のメールアドレス(no-reply)');
            $table->integer('create_task_time_limit_from')->comment('登録できる稼働依頼は、本日から何日後から');
            $table->integer('create_task_time_limit_to')->comment('登録できる稼働依頼は、本日から何日後まで');
            $table->integer('create_task_hour_limit')->comment('登録できる稼働依頼の日付範囲に時間の指定。 何時まで登録可能か。');
            $table->integer('task_time_out_later')->comment('現在から何日後の稼働日を時間切れにするか');
            $table->integer('register_request_token_time_limit')->comment('登録申請の有効期限(許可が出てから何時間後まで有効か)');

            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        DB::statement("ALTER TABLE web_config_systems COMMENT 'サイトシステム設定'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_config_systems');
    }
}
