<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


//app
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use onefasteuro\ShopifyApps\Services\AuthService;
use onefasteuro\ShopifyClient\GraphClientInterface;
use onefasteuro\ShopifyClient\GraphResponse;

//middleware
use onefasteuro\ShopifyApps\Http\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\SaveNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Http\SetNonceStoreMiddleware;



class AuthController extends AbstractBaseController
{
	protected function getService($app_id)
	{
		//get the right config file
		$config = Config::get("shopifyapps.app_$app_id");
		return App::makeWith(AuthService::class, ['config' => $config]);
	}
	
	protected function init()
	{
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
	public function redirectToAuth($app_id, $shop_domain)
	{
		$redirect = $this->getService($app_id)->getOAuthUrl($shop_domain);
		
		
		return redirect()->to($redirect);
	}
	

    public function completeAuth(Request $request, $app_id)
    {
    	$service = $this->getService($app_id);
    	
	    //exchange code for a token
		$token_response = $service->exchangeCodeForToken($request->get('shop'), $request->get('code'));
		
		//grab our graph client
		$client = resolve(GraphClientInterface::class, [
			'domain' => $request->get('shop'),
			'token' => $token_response->body('access_token')
		]);
	
	    //get the shop info to save in our db
	    $shop_info = $service->getShopInfo($client);
	
	    //resolve our repository
	    $shopify_app = $this->saveShopifyApp($token_response, $shop_info);
	    
	    return redirect()->to($shopify_app->launch_url);
    }
	
	
	/**
	 * Saves our shopify app in the data store
	 * @param $token_response
	 * @param GraphResponse $shop_info
	 * @return mixed
	 */
    protected function saveShopifyApp($token_response, GraphResponse $shop_info)
    {
    	return $this->repository->create(
    		$token_response->body('access_token'),
		    $shop_info->body('data.app'),
		    $shop_info->body('data.shop')
	    );
    }
}
