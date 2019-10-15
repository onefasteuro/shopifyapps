<?php

namespace onefasteuro\ShopifyApps\Services;

use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use onefasteuro\ShopifyClient\GraphClientInterface;
use onefasteuro\ShopifyApps\Exceptions\ConfigException;

class BillingService extends BaseService implements ServiceInterface
{
	
	protected function validateConfig(array $config)
	{
		$config = parent::validateConfig($config);
		
		//params we need to check
		$params = [
			'test',
			'trial',
			'return_url',
			'type',
			'plans',
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
		$call =  'mutation($trial: Int, $test: Boolean, $name: String!, $return: URL!) {
			  bill: appSubscriptionCreate(
			    test: $test
			    name: $name
			    trialDays: $trial
			    returnUrl: $return
			    lineItems: [%s]
			  ) {
			    userErrors {
			      field
			      message
			    }
			    confirmationUrl
			    appSubscription {
			      id
			    }
			  }
			}';
		
		$line_items = $this->getPlan();
		
		$params = $this->getChargeParams();
		
		$call = sprintf($call, $line_items);
		
		$response = $client->query($call, $params);
		
		return $response;
	}
	
	
	protected function getChargeParams()
	{
		$params = [
			'test' => $this->config('billing.test'),
			'trial' => $this->config('billing.trial'),
			'name' => $this->config('billing.name'),
			'return' => 'https://bpisports.com',
		];
		return $params;
	}
	
	protected function getPlan()
	{
		$items = $this->config('billing.plans');
		$output = '';
		
		foreach($items as $key => $item) {
			$output .= '{
				plan: {
					appRecurringPricingDetails: {
			            price: { amount: ' . $item['amount'] . ', currencyCode: ' . $item['currency'] . ' }
			        }
				}
			}';
		}
		
		return $output;
	}
}