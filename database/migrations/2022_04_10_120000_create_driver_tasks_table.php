<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_tasks', function (Blueprint $table) {
            $table->id();
            $table->datetime('task_date')->comment('稼働依頼日');
            $table->foreignId('driver_id')->nullable()->comment('担当ドライバーID(外部キー) ドライバーを指定しない依頼はnullになる')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('delivery_office_id')->nullable()->comment('依頼した営業所ID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('driver_task_status_id')->comment('稼働ステータス(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('rough_quantity')->comment('配送の物量');
            $table->text('delivery_route')->comment('配送コース');
            $table->text('task_memo')->comment('依頼メモ');
            $table->string('task_delivery_company_name')->comment('集荷先配送会社名');
            $table->string('task_delivery_office_name')->comment('集荷先営業所名');
            $table->string('task_email')->comment('集荷先メールアドレス');
            $table->string('task_tel')->comment('集荷先電話番号');
            $table->string('task_post_code1')->comment('集荷先郵便番号1');
            $table->string('task_post_code2')->comment('集荷先郵便番号2');
            $table->foreignId('task_addr1_id')->comment('集荷先都道府県')->constrained('prefectures');
            $table->string('task_addr2')->comment('集荷先市区町村');
            $table->string('task_addr3')->comment('集荷先丁目 番地 号');
            $table->string('task_addr4')->comment('集荷先建物名部屋番号');


            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        DB::statement("ALTER TABLE driver_tasks COMMENT '稼働依頼'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_tasks');
    }
}
