<?php

namespace onefasteuro\ShopifyApps\Http;



use onefasteuro\ShopifyApps\Auth\OAuthRequestValidator;

class AuthMiddleware extends SaveNonceStoreMiddleware
{
	
	
	public function handle($request, \Closure $next)
	{
		$shopify_app = $request->route()->parameter('shopify_app_name');
		
		$config = config('shopifyapps.'. $shopify_app);
		
		$validator = new OAuthRequestValidator($config, $this->nonce);
		
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