<?php

namespace onefasteuro\ShopifyApps\Repositories;



interface AppRepositoryInterface
{
	public function model();
	
	public function find($id);
	
	public function findByInstallationId($id);
	
	public function create($token, $app_id, $app_installation_id, $domain);
}