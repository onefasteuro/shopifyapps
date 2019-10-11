<?php

namespace onefasteuro\ShopifyApps\Contracts;


use onefasteuro\ShopifyClient\GraphClient;

interface BillingContract
{
	
	public static function authorizeCharge(GraphClient $client, $return_url);

    public function appName();

    public static function name();

    public static function testCharge();

    public static function trialDuration();


}