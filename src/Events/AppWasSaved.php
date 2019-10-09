<?php

namespace onefasteuro\ShopifyAuth\Events;


class AppWasSaved
{
	public $model;
	
	public function __construct(\onefasteuro\ShopifyAuth\Models\ShopifyApp $model)
	{
		$this->model;
	}
	
}
