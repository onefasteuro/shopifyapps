<?php

$namespace = 'onefasteuro\ShopifyAuth\Http\Controllers\\';


	



Route::group(['middleware' => ['web']], function () use(&$namespace) {
	
	Route::get('shopify/auth/webhooks/redact/customer', ['uses' => $namespace . 'RedactController@customer']);
	Route::get('shopify/auth/webhooks/redact/shop', ['uses' => $namespace . 'RedactController@customer']);
	
	//finish the auth process
	Route::get('shopify/auth/{appname}',
		[
			'as' =>  'shopifyauth.handle',
			'middleware' => [\onefasteuro\ShopifyAuth\Http\NonceMiddleware::class, \onefasteuro\ShopifyAuth\Http\AuthMiddleware::class],
			'uses' => $namespace . 'AuthController@getAuth'])->where('appname', '[a-z\-0-9]+');
	
	
	//Starts the auth process
	Route::get('shopify/auth/{appname}/{shop}',
		[
			'as' =>  'shopifyauth.begin',
			'middleware' => [\onefasteuro\ShopifyAuth\Http\NonceMiddleware::class],
			'uses' => $namespace . 'AuthController@getBegin'
		])
		->where('appname', '[a-z\-0-9]+')->where('shop', '[a-z\-0-9]+');
	

});