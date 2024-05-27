<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class CreatePrefecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prefectures', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('都道府県名');
            $table->string('name_kana')->comment('読み仮名(カナ)');
            $table->string('name_romaji')->comment('ローマ字');
            $table->foreignId('region_id')->comment('地方ID(外部キー)')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
        DB::statement("ALTER TABLE prefectures COMMENT '都道府県'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prefectures');
    }
}
