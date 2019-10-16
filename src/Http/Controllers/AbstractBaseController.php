<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;
use onefasteuro\ShopifyApps\Services\ServiceInterface;

abstract class AbstractBaseController extends \Illuminate\Routing\Controller
{
	protected $repository;
	
	public function __construct(AppRepositoryInterface $repository)
	{
		$this->repository = $repository;
		
		$this->init();
	}
	
	abstract protected function init();
	
	abstract protected function getService($app_id);
}
