<?php

namespace onefasteuro\ShopifyApps\Contracts;


use onefasteuro\ShopifyClient\GraphClient;

interface BillingContract
{
	
	public static function authorizeCharge(ModelContract $model, GraphClient $client);
	
	public static function testCharge();
	
	public static function trialDuration();
}