<?php

namespace onefasteuro\ShopifyApps\Services;

use onefasteuro\ShopifyApps\Exceptions\ConfigException;
use onefasteuro\ShopifyApps\Exceptions\MissingChargeFormatException;
use onefasteuro\ShopifyClient\AdminClientInterface;

class BillingService
{
	const BILLING_SUBSCRIPTION = 'subscription';
	const BILLING_ONE_TIME = 'onetime';
	
	
	
	protected static function validateParams(array $params)
	{
		$verif = [
			'trial',
			'test',
			'name',
			'type',
			'plans'
		];
		
		foreach($verif as $value) {
			if(!array_key_exists($value, $params)) {
				throw ConfigException::factory($value);
			}
		}
		
		if($params['type'] != static::BILLING_SUBSCRIPTION or $params['type'] != static::BILLING_ONE_TIME) {
			throw MissingChargeFormatException::factory($params['type']);
		}
		
		return true;
	}
	
	
	protected static function assertChargeMethod($type)
	{
		return 'authorize' . ucfirst($type);
	}
	
	
	public static function authorizeCharge(AdminClientInterface $client, array $billing_config)
	{
		$method = static::assertChargeMethod($billing_config['type']);
		
		$query_call = static::$method($billing_config);
		
		$query_params = [
			'test' => $billing_config['test'],
			'trial' => $billing_config['trial'],
			'return' => $billing_config['return_url'],
			'name' => $billing_config['name'],
		];
		
		return $client->query($query_call, $query_params);
	}
	
	protected static function authorizeSubscription(array $billing_config)
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
		
		
		$output = '';
		
		foreach($billing_config['plans'] as $key => $item) {
			$output .= '{
				plan: {
					appRecurringPricingDetails: {
			            price: { amount: ' . $item['amount'] . ', currencyCode: ' . $item['currency'] . ' }
			        }
				}
			}';
		}
		
		$call = sprintf($call, $output);
		
		return $call;
	}
	
	
	protected function authorizeOnetime(array $billing_config = [])
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
}