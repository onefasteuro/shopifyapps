<?php

namespace onefasteuro\ShopifyApps\Contracts;



interface BillingContract
{
	
	public function parseResponse(array $response);
	
	public function bill(ModelContract $model);
}