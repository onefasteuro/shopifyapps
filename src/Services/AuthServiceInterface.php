<?php
	
	
	namespace onefasteuro\ShopifyApps\Services;
	
	/**
	 * Created by PhpStorm.
	 * User: oliverharoun
	 * Date: 10/16/19
	 * Time: 4:42 PM
	 */
	
	interface AuthServiceInterface
	{
		public function getOAuthUrl($shop_domain, $client_id, $scope, $state, $redirect_url);
		
		public function exchangeCodeForToken($domain, $code, $client_id, $client_secret);
	}