<?php

namespace onefasteuro\ShopifyApps\Services;

use onefasteuro\ShopifyApps\Exceptions\ConfigException;
use onefasteuro\ShopifyUtils\ShopifyUtils;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Support\Arr;

class BaseService implements ServiceInterface
{
    protected $config = [];
    
	//the active app that we draw config etc from
	protected $shopify_domain = null;
	
	protected $events;
	
	public function __construct(EventsDispatcher $events)
	{
		$this->events = $events;
	}
	
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
    		'app_id',
    		'client_id',
		    'client_secret',
		    'return_url',
		    'redirect_url',
		    'scope',
		    'billing'
	    ];
    	
    	foreach($params as $key)
	    {
		    if(!array_key_exists($key, $config)) {
			    throw new ConfigException('The '.$key.' key is missing from the config');
		    }
	    }
	    
    	return $config;
    }
    
    public function config($key)
    {
    	return Arr::get($this->config, $key);
    }

    public function setAppDomain($domain)
    {
        $this->shopify_domain = ShopifyUtils::formatDomain($domain);
        return $this;
    }

}
