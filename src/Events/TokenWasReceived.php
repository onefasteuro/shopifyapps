<?php

namespace onefasteuro\ShopifyApps\Events;


class TokenWasReceived
{
	public $token;
	
	public function __construct($token)
	{
		$this->token = $token;
	}
	
}
