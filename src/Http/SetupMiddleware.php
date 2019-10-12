<?php

namespace onefasteuro\ShopifyApps\Http;


use Illuminate\Contracts\Config\Repository as ConfigRepository;

class SetupMiddleware
{
    protected $config;
    protected $nonce;

    public function __construct(ConfigRepository $config, \onefasteuro\ShopifyApps\Nonce $nonce)
    {
        $this->config = $config;
        $this->nonce = $nonce;
    }


    public function handle($request, \Closure $next)
	{
		if(($shopify_app_name = $request->route()->parameter('shopify_app_name'))) {
            $this->config->set('shopifyapps.default', $shopify_app_name);
        }

        $this->setNonceStore($request);

		dd(2);

		return $next($request);
	}

    public function setNonceStore($request)
    {
        $this->nonce->setStore($request->session());
    }
}