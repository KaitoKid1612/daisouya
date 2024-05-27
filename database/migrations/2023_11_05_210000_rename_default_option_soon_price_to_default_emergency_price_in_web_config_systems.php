<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDefaultOptionSoonPriceToDefaultEmergencyPriceInWebConfigSystems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_config_systems', function (Blueprint $table) {
            $table->renameColumn('default_option_soon_price', 'default_emergency_price');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('default_emergency_price_in_web_config_systems', function (Blueprint $table) {
            $table->renameColumn('default_emergency_price', 'default_option_soon_price');
            
        });
    }
}
