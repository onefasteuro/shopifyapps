<?php

namespace onefasteuro\ShopifyApps\Services;

use onefasteuro\ShopifyApps\Exceptions\MissingChargeFormatException;
use onefasteuro\ShopifyClient\GraphClientInterface;


class BillingService implements BillingServiceInterface
{
	const BILLING_SUBSCRIPTION = 'subscription';
	const BILLING_ONE_TIME = 'onetime';
	
	protected $repository;
	
	public function __construct(GraphClientInterface $client)
	{
		$this->repository = $client;
	}
	
	protected function getLaunchUrl()
	{
		$shop_call = '{
		app: appInstallation {
		    launchUrl
		  }
		}';
		
		$response = $this->repository->query($shop_call);
		
		return $response->body('data.app.launchUrl');
	}
	
	protected static function assertChargeMethod($type)
	{
		return 'authorize' . ucfirst($type);
	}
	
	public function authorizeCharge(array $billing_config)
	{
		$method = static::assertChargeMethod($billing_config['type']);
		
		if(!method_exists(__CLASS__, $method)) {
			throw MissingChargeFormatException::factory($billing_config['type']);
		}
		
		//go back to the launch url
		$redirect_url = $this->getLaunchUrl();
		
		$response = static::$method($this->graph_client, $redirect_url, $billing_config);
		
		dd($response);
		return $response;
	}
	
	protected static function authorizeSubscription(GraphClientInterface $client, $redirect_url, array $billing_config = [])
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
		
		$params = [
			'test' => $billing_config['test'],
			'trial' => $billing_config['trial'],
			'return' => $redirect_url,
			'name' => $billing_config['name'],
		];
		
		return $client->query($call, $params);
	}
	
	
	protected function authorizeOnetime(GraphClientInterface $client, $redirect_url, array $billing_config = [])
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
		
		return $client->query($call, $params);
	}
}