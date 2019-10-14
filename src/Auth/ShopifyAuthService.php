<?php

namespace onefasteuro\ShopifyApps\Auth;

use Illuminate\Support\Arr;
use onefasteuro\ShopifyClient\GraphClient;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use onefasteuro\ShopifyApps\Nonce;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class ShopifyAuthService
{
    protected $nonce;
    protected $client;
    protected $events;
    protected $config = [];

    //the active app that we draw config etc from
    protected $shopify_app;
    protected $shopify_domain;

	public function __construct(Nonce $nonce, GraphClient $client, EventsDispatcher $events)
    {
        $this->nonce = $nonce;
        $this->client = $client;
        $this->events = $events;
    }
    
    public function setShopifyAppConfig(array $config)
    {
    	$config = $this->validateConfig($config);
    	$this->config = $config;
    	return $this;
    }
    
    protected function validateConfig(array $config)
    {
    	//params we need to check
    	$params = [
    		'client_id',
		    'client_secret',
		    'return_url',
		    'redirect_url',
		    'scope'
	    ];
    	
    	return $config;
    }

    public function setShopifyApp($app)
    {
        $this->shopify_app = $app;
        return $this;
    }

    public function setShopifyDomain($domain)
    {
        $this->shopify_domain = ShopifyUtils::formatDomain($domain);
        return $this;
    }


    /**
     * This is the URL that we redirect to for shopify to create the oauth token
     */
    public function getOAuthUrl($domain)
    {
        $shopify_domain = ShopifyUtils::formatDomain($domain);

        //grab the url to start the authorization
        $format = ShopifyUtils::URL_AUTHORIZE;
    }


    public function verifyOAuthCode()
    {

    }


}
