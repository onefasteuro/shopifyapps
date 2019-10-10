<?php

namespace onefasteuro\ShopifyApps\Contracts;


use onefasteuro\ShopifyClient\GraphClient;

interface BillingContract
{
	
	public static function authorizeCharge(GraphClient $client, $return_url);
	
	public static function testCharge();
	
	public static function trialDuration();
}