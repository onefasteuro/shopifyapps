<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;

//exceptions
use onefasteuro\ShopifyApps\Exceptions\GraphqlException;
use onefasteuro\ShopifyApps\Exceptions\ModelException;

//models
use onefasteuro\ShopifyApps\Helpers;
use onefasteuro\ShopifyApps\Models\ShopifyApp;

class BillingController extends AuthController
{

	public function redirectToBill(Request $request, $appname, $id)
	{
		try {
			$model = static::getModel($appname, $id);
			
			//prepare our client
			$this->client->init($model->shop_domain, $model->token);
			
			$provider = Helpers::getBillingProvider($appname);
			
			$response = call_user_func_array([$provider, 'authorizeCharge'], [$model, $this->client]);
			dd($response);
			
			/*
			$bill->purchase_id = $response['id'];
			$bill->save();
			*/
			
			//sends back to shopify so merchant can agree to not to the terms of the plan
			return redirect()->to($response['url']);
		}
		catch(ModelException $e)
		{
			abort($e->getCode(), $e->getMessage());
		}
		catch(GraphqlException $e)
		{
			dd($e);
		}
		catch(\Exception $e) {
			dd($e);
		}
	}
	
	

	public function saveTransaction(Request $request, $appname, $id)
	{
		try {
			$model = static::getModel($appname, $id);
			$this->provider->init($model);
		}
		catch(ModelException $e)
		{
			abort($e->getCode(), $e->getMessage());
		}
		
		$bill = $model->getRelation('bill');
		
		//update the purchase status
		$bill->purchase_completed = true;
		$bill->charge_id = $request->get('charge_id');
		$bill->save();
		
		return redirect()->to($bill->return_url);
		
	}
	
	protected static function getModel($appname, $id)
	{
		$model = ShopifyApp::with('bill')->where('app_name', '=', $appname)->where('id', '=', (int)$id)->first();
		
		if(!$model) {
			Throw new ModelException('Could not find the application.', 400);
		}
		
		return $model;
	}
}