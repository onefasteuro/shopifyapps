<?php

namespace onefasteuro\ShopifyApps\Services;

use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use onefasteuro\ShopifyClient\GraphClientInterface;
use onefasteuro\ShopifyApps\Exceptions\ConfigException;

class BillingService extends BaseService
{
	
	protected function validateConfig(array $config)
	{
		$config = parent::validateConfig($config);
		
		//params we need to check
		$params = [
			'test',
			'trial',
			'return_url',
		];
		
		foreach($params as $key)
		{
			if(!array_key_exists($key, $config['billing'])) {
				throw new ConfigException('The '.$key.' key is missing from the config');
			}
		}
		
		return $config;
	}
	
	
	public function authorizeCharge(GraphClientInterface $client)
	{
		$trial = $this->config('billing.trial');
		$test = $this->config('billing.test');
		$type = $this->config('billing.type');
		
		
	}
}