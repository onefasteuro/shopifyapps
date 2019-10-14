<?php

namespace onefasteuro\ShopifyApps\Http;



class SetNonceStoreMiddleware
{
    protected $nonce;

    public function __construct(\onefasteuro\ShopifyApps\Nonce $nonce)
    {
        $this->nonce = $nonce;
    }


    public function handle($request, \Closure $next)
	{
        $this->setNonceStore($request);

		return $next($request);
	}

    public function setNonceStore($request)
    {
        $this->nonce->setStore($request->session());
    }
}