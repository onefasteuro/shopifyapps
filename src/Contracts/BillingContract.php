<?php

namespace onefasteuro\ShopifyApps\Contracts;



interface BillingContract
{

	public function getName();
	
	public function getTest();
	
	public function getReturnUrl();
	
	public function getCompletedUrl();
	
	public function parseResponse(array $response);
	
	public function bill();
}