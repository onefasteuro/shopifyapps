<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;
use onefasteuro\ShopifyApps\Events\AppWasCreated;
use onefasteuro\ShopifyApps\Events\AppWasSaved;

use onefasteuro\ShopifyApps\Models\ShopifyApp;
use onefasteuro\ShopifyApps\Auth\ShopifyAuthService;

//middleware
use onefasteuro\ShopifyApps\Http\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\SaveNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Http\SetNonceStoreMiddleware;

//repositories
use onefasteuro\ShopifyApps\Repositories\AppRepository;
use onefasteuro\ShopifyApps\Repositories\GraphqlRepository;


class AuthController extends \Illuminate\Routing\Controller
{
	protected $service;
	
	public function __construct(ShopifyAuthService $service)
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
		
		$this->service->setShopifyApp($shopify_app_name)
			->setShopifyAppConfig($config)
			->setShopifyDomain($shop);

		$redirect = $this->service->getOAuthUrl();
		
		return redirect()->to($redirect);
	}
	

    public function completeAuth(Request $request, $shopify_app_name)
    {
	    $config = static::getConfig($shopify_app_name);
	    
	    $this->service->setShopifyApp($shopify_app_name)
		    ->setShopifyAppConfig($config)
		    ->setShopifyDomain($request->get('shop'));
	    
    	
	    //exchange code for a token
		$token_response = $this->service->exchangeCodeForToken($request->get('code'));
		
		$graph_repo = resolve(GraphqlRepository::class, ['domain' => $request->get('shop'), 'token' => $token_response->body('access_token') ]);
		$shop_info = $graph_repo->getShopInfo();
		
		dd($shop_info);
		
		$app_repo = resolve(AppRepository::class);
		
		$app_repo->create($token_response->body('access_token'), $shop_info->body('data.app'), $shop_info->body('data.shop'));
    }
	
	/**
	 * Creates a new model instance of our app, getting necessary details from API
	 * @param $domain
	 * @param $appname
	 * @param $oauth
	 * @return mixed|ShopifyApp
	 */
    protected function createShopifyAppInstance(array $gql, array $oauth)
    {
	    //no app found
        $app = ShopifyApp::findInstallation($gql['app']['id']);
        $created = false;
        if($app === null) {
            $app = new ShopifyApp;
            $app->app_installation_id = $gql['app']['id'];
            $app->shop_id = $gql['shop']['id'];
            $created = true;
        }

        //app necessary properties
        $app->app_name = $gql['app']['current']['handle'];
        $app->app_launch_url = $gql['app']['launchUrl'];

	    //shop necessary properties
	    $app->shop_name = $gql['shop']['name'];
	    $app->shop_domain = $gql['shop']['domain'];
	    $app->shop_email = $gql['shop']['email'];

	    //oauth data
	    $app->token = $oauth['access_token'];
	    $app->scope = $oauth['scope'];

	    //save our model
	    $app->save();

	    if($created === true) {
            $this->events->dispatch(new AppWasCreated($app));
        }
	    
        $this->events->dispatch(new AppWasSaved($app));

	    return $app;
    }

}
