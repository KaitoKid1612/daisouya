<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverTaskReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_task_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_task_id')->nullable()->unique()->comment('どの稼働依頼に対するレビューか 稼働依頼id(主キー、外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate(); // 1稼働1レビュー
            $table->integer('score')->comment('評価点');
            $table->text('title')->comment('レビュータイトル');
            $table->text('text')->comment('レビュー本文');
            $table->foreignId('driver_id')->nullable()->comment('レビュー対象 ドライバーid(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('delivery_office_id')->nullable()->comment('レビュー者 配送営業所id(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('driver_task_review_public_status_id')->comment('公開ステータスid(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            // $table->primary([ 'driver_task_id' ]);

            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
        DB::statement("ALTER TABLE driver_task_reviews COMMENT 'レビュー 依頼者(配送営業所)からドライバーへの評価'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_task_reviews');
    }
}
