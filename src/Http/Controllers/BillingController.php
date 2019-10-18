<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Container\Container;


class BillingController extends AbstractBaseController
{
	/**
	 * Redirects the user to their shopify store to authorize or decline the charge
	 * @param $app_installation_id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function redirectToBill($app_handle, $id)
	{
		$shopify_app = $this->repository->find($id);
		
		$params = [
			'domain' => $shopify_app->myshopify_domain,
			'token' => $shopify_app->token,
		];
		$service = Container::getInstance()->makeWith('shopifyapps.billing.service', $params);
		
		
		$response = $service->authorizeCharge( $this->config->get("shopifyapps.$app_handle.billing") );
		
		//go to the authorization code
		return redirect()->to($response->body('data.bill.confirmationUrl'));
	}
	
	
	
	public function completeBilling($app_handle, $id)
	{
		$model = $shopify_app = $this->repository->find($id);
		
		if(!$model) {
			//TODO
		}
	}
	
	
	public function handleWebhooks()
	{
	
	}
}