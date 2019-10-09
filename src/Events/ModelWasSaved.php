<?php

namespace onefasteuro\ShopifyAuth\Events;


class ModelWasSaved
{
	public $model;
	
	public function __construct(\onefasteuro\ShopifyAuth\Models\ShopifyApp $model)
	{
		$this->model;
	}
	
}
