<?php

namespace onefasteuro\ShopifyApps\Billing;

use onefasteuro\ShopifyApps\Contracts\BillingContract;
use onefasteuro\ShopifyApps\Contracts\ModelContract;
use onefasteuro\ShopifyApps\Helpers;
use onefasteuro\ShopifyClient\GraphClient;

class RecurringBilling implements BillingContract
{
    public static function name()
    {
        return 'GP Bridge sync';
    }
	
	public static function testCharge()
	{
		return true;
	}
	
	public static function trialDuration()
	{
		return 0;
	}
	
	public static function authorizeCharge(GraphClient $client, $return_url)
	{
		$call =  'mutation($trial: Int, $test: Boolean, $name: String!, $return: URL!) {
			  appSubscriptionCreate(
			    test: $test
			    name: $name
			    trialDays: $trial
			    returnUrl: $return
			    lineItems: [{
			      plan: {
			        appRecurringPricingDetails: {
			            price: { amount: 500.00, currencyCode: USD }
			        }
			      }
			    }]
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
		
		
		return $client->query($call, [
			'test' => static::testCharge(),
			'name' => static::name(),
			'trial' => static::trialDuration(),
			'return' => $return_url,
		]);
	}
}