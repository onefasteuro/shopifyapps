<?php

namespace onefasteuro\ShopifyApps\Http\Middlewares;



use Illuminate\Container\Container;

class AuthMiddleware extends SaveNonceStoreMiddleware
{

    public function handle($request, \Closure $next)
	{
	    $container =  Container::getInstance();


		$hndl = $request->route()->parameter('shop_handle');
		
		$config = $container->make('config')->get("shopifyapps.$hndl");

		$validator = $container->make('shopifyapps.auth.service');

		if(!$validator->assertNonce($request->query('state'), $this->nonce->retrieve())){
			return abort(403, 'Could not validate the request. State mismatch.');
		}
		
		//check if the HMAC signature matches the request
		if(!$validator->assertHMAC($request->query(), $config['client_secret'])) {
			return abort(403, 'Could not validate the request. HMAC mismatch.');
		}
		
		//Check if the domain matches
		if(!$validator->assertDomain($request->get('shop'))) {
			return abort(403, 'Could not validate the request. Domain mismatch.');
		}
		
		return $next($request);
	}
}