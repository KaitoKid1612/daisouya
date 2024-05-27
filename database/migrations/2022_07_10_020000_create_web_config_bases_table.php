<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWebConfigBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_config_bases', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->comment('サイト名');
            $table->string('company_name')->comment('会社名');
            $table->string('company_name_kana')->comment('会社名(カナ)');
            $table->string('owner_name')->comment('オーナー名前');
            $table->string('post_code1')->comment('郵便番号1');
            $table->string('post_code2')->comment('郵便番号2');
            $table->foreignId('addr1_id')->comment('都道府県(外部キー)')->constrained('prefectures');
            $table->string('addr2')->comment('市区町村');
            $table->string('addr3')->comment('丁目 番地 号');
            $table->string('addr4')->comment('建物名 部屋番号');
            $table->string('tel')->comment('電話番号');
            $table->text('commerce_law')->comment('特定商取引法に基づく表記');
            $table->text('terms_service_delivery_office')->comment('ご利用規約 依頼者');
            $table->text('terms_service_driver')->comment('ご利用規約 ドライバー');
            $table->text('privacy_policy')->comment('プライバシーポリシー');
            $table->string('transfer')->comment('振込情報');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        DB::statement("ALTER TABLE web_config_bases COMMENT 'サイト基本設定'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_config_bases');
    }
}
