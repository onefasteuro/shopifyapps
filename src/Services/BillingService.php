<?php

namespace onefasteuro\ShopifyApps\Services;

use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use onefasteuro\ShopifyClient\GraphClient;

class BillingService extends BaseService implements BillingInterface
{
	protected $events;
	protected $client;
	
	public function __construct(GraphClient $client, EventsDispatcher $events)
	{
		$this->events = $events;
		$this->client = $client;
	}
	
	public static function authorizeCharge(GraphClient $client, $return_url)
	{
		$call =  'mutation($trial: Int, $test: Boolean, $name: String!, $return: URL!) {
			  bill: appSubscriptionCreate(
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