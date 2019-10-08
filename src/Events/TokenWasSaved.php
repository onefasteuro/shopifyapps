<?php

namespace onefasteuro\ShopifyAuth\Events;


class TokenWasSaved
{
	public $token;
	
	public function __construct($token)
	{
		$this->token = $token;
	}
	
}
