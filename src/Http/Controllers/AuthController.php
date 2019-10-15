<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;
use onefasteuro\ShopifyApps\Services\ServiceInterface as AuthService;

//middleware
use onefasteuro\ShopifyApps\Http\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\SaveNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Http\SetNonceStoreMiddleware;

//repositories
use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;
use onefasteuro\ShopifyClient\GraphClientInterface;
use Symfony\Component\HttpKernel\Client;


class AuthController extends BaseController
{
	
	public function __construct(AuthService $service)
	{
		parent::__construct($service);
		
		$this->middleware([SetNonceStoreMiddleware::class, SaveNonceStoreMiddleware::class])->only('redirectToAuth');
		$this->middleware([SetNonceStoreMiddleware::class, AuthMiddleware::class])->only('completeAuth');
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
	public function redirectToAuth($app_id, $shop)
	{
		$config = shopifyAppsConfig($app_id);
		
		$this->service->setAppConfig($config)
			->setAppDomain($shop);

		$redirect = $this->service->getOAuthUrl();
		
		return redirect()->to($redirect);
	}
	

    public function completeAuth(Request $request, $app_id)
    {
	    $config = shopifyAppsConfig($app_id);
	
	    //set the needed data for our service config
	    $this->service->setAppConfig($config)
		    ->setAppDomain($request->get('shop'));
	    
    	
	    //exchange code for a token
		$token_response = $this->service->exchangeCodeForToken($request->get('code'));
		
		//grab our graph client
		$client = resolve(GraphClientInterface::class, [
			'domain' => $request->get('shop'),
			'token' => $token_response->body('access_token')
		]);
		
		try {
			//get the shop info to save in our db
			$shop_info = $this->service->getShopInfo($client);
			
			//resolve our repository
			$app_repo = resolve(AppRepositoryInterface::class);
			
			$params = [
				$token_response->body('access_token'),
				$shop_info->body('data.app'),
				$shop_info->body('data.shop')
			];
			
			//persist a new shopify app
			$shopify_app = call_user_func_array([$app_repo, 'create'], $params);
			
			return redirect()->to($shopify_app->launch_url);
		}
		catch(\onefasteuro\ShopifyClient\Exceptions\NotReadyException $e)
		{
			abort(400, $e->getMessage());
		}
    }
}
