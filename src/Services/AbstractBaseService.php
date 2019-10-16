<?php

namespace onefasteuro\ShopifyApps\Services;

use onefasteuro\ShopifyApps\Exceptions\ConfigException;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Support\Arr;

abstract class AbstractBaseService implements ServiceInterface
{
    protected $app_config = [];
	protected $events;
	
	public function __construct(EventsDispatcher $events)
	{
		$this->events = $events;
	}
	
	public function setAppConfig(array $config)
	{
		$config = $this->validateConfig($config);
		$this->app_config = $config;
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
			    throw ConfigException::factory($key);
		    }
	    }
	    
    	return $config;
    }
    
    public function config($key)
    {
    	return Arr::get($this->app_config, $key);
    }
}
