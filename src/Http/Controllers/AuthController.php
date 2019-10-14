<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


use Illuminate\Http\Request;
use onefasteuro\ShopifyApps\Events\AppWasCreated;
use onefasteuro\ShopifyApps\Events\AppWasSaved;
use onefasteuro\ShopifyApps\Helpers;
use onefasteuro\ShopifyApps\Http\SaveNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Http\SetNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Models\ShopifyApp;
use onefasteuro\ShopifyApps\Auth\ShopifyAuthService;
use Requests;

//constructor
use Illuminate\Contracts\Events\Dispatcher as EventBus;
use onefasteuro\ShopifyClient\GraphClient;
use onefasteuro\ShopifyApps\Nonce;

class AuthController extends \Illuminate\Routing\Controller
{
	protected $service;
	
	public function __construct(ShopifyAuthService $service)
	{
		$this->service = $service;
		
		$this->middleware([SetNonceStoreMiddleware::class, SaveNonceStoreMiddleware::class])->only('redirectToAuth');
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
	public function redirectToAuth(Request $request, $shopify_app_name, $shop)
	{
		$config = config('shopifyapps.'.$shopify_app_name);
		
		$this->service->setShopifyAppConfig($config);
        $this->service->setShopifyDomain($shop);



	}

	
	public function getAuthUrl(Request $request, $appname, $shop)
	{
		$url = '';
		return $url;
	}

    public function getAuth(Request $request, $appname)
    {
    	//params sent from shopify
	    $shopdomain = $request->get('shop');
    	$code = $request->get('code');
    	
    	$token_url = $this->helper->getOauthUrl($shopdomain);
    	
    	$body = [
    		'client_id' => Helpers::config($appname, 'client_id'),
		    'client_secret' => Helpers::config($appname, 'client_secret'),
		    'code' => $code
	    ];
    	
    	//get the response with the oauth token
		$response = Requests::post($token_url, [], $body);
		
		if($response->status_code == 200) {
			$oauth = json_decode($response->body, true);
			
			//event, we have a token, do we need it anywhere else?
			$this->events->dispatch(new \onefasteuro\ShopifyApps\Events\TokenWasReceived($oauth['access_token']));

			//get the graphql details for this app
            $gql = $this->getGraphDetails($shopdomain, $oauth['access_token']);

			//save our token
			$app = static::createShopifyAppInstance($gql, $oauth);
			
			//return to that URL
			return redirect()->to($app->return_url);

		}
	
	    dd('error');
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
	


	/**
	 * Fetches a bit more details about this shop from Shopify
	 * @param $token
	 * @param $shopdomain
	 * @return mixed
	 */
    protected function getGraphDetails($shopdomain, $token)
    {
        $this->client->init($shopdomain, $token);
	
	    $call = 'query {
			  app: appInstallation {
			    id
			    launchUrl
			    uninstallUrl
			    current: app {
			        handle
			    }
			  }
			  shop {
			    id
			    name
			    email
			    domain: myshopifyDomain
			  }
			}';
	
	    $r =  $this->client->query($call, []);
	    
	    if(array_key_exists('data', $r)){
	    	return $r['data'];
	    }
	    else {
	    	//error? handle me
	    }
    }
    
    

}
