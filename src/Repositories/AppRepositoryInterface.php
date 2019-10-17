<?php

namespace onefasteuro\ShopifyApps\Repositories;



interface AppRepositoryInterface
{
	public function model();
	
	public function findByToken($token);
}