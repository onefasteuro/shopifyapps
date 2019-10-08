<?php

namespace onefasteuro\ShopifyAuth\Http;


use Closure;
use Illuminate\Http\Request;
use onefasteuro\ShopifyAuth\Nonce;

class AuthMiddleware extends NonceMiddleware
{
	
	
	public function handle($request, Closure $next)
	{
		parent::setNonceStore($request);
		
		if(!$this->assertNonce($request)) {
			return abort(403, 'Could not validate the request. State mismatch.');
		}
		
		if(!$this->assertHMAC($request)) {
			return abort(403, 'Could not validate the request. HMAC mismatch.');
		}
		
		if(!$this->assertDomain($request)) {
			return abort(403, 'Could not validate the request. Domain mismatch.');
		}
		
		return $next($request);
	}
	
	protected function assertNonce(Request $request)
	{
		$nonce = $this->nonce->retrieve();
		
		//nonce do not match, error out
		return $nonce === $request->query('state');
	}
	
	protected function assertDomain(Request $request)
	{
		$domain = $request->get('shop');
		
		return preg_match('/[a-zA-Z0-9\-]+\.myshopify\.com/', $domain);
	}
	
	
	protected function assertHMAC(Request $request)
	{
		$app = $request->route()->parameter('appname');
		
		$query = $request->query();
		
		$hmac = $query['hmac'];
		if(array_key_exists('hmac', $query) ) {
			unset($query['hmac']);
		}
		
		if(array_key_exists('signature', $query)) {
			unset($query['signature']);
		}
		
		ksort($query);
		
		
		$data = urldecode(http_build_query($query));
		
		$secret = config('shopifyauth.apps.'.$app.'.client_secret');
		
		$calc = hash_hmac('sha256', $data, $secret);
		
		return $calc == $hmac;
	}
}