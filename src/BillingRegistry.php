<?php

namespace onefasteuro\ShopifyApps;

use onefasteuro\ShopifyApps\Contracts\BillingContract;

class BillingRegistry
{
	protected $providers = [];
	
	public function register($name, BillingContract $c)
	{
		$this->providers[$name] = $c;
		return $this;
	}
	
	public function get($name)
	{

		if (array_key_exists($name, $this->providers)) {
			return $this->providers[$name];
		} else {
			throw new \Exception("Invalid provider: ".$name);
		}
	}
}
