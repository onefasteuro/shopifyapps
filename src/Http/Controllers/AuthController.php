<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;
use onefasteuro\ShopifyApps\Services\AuthService;

//middleware
use onefasteuro\ShopifyApps\Http\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\SaveNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Http\SetNonceStoreMiddleware;

//repositories
use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;
use onefasteuro\ShopifyClient\GraphClientInterface;


class AuthController extends BaseController
{
	protected $service;
	
	public function __construct(AuthService $service)
	{
		$this->service = $service;
		
		$this->middleware([SetNonceStoreMiddleware::class, SaveNonceStoreMiddleware::class])->only('redirectToAuth');
		$this->middleware([SetNonceStoreMiddleware::class, AuthMiddleware::class])->only('completeAuth');
	}
	
	protected static function getConfig($name)
	{
		return config('shopifyapps.'.$name, []);
	}
	
	protected function view($view, array $data = [])
	{
		return view('shopifyapps::' . $view, $data);
	}

    /**
     * begin the auth process by automatically redirecting
     * @param Request $request
     * @param $appname
     * @param $shop
     * @return mixed
     */
	public function redirectToAuth($shopify_app_name, $shop)
	{
		$config = static::getConfig($shopify_app_name);
		
		$this->service->setAppHandle($shopify_app_name)
			->setAppConfig($config)
			->setAppDomain($shop);

		$redirect = $this->service->getOAuthUrl();
		
		return redirect()->to($redirect);
	}
	

    public function completeAuth(Request $request, $shopify_app_name)
    {
	    $config = static::getConfig($shopify_app_name);
	
	    //set the needed data for our service config
	    $this->service->setAppHandle($shopify_app_name)
		    ->setAppConfig($config)
		    ->setAppDomain($request->get('shop'));
	    
    	
	    //exchange code for a token
		$token_response = $this->service->exchangeCodeForToken($request->get('code'));
		
		//grab our graph client
		$client = resolve(GraphClientInterface::class, [
			'domain' => $request->get('shop'),
			'token' => $token_response->body('access_token')
		]);
		
		try {
			$shop_info = $this->service->getShopInfo($client);
			
			$app_repo = resolve(AppRepositoryInterface::class);
			
			$params = [
				$shopify_app_name,
				$token_response->body('access_token'),
				$shop_info->body('data.app'),
				$shop_info->body('data.shop')
			];
			
			$shopify_app = call_user_func_array([$app_repo, 'create'], $params);
			
			return redirect()->to($shopify_app->launch_url);
		}
		catch(\onefasteuro\ShopifyClient\Exceptions\NotReadyException $e)
		{
			abort(400, $e->getMessage());
		}
    }
}
