<?php

namespace onefasteuro\ShopifyApps\Http;


class SaveNonceMiddleware
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
        $this->nonce->createAndSave();
		return $next($request);
	}
}