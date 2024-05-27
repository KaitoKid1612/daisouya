<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * stripeで利用するカラム追加
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_offices', function (Blueprint $table) {
            $table->after('remember_token', function ($table) {
                $table->string('stripe_id')->collation('utf8_bin')->nullable()->index();
                $table->string('pm_type')->nullable()->comment('カードの種類');
                $table->string('pm_last_four', 4)->nullable()->comment('下4桁');
                $table->timestamp('trial_ends_at')->nullable()->comment('仕様期間終了日時');
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
        Schema::table('delivery_offices', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_id',
                'pm_type',
                'pm_last_four',
                'trial_ends_at',
            ]);
        });
    }
};
