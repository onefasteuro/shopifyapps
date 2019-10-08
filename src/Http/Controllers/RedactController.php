<?php

namespace onefasteuro\ShopifyAuth\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

//models
use onefasteuro\ShopifyAuth\ShopifyApp;
use onefasteuro\ShopifyAuth\ShopifyBilling;

class RedactController extends Controller
{
	
	public function customer()
	{
		return response()->json([], 200);
	}
	
	public function shop()
	{
		
		
		return response()->json([], 200);
	}
}
