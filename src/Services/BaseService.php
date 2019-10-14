<?php

namespace onefasteuro\ShopifyApps\Services;

use onefasteuro\ShopifyApps\Exceptions\ConfigException;
use onefasteuro\ShopifyClient\GraphResponse;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use onefasteuro\ShopifyApps\Nonce;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

class BaseService
{
    protected $config = [];
    
	//the active app that we draw config etc from
	protected $shopify_app = null;
	protected $shopify_domain = null;
    
    public function setAppConfig(array $config)
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
    	
    	foreach($params as $key)
	    {
		    if(!array_key_exists($key, $config)) {
			    throw new ConfigException('The '.$key.' key is missing from the config');
		    }
	    }
	    
    	return $config;
    }

    public function setAppHandle($app)
    {
        $this->shopify_app = $app;
        return $this;
    }

    public function setAppDomain($domain)
    {
        $this->shopify_domain = ShopifyUtils::formatDomain($domain);
        return $this;
    }

}
