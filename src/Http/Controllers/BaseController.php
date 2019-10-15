<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;

use onefasteuro\ShopifyApps\Services\ServiceInterface;

class BaseController extends \Illuminate\Routing\Controller
{
	protected $config;
	protected $service;
	
	public function __construct(ServiceInterface $service)
	{
		$this->service = $service;
	}
}
