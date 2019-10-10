<?php

namespace onefasteuro\ShopifyApps\Billing;

use onefasteuro\ShopifyApps\Contracts\BillingContract;
use onefasteuro\ShopifyApps\Contracts\ModelContract;
use onefasteuro\ShopifyApps\Helpers;
use onefasteuro\ShopifyClient\GraphClient;

class RecurringBilling implements BillingContract
{
	
	public static function testCharge()
	{
		return true;
	}
	
	public static function trialDuration()
	{
		return 0;
	}
	
	public static function authorizeCharge(ModelContract $model, GraphClient $client)
	{
		$client->init($model->shop_domain, $model->token);
		
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
			'name' => Helpers::config($model->app_name, 'billing.name'),
			'trial' => static::trialDuration(),
			'return' => $model->launch_url
		]);
	}
}