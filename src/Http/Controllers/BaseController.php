<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


class BaseController extends \Illuminate\Routing\Controller
{
	protected static function getConfig($name)
	{
		return config('shopifyapps.' . $name, []);
	}
}
