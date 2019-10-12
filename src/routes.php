<?php

use Illuminate\Support\Facades\Route;
use \onefasteuro\ShopifyApps\Http\SetupMiddleware;
use \onefasteuro\ShopifyApps\Http\SaveNonceMiddleware;
use \onefasteuro\ShopifyApps\Http\AuthMiddleware;

$namespace = 'onefasteuro\ShopifyApps\Http\Controllers\\';



Route::group(['middleware' => ['web']], function () use(&$namespace) {
	
	Route::get('shopify/auth/webhooks/redact/customer', ['uses' => $namespace . 'RedactController@customer']);
	Route::get('shopify/auth/webhooks/redact/shop', ['uses' => $namespace . 'RedactController@customer']);
	
	//finish the auth process
	Route::get('shopify/auth/{shopify_app_name}/complete',
		[
			'as' =>  'shopify.auth.complete',
			'middleware' => [SetupMiddleware::class, SaveNonceMiddleware::class, AuthMiddleware::class],
			'uses' => $namespace . 'AuthController@handleAuth'])
        ->where('ashopify_app_name', '[a-z\-0-9]+');


	//Starts the auth process
	Route::get('shopify/auth/{shopify_app_name}/{shop}/url',
		[
			'as' =>  'shopify.auth.url',
			'middleware' => [SetupMiddleware::class, SaveNonceMiddleware::class],
			'uses' => $namespace . 'AuthController@getAuthUrl'
		])
		->where('shopify_app_name', '[a-z\-0-9]+')->where('shop', '[a-z\-0-9]+');
	
	//Starts the auth process
	Route::get('shopify/auth/{shopify_app_name}/{shop}',
		[
			'as' =>  'shopify.auth.redirect',
			'middleware' => [SetupMiddleware::class, SaveNonceMiddleware::class],
			'uses' => $namespace . 'AuthController@redirectToAuth'
		])
		->where('shopify_app_name', '[a-z\-0-9]+')->where('shop', '[a-z\-0-9]+');
	

	
	
	Route::get('shopify/billing/{appname}/{id}/complete', [
		'as' =>  'shopify.billing.complete',
		'uses' => $namespace . 'BillingController@saveTransaction',
	])->where('appname', '[a-z\-0-9]+')
		->where('id', '[0-9]+');
	
	
	Route::get('shopify/billing/{appname}/{id}', [
		'as' =>  'shopify.billing.redirect',
		'uses' => $namespace . 'BillingController@redirectToBill',
	])->where('appname', '[a-z\-0-9]+')
		->where('id', '[0-9]+');
	
});