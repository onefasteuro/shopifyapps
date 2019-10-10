<?php

$namespace = 'onefasteuro\ShopifyApps\Http\Controllers\\';






Route::group(['middleware' => ['web']], function () use(&$namespace) {
	
	Route::get('shopify/auth/webhooks/redact/customer', ['uses' => $namespace . 'RedactController@customer']);
	Route::get('shopify/auth/webhooks/redact/shop', ['uses' => $namespace . 'RedactController@customer']);
	
	//finish the auth process
	Route::get('shopify/auth/{appname}',
		[
			'as' =>  'shopify.auth.handle',
			'middleware' => [\onefasteuro\ShopifyApps\Http\NonceMiddleware::class, \onefasteuro\ShopifyApps\Http\AuthMiddleware::class],
			'uses' => $namespace . 'AuthController@handleAuth'])->where('appname', '[a-z\-0-9]+');
	
	//Starts the auth process
	Route::get('shopify/auth/{appname}/{shop}/url',
		[
			'as' =>  'shopify.auth.url',
			'middleware' => [\onefasteuro\ShopifyApps\Http\NonceMiddleware::class],
			'uses' => $namespace . 'AuthController@getAuthUrl'
		])
		->where('appname', '[a-z\-0-9]+')->where('shop', '[a-z\-0-9]+');
	
	//Starts the auth process
	Route::get('shopify/auth/{appname}/{shop}',
		[
			'as' =>  'shopify.auth.redirect',
			'middleware' => [\onefasteuro\ShopifyApps\Http\NonceMiddleware::class],
			'uses' => $namespace . 'AuthController@redirectToAuth'
		])
		->where('appname', '[a-z\-0-9]+')->where('shop', '[a-z\-0-9]+');
	

	
	
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