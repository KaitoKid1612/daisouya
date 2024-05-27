<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ドライバーの登録状況
 */
class CreateDriverEntryStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_entry_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名前');
            $table->string('label')->comment('ラベル');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        DB::statement("ALTER TABLE driver_entry_statuses COMMENT 'ドライバーの登録申請ステータス'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_entry_statuses');
    }
}
