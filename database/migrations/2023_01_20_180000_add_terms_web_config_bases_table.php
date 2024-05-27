<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 特定商取引法に基づく表記 プライバシポリシー 依頼者用 ドライバー用
 */
class AddTermsWebConfigBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_config_bases', function (Blueprint $table) {
            // 特定商取引法に基づく表記
            $table->after('commerce_law_delivery_office', function ($table) {
                $table->text('commerce_law_driver')->comment('特定商取引法に基づく表記 ドライバー');
            });
            $table->text('commerce_law_delivery_office')->comment('特定商取引法に基づく表記 依頼者')->change();

            // プライバシポリシー
            $table->after('privacy_policy_delivery_office', function ($table) {
                $table->text('privacy_policy_driver')->comment('プライバシーポリシー ドライバー');
            });
            $table->text('privacy_policy_delivery_office')->comment('プライバシーポリシー 依頼者')->change();
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
            $table->dropColumn('commerce_law_driver');
            $table->dropColumn('commerce_law_delivery_office');
            $table->dropColumn('privacy_policy_driver');
            $table->dropColumn('privacy_policy_delivery_office');
        });
    }
}
