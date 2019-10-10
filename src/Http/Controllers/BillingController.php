<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Contracts\Events\Dispatcher as EventBus;

//models
use onefasteuro\ShopifyApps\BillingRegistry;
use onefasteuro\ShopifyApps\Models\ShopifyApp;

class BillingController extends \Illuminate\Routing\Controller
{
	
	protected $events;
	protected $registry;
	
	public function __construct(EventBus $events, BillingRegistry $registry)
	{
		$this->registry = $registry;
		$this->events = $events;
	}
	

	public function startBilling(Request $request, $id)
	{
		$model = $this->getModel($id);
		
		//get the provider
		$provider = $this->registry->get($model->app_name);
		
		//ready our provider
		$provider->init($model->shop_domain, $model->token, $model->id);
		
		
		$response = $provider->bill();
		
		//update our model and save
		$model->bill->purchase_id = $response['id'];
		$model->bill->save();
		
		//sends back to shopify so merchant can agree to not to the terms of the plan
		return redirect()->to($response['url']);
	}
	
	

	public function endBilling(Request $request, $app_name, $id)
	{
		$model = $this->getModel($id);
		
		//update the purchase status
		$model->bill->purchase_completed = true;
		$model->bill->charge_id = $request->get('charge_id');
		$model->bill->save();
		
		//get our final return URL
		$final_url = $this->registry->get($model->app_name)->getCompletedUrl();
		return redirect()->to($final_url);
		
	}
	
	protected function getModel($id)
	{
		$model = ShopifyApp::with('bill')->find((int)$id);
		
		if(!$model) {
		
		}
		
		return $model;
	}
}

