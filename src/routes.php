<?php

use Illuminate\Support\Facades\Route;
use \onefasteuro\ShopifyApps\Http\SetNonceStoreMiddleware;
use \onefasteuro\ShopifyApps\Http\SaveNonceStoreMiddleware;
use \onefasteuro\ShopifyApps\Http\AuthMiddleware;

$namespace = 'onefasteuro\ShopifyApps\Http\Controllers\\';



Route::group(['middleware' => ['web']], function () use(&$namespace) {
	
	Route::get('shopify/auth/webhooks/redact/customer', ['uses' => $namespace . 'RedactController@customer']);
	Route::get('shopify/auth/webhooks/redact/shop', ['uses' => $namespace . 'RedactController@customer']);
	
	//finish the auth process
	Route::get('shopify/auth/{shopify_app_name}/complete',
		[
			'as' =>  'shopify.auth.complete',
			'uses' => $namespace . 'AuthController@completeAuth'])
        ->where('ashopify_app_name', '[a-z\-0-9]+');
	
	
	//Starts the auth process
	Route::get('shopify/auth/{shopify_app_name}/{shop}',
		[
			'as' =>  'shopify.auth.redirect',
			'uses' => $namespace . 'AuthController@redirectToAuth'
		])
		->where('shopify_app_name', '[a-z\-0-9]+')
		->where('shop', '[a-z\-0-9]+');
	

	
	
	Route::get('shopify/billing/{id}/complete', [
		'as' =>  'shopify.billing.complete',
		'uses' => $namespace . 'BillingController@saveTransaction',
	])->where('app_installation_id', '[0-9]+');
	
	
	Route::get('shopify/billing/{app_installation_id}', [
		'as' =>  'shopify.billing.redirect',
		'uses' => $namespace . 'BillingController@redirectToBill',
	])->where('app_installation_id', '[0-9]+');
	
});