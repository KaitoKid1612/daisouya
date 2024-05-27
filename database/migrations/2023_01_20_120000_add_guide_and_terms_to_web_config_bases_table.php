<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ユーザーガイド追加 & 特定商取引法に基づく表記、プライバシポリシーのリネーム
 */
class AddGuideAndTermsToWebConfigBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_config_bases', function (Blueprint $table) {

            // ユーザーガイド追加
            $table->after('privacy_policy', function ($table) {
                $table->string('user_guide_path_delivery_office')->default('')->comment('ユーザガイド(ドライバー用)のパス');
                $table->string('user_guide_path_driver')->default('')->comment('ユーザガイド(依頼者用)のパス');
            });

            // 名前変更
            $table->renameColumn('commerce_law', 'commerce_law_delivery_office');
            $table->renameColumn('privacy_policy', 'privacy_policy_delivery_office');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_config_bases', function (Blueprint $table) {
            $table->dropColumn('user_guide_path_driver');
            $table->dropColumn('user_guide_path_delivery_office');

        });
    }
}
