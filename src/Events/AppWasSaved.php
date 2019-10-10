<?php

namespace onefasteuro\ShopifyApps\Events;


class AppWasSaved
{
	public $model;
	
	public function __construct(\onefasteuro\ShopifyApps\Models\ShopifyApp $model)
	{
		$this->model;
	}
	
}
