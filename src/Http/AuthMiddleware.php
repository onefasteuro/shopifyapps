<?php

namespace onefasteuro\ShopifyApps\Http;



class AuthMiddleware extends SaveNonceStoreMiddleware
{
	
	public function handle($request, \Closure $next)
	{
		$app_id = $request->route()->parameter('app_id');
		
		$config = config("shopifyapps.app_$app_id");
		
		$validator = new \onefasteuro\ShopifyApps\Services\OAuthRequestValidator($config, $this->nonce);
		
		if(!$validator->assertNonce($request)){
			return abort(403, 'Could not validate the request. State mismatch.');
		}
		
		//check if the HMAC signature matches the request
		if(!$validator->assertHMAC($request)) {
			return abort(403, 'Could not validate the request. HMAC mismatch.');
		}
		
		//Check if the domain matches
		if(!$validator->assertDomain($request)) {
			return abort(403, 'Could not validate the request. Domain mismatch.');
		}
		
		return $next($request);
	}
}