<?php

namespace onefasteuro\ShopifyApps\Models;

use Illuminate\Database\Eloquent\Model;
use onefasteuro\ShopifyUtils\ShopifyUtils;

class ShopifyBilling extends Model
{
	protected $table = 'shopify_billings';
	
	protected $appends = ['purchase_gid'];
	
	public function bill_app()
	{
		return $this->belongsTo(ShopifyApp::class, 'app_id', 'id');
	}
	
	public function getPurchaseCompletedAttribute($value)
	{
		return $value === 1 ? true : false;
	}
	
	public function getPurchaseIdAttribute($value)
	{
		$this->attributes['purchase_id'] = ShopifyUtils::gidParse($value);
	}
	
	public function getPurchaseGidAttribute()
	{
		return '';
	}
}
