<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWebContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_type_id')->comment('ユーザ種類 外部キー')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('user_id')->nullable()->comment('ユーザID ドライバー,配送営業所');
            $table->string('name_sei')->comment('姓');
            $table->string('name_mei')->comment('名');
            $table->string('name_sei_kana')->comment('姓(カナ)');
            $table->string('name_mei_kana')->comment('名(カナ)');
            $table->string('email')->comment('メールアドレス');
            $table->string('tel')->comment('電話番号');
            $table->foreignId('web_contact_type_id')->comment('お問い合わせ種類 外部キー')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('web_contact_status_id')->comment('お問い合わせステータス 外部キー')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title')->comment('題目');
            $table->text('text')->comment('内容');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        DB::statement("ALTER TABLE web_contacts COMMENT 'お問い合わせ'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_contacts');
    }
}
