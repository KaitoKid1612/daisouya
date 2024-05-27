<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDriverSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->comment('ドライバーID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('available_date')->comment('稼働可能日付');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            // $table->primary(['driver_id', 'available_date']);
            $table->unique(['driver_id', 'available_date']);
        });
        
        DB::statement("ALTER TABLE driver_schedules COMMENT 'ドライバー稼働可能スケジュール'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_schedules');
    }
}
