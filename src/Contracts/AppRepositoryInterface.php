<?php

namespace onefasteuro\ShopifyApps\Contracts;



interface AppRepositoryInterface
{
	public function model();
	
	public function findByAppId($app_id);
}