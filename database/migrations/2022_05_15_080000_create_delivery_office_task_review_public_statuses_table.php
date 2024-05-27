<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDeliveryOfficeTaskReviewPublicStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_office_task_review_public_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('公開状態');
        });
        DB::statement("ALTER TABLE delivery_office_task_review_public_statuses COMMENT 'レビュー公開ステータス 配送営業所'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_office_task_review_public_statuses');
    }
}
