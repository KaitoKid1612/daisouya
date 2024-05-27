<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToDriverTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->after('updated_at', function ($table) { $table->dateTime('deleted_at')->nullable(); });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_tasks', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}
