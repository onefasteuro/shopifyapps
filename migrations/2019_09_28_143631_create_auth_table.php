<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('shopify_apps', function (Blueprint $table) {
	        $table->bigIncrements('id');
		    $table->bigInteger('shop_id')->index('shop_id');
		    $table->bigInteger('app_installation_id')->index('app_installation_id');
		    $table->bigInteger('app_id')->index('app_id');
		    $table->string('app_name', 100)->index('app_name');
		    $table->string('app_launch_url', 200)->nullable()->default(null);
		    $table->string('shop_domain', 200)->index('shop_domain');
		    $table->string('shop_name');
		    $table->string('shop_email', 200)->index('shop_email')->nullable()->default(null);
		    $table->string('token', 100)->nullable()->default(null);
		    $table->string('scope', 250)->nullable()->default(null);
		    $table->timestamps();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopify_apps');
    }
}
