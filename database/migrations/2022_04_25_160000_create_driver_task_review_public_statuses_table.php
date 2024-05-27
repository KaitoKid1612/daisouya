<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverTaskReviewPublicStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_task_review_public_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('公開状態');
        });
        DB::statement("ALTER TABLE driver_task_review_public_statuses COMMENT 'ドライバーレビューのステータス'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_task_review_public_statuses');
    }
}
