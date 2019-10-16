<?php

namespace onefasteuro\ShopifyApps\Services;

use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use onefasteuro\ShopifyApps\Exceptions\MissingChargeFormatException;
use onefasteuro\ShopifyClient\GraphClientInterface;
use onefasteuro\ShopifyApps\Exceptions\ConfigException;

class BillingService extends AbstractBaseService implements ServiceInterface
{
	
	const BILLING_SUBSCRIPTION = 'subscription';
	const BILLING_ONE_TIME = 'onetime';
	
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
			'type',
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
		$type = $this->config('billing.type');
		
		$method = 'get' . ucfirst($type) . 'Query';
		
		if(!method_exists(__CLASS__, $method)) {
			throw new MissingChargeFormatException('There is no charge of type: '.$type);
		}
		
		$call = static::$method();
		
		$line_items = $this->getPlan();
		
		$params = $this->getChargeParams();
		
		$call = sprintf($call, $line_items);
		
		$response = $client->query($call, $params);
		
		return $response;
	}
	
	protected static function getSubscriptionQuery()
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
			    transaction: appSubscription {
			      id
			    }
			  }
			}';
		
		return $call;
	}
	
	
	protected function getOnetimeQuery()
	{
		$call =  'mutation($trial: Int, $test: Boolean, $price: MoneyInput!, $name: String!, $return: URL!) {
			  bill: appPurchaseOneTimeCreate(
			    test: $test
			    name: $name
			    price: $price
			    trialDays: $trial
			    returnUrl: $return
			  ) {
			    userErrors {
			      field
			      message
			    }
			    confirmationUrl
			    transaction: appPurchaseOneTime {
			      id
			    }
			  }
			}';
		
		return $call;
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