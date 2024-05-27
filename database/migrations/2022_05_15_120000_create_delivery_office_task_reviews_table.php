<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDeliveryOfficeTaskReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_office_task_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_task_id')->unique()->comment('どの稼働依頼に対するレビューか 稼働依頼id(主キー、外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate(); // 1稼働1レビュー
            $table->integer('score')->comment('評価点');
            $table->text('title')->comment('レビュータイトル');
            $table->text('text')->comment('レビュー本文');
            $table->foreignId('delivery_office_id')->nullable()->comment('レビュー対象 配送営業所id 外部キー')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('driver_id')->nullable()->comment('レビュー者 ドライバーid 外部キー')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('review_public_status_id')->comment('公開ステータスid 外部キー');
            $table->foreign('review_public_status_id')->references('id')->on('delivery_office_task_review_public_statuses')->cascadeOnDelete()->cascadeOnUpdate();

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
        DB::statement("ALTER TABLE delivery_office_task_reviews COMMENT 'レビュー ドライバーから依頼者(配送営業所)への評価'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_office_task_reviews');
    }
}
