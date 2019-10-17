<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class WebhooksController extends Controller
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
