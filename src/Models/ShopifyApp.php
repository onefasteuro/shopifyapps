<?php

namespace onefasteuro\ShopifyApps\Models;


use Illuminate\Database\Eloquent\Model;
use onefasteuro\ShopifyUtils\ShopifyUtils;

class ShopifyApp extends Model
{
	protected $table = 'shopify_apps';
	
	//appends to the toArray output
	protected $appends = ['shop_gid', 'app_installation_gid'];
	

	
	public function bill()
	{
		return $this->hasOne(ShopifyBilling::class, 'app_id','id')->withDefault();
	}
	
	public function setAppIdAttribute($value)
	{
		$this->attributes['app_id'] = ShopifyUtils::gidParse($value);
	}
	
	public function setShopIdAttribute($value)
	{
		$this->attributes['shop_id'] = ShopifyUtils::gidParse($value);
	}
	
	public function setAppInstallationIdAttribute($value)
	{
		$this->attributes['app_installation_id'] = ShopifyUtils::gidParse($value);
	}
	
	public function getFqdnAttribute()
	{
		if(!preg_match('/http\:\/\//', $this->shop_domain)) {
			return sprintf('https://%s', $this->shop_domain);
		}
		
		return $this->shop_domain;
	}
	
	public function getShopGidAttribute()
	{
		return ShopifyUtils::gidRestore($this->shop_id, 'Shop');
	}
	
	public function getAppInstallationGidAttribute()
	{
		return ShopifyUtils::gidRestore($this->app_installation_id, 'AppInstallation');
	}
	
	public function getLaunchUrlAttribute()
	{
		return $this->app_launch_url;
	}

	public function getBillingProviderAttribute()
    {
        return ShopifyUtils::getBillingProvider($this->app_name);
    }
    

	public function updateBillingPurchaseId($id)
    {
        $this->bill->purchase_id = $id;
        $this->bill->save();

        return $this;
    }
}

