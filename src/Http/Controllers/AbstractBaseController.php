<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;
use Illuminate\Config\Repository as ConfigRepository;
use onefasteuro\ShopifyClient\AdminClientInterface;

abstract class AbstractBaseController extends \Illuminate\Routing\Controller
{
	protected $repository;
	protected $config;
	
	public function __construct(ConfigRepository $config, AppRepositoryInterface $repository)
	{
		$this->repository = $repository;
		$this->config = $config;
		
		if(method_exists($this, 'init')) {
			$this->init();
		}
	}

	protected function getHttpClient($domain, $token)
	{
		return resolve(AdminClientInterface::class, ['domain' => $domain, 'token' => $token]);
	}
}
