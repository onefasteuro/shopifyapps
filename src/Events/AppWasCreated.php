<?php

namespace onefasteuro\ShopifyApps\Events;


class AppWasCreated
{
	public $model;
	
	public function __construct(\onefasteuro\ShopifyApps\Models\ShopifyApp $model)
	{
		$this->model;
	}
	
}
