<?php

namespace onefasteuro\ShopifyApps\Models;


use Illuminate\Database\Eloquent\Model;
use onefasteuro\ShopifyUtils\ShopifyUtils;

class ShopifyApp extends Model
{
	protected $table = 'shopify_apps';
	
	protected $appends = [
		'app_gid', 'app_installation_gid'
	];
	
	public function setAppInstallationIdAttribute($value)
	{
		$this->attributes['app_installation_id'] = ShopifyUtils::gidParse($value);
	}
	
	public function setAppIdAttribute($value)
	{
		$this->attributes['app_id'] = ShopifyUtils::gidParse($value);
	}
	
	public function getAppGidAttribute()
	{
		return ShopifyUtils::gidRestore($this->app_id, 'App');
	}
	
	public function getAppInstallationGidAttribute()
	{
		return ShopifyUtils::gidRestore($this->app_installation_id, 'AppInstallation');
	}
}

