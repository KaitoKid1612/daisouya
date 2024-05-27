<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRegisterRequestDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_request_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('token')->nullable()->unique()->comment('登録トークン');
            $table->foreignId('register_request_status_id')->comment('登録申請ステータスID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name_sei')->comment('姓');
            $table->string('name_mei')->comment('名');
            $table->string('name_sei_kana')->comment('姓(カナ)');
            $table->string('name_mei_kana')->comment('名(カナ)');
            $table->string('email')->comment('メールアドレス');
            $table->foreignId('gender_id')->comment('性別ID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('birthday')->comment('誕生日');
            $table->string('post_code1')->comment('郵便番号1');
            $table->string('post_code2')->comment('郵便番号2');
            $table->foreignId('addr1_id')->comment('都道府県(外部キー)')->constrained('prefectures');
            $table->string('addr2')->comment('市区町村');
            $table->string('addr3')->comment('丁目 番地 号');
            $table->string('addr4')->comment('建物名 部屋番号');
            $table->string('tel')->comment('電話番号');
            $table->text('career')->comment('経歴');
            $table->text('introduction')->comment('紹介文');
            $table->text('message')->comment('メッセージ、備考');
            $table->dateTime('time_limit_at')->nullable()->comment('登録の期限');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
        DB::statement("ALTER TABLE register_request_drivers COMMENT '登録申請 ドライバー'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('register_request_drivers');
    }
}
