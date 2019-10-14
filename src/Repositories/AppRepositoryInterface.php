<?php

namespace onefasteuro\ShopifyApps\Repositories;



interface AppRepositoryInterface
{
	public function model();
	
	public function findByAppInstallId($app_id);
}