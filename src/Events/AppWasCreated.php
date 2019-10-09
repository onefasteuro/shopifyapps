<?php

namespace onefasteuro\ShopifyAuth\Events;


class AppWasCreated
{
	public $model;
	
	public function __construct(\onefasteuro\ShopifyAuth\Models\ShopifyApp $model)
	{
		$this->model;
	}
	
}
