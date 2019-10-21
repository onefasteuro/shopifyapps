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
		    $table->string('token', 100)->index('token');
		    $table->bigInteger('app_id')->index('app_id');
		    $table->bigInteger('app_installation_id')->index('app_installation_id');
		    $table->string('shop_domain', 100);
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
