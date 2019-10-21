<?php

namespace onefasteuro\ShopifyApps\Http\Middlewares;



use Illuminate\Container\Container;
use onefasteuro\ShopifyApps\Services\AuthService;

class AuthMiddleware extends SaveNonceStoreMiddleware
{

    public function handle($request, \Closure $next)
	{
	    $container =  Container::getInstance();


		$hndl = $request->route()->parameter('app_handle');
		
		$config = $container->make('config')->get("shopifyapps.$hndl");
		
		
		if(!AuthService::assertNonce($request->query('state'), $this->nonce->retrieve())){
			return abort(403, 'Could not validate the request. State mismatch.');
		}
		
		//check if the HMAC signature matches the request
		if(!AuthService::assertHMAC($request->query(), $config['client_secret'])) {
			return abort(403, 'Could not validate the request. HMAC mismatch.');
		}
		
		//Check if the domain matches
		if(!AuthService::assertDomain($request->get('shop'))) {
			return abort(403, 'Could not validate the request. Domain mismatch.');
		}
		
		return $next($request);
	}
}