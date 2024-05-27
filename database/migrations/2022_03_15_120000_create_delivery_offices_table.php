<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDeliveryOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_offices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_type_id')->comment('ユーザー種類(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->comment('営業所名');
            $table->string('manager_name_sei')->comment('担当者 姓');
            $table->string('manager_name_mei')->comment('担当者 名');
            $table->string('manager_name_sei_kana')->comment('担当者 姓 読み仮名(カナ)');
            $table->string('manager_name_mei_kana')->comment('担当者 名 読み仮名(カナ)');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->comment('パスワード');
            $table->foreignId('delivery_company_id')->nullable()->comment('配送会社id(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('delivery_company_name')->comment('会社名 (delivery_company_idがnullの場合に利用する)');
            $table->foreignId('delivery_office_type_id')->comment('依頼者種類')->constrained()->cascadeOnDelete()->cascadeOnUpdate()->name('register_request_delivery_office_type_id_foreign');
            $table->string('post_code1')->comment('郵便番号1');
            $table->string('post_code2')->comment('郵便番号2');
            $table->foreignId('addr1_id')->comment('都道府県(外部キー)')->constrained('prefectures');
            $table->string('addr2')->comment('市区町村');
            $table->string('addr3')->comment('丁目 番地 号');
            $table->string('addr4')->comment('建物名 部屋番号');
            $table->string('manager_tel')->comment('担当者電話番号');

            $table->rememberToken();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
        DB::statement("ALTER TABLE delivery_offices COMMENT '依頼者(配送営業所)アカウント'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_offices');
    }
}
