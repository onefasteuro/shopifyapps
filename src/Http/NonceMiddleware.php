<?php

namespace onefasteuro\ShopifyApps\Http;


class NonceMiddleware
{

	protected $nonce;


    /**
     * Instance of our Nonce set the storage to the session
     * NonceMiddleware constructor.
     * @param \onefasteuro\ShopifyApps\Nonce $n
     */
	public function __construct(\onefasteuro\ShopifyApps\Nonce $n)
	{
		$this->nonce = $n;
	}
	
	public function handle($request, \Closure $next)
	{
		$this->setNonceStore($request);
		
		return $next($request);
	}
	
	
	public function setNonceStore($request)
	{
		$this->nonce->setStore($request->session());
		
		//create and save the nonce
		if($request->route()->getName() === 'shopify.auth.url' or
			$request->route()->getName() === 'shopify.auth.redirect') {
			$this->nonce->createAndSave();
		}
	}
}