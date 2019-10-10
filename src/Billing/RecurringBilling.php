<?php

namespace onefasteuro\ShopifyApps\Billing;


class RecurringBilling extends BaseBilling
{
	
	/**
	 * Trial time, in days
	 * @return int
	 */
	public function getTrial()
	{
		return $this->config['trial'];
	}
	
	public function bill()
	{
		$call = 'mutation($trial: Int, $test: Boolean, $name: String!, $return: URL!) {
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
		
		
		$response =  $this->client->query($call, [
				"trial" => $this->getTrial(),
				"test" => $this->getTest(),
				"name"  => $this->getName(),
				"return" => $this->getReturnUrl(),
			]
		);
		
		$output = $this->parseResponse($response);
		
		//no proper output?
		if(!$output) {
			//TODO: error
		}
		
		return $output;
	}
	
	
	public function parseResponse(array $r)
	{
		if(array_key_exists('data', $r)) {
			$url = $r['data']['appSubscriptionCreate']['confirmationUrl'];
			$sub_id = $r['data']['appSubscriptionCreate']['appSubscription']['id'];
			
			return ['url' => $url, 'id' => $sub_id];
		}
		
		return false;
	}
	
	
}