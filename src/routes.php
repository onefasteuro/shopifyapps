<?php

use Illuminate\Support\Facades\Route;

$namespace = 'onefasteuro\ShopifyApps\Http\Controllers\\';


Route::group(['prefix' => 'shopify', 'middleware' => ['web']], function () use(&$namespace) {
	Route::get('webhooks/redact/customer', ['uses' => $namespace . 'WebhooksController@customer']);
	Route::get('webhooks/redact/shop', ['uses' => $namespace . 'WebhooksController@customer']);
	
	Route::get('webhooks/billing', [
		'uses' => $namespace . 'WebhooksController@handleWebhooks',
	]);
	
	//finish the auth process
	Route::get('auth/{app_handle}/complete',
		[
			'as' => 'shopify.auth.complete',
			'uses' => $namespace . 'AuthController@completeAuth'])
        ->where('app_handle', '[a-z\-0-9]+');
	
	
	//Starts the auth process
	Route::get('auth/{app_handle}/{shop_domain}',
		[
			'uses' => $namespace . 'AuthController@redirectToAuth'
		])
		->where('app_handle', '[a-z\-0-9]+')
		->where('shop_domain', '[a-z\-0-9]+');
	

	Route::get('billing/{app_handle}/{app_installation_id}', [
		'as' => 'shopify.billing.redirect',
		'uses' => $namespace . 'BillingController@redirectToBill',
	])->where('app_handle', '[a-z\-0-9]+')
		->where('app_installation_id', '[0-9]+');
	
	Route::get('billing/{app_handle}/{app_installation_id}/complete', [
		'as' => 'shopify.billing.complete',
		'uses' => $namespace . 'BillingController@completeBilling',
	])->where('app_handle', '[a-z\-0-9]+')
		->where('app_installation_id', '[0-9]+');
	
	

});