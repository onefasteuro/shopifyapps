<?php

namespace onefasteuro\ShopifyApps\Http\Middlewares;



class AuthMiddleware extends SaveNonceStoreMiddleware
{
	
	public function handle($request, \Closure $next)
	{
		$hndl = $request->route()->parameter('shop_handle');
		
		$config = config("shopifyapps.$hndl");
		
		$validator = new \onefasteuro\ShopifyApps\Services\OAuthRequestValidator($config);
		
		if(!$validator->assertNonce($request, $this->nonce->retrieve())){
			return abort(403, 'Could not validate the request. State mismatch.');
		}
		
		//check if the HMAC signature matches the request
		if(!$validator->assertHMAC($request, $config['client_secret'])) {
			return abort(403, 'Could not validate the request. HMAC mismatch.');
		}
		
		//Check if the domain matches
		if(!$validator->assertDomain($request)) {
			return abort(403, 'Could not validate the request. Domain mismatch.');
		}
		
		return $next($request);
	}
}