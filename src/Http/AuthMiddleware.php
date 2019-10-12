<?php

namespace onefasteuro\ShopifyApps\Http;


class AuthMiddleware extends SaveNonceMiddleware
{

	public function handle($request, \Closure $next)
	{
		parent::setNonceStore($request);
		
		//check if the nonce matches
		if(!$this->assertNonce($request)) {
			return abort(403, 'Could not validate the request. State mismatch.');
		}
		
		//check if the HMAC signature matches the request
		if(!$this->assertHMAC($request)) {
			return abort(403, 'Could not validate the request. HMAC mismatch.');
		}
		
		//Check if the domain matches
		if(!$this->assertDomain($request)) {
			return abort(403, 'Could not validate the request. Domain mismatch.');
		}
		
		return $next($request);
	}
	
	protected function assertNonce(\Illuminate\Http\Request $request)
	{
		$nonce = $this->nonce->retrieve();

		//nonce do not match, error out
		return $nonce === $request->query('state');
	}

	protected function assertDomain(\Illuminate\Http\Request $request)
	{
		$domain = $request->get('shop');

		return preg_match('/[a-zA-Z0-9\-]+\.myshopify\.com/', $domain);
	}


	protected function assertHMAC(\Illuminate\Http\Request $request)
	{
		$query = $request->query();

		$hmac = $query['hmac'];
		if(array_key_exists('hmac', $query) ) {
			unset($query['hmac']);
		}

		if(array_key_exists('signature', $query)) {
			unset($query['signature']);
		}

		//sort the array by key
		ksort($query);


		$data = urldecode(http_build_query($query));

		$secret = $this->helpers->getClientSecret();

		$calc = hash_hmac('sha256', $data, $secret);

		return $calc == $hmac;
	}
}