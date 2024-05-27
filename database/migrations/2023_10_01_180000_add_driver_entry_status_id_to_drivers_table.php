<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ドライバー申請状況
 */
class AddDriverEntryStatusIdToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->after('user_type_id', function ($table) {
                    $table->foreignId('driver_entry_status_id')->nullable()->comment('登録申請ステータス')->constrained();
                });
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('driver_entry_status_id')->nullable()->comment('登録状況')->constrained();
        });
    }
}
