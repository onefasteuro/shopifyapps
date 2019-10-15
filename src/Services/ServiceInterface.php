<?php

namespace onefasteuro\ShopifyApps\Services;


interface ServiceInterface
{
	public function setAppConfig(array $config);
	
	public function setAppDomain($domain);
}