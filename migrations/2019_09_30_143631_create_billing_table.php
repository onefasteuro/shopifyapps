<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('shopify_billings', function (Blueprint $table) {
	        $table->bigIncrements('id');
	        $table->bigInteger('app_id')->index('app_id');
	        $table->boolean('purchase_completed')->default(false);
		    $table->bigInteger('purchase_id');
		    $table->string('charge_id', 20)->nullable()->default(null);
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
        Schema::dropIfExists('shopify_billings');
    }
}
