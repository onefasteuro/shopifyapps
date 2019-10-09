<?php

namespace onefasteuro\ShopifyAuth\Http\Controllers;


use Illuminate\Http\Request;
use onefasteuro\ShopifyAuth\Events\ModelWasSaved;
use onefasteuro\ShopifyAuth\Helpers;
use onefasteuro\ShopifyAuth\Models\ShopifyApp;
use Requests;

//constructor
use Illuminate\Contracts\Events\Dispatcher as EventBus;
use onefasteuro\ShopifyClient\GraphClient;
use onefasteuro\ShopifyAuth\Nonce;

class AuthController extends \Illuminate\Routing\Controller
{
	protected $nonce;
	protected $client;
	protected $bus;
	protected $helper;
	
	public function __construct(Nonce $nonce, GraphClient $client, EventBus $bus, Helpers $helper)
	{
		$this->client = $client;
		$this->nonce = $nonce;
		$this->bus = $bus;
		$this->helper = $helper;
	}
	
	protected function view($view, array $data = [])
	{
		return view('shopifyauth::' . $view, $data);
	}
	
	public function getBegin(Request $request, $appname, $shop)
	{
		$url = $this->helper->setAppName($appname)->getShopAuthUrl($shop);
		return redirect($url);
	}


    public function getAuth(Request $request, $appname)
    {
    	$this->helper->setAppName($appname);
    	
    	//params sent from shopify
	    $shopdomain = $request->get('shop');
    	$code = $request->get('code');
    	
    	$token_url = $this->helper->getOauthUrl($shopdomain);
    	
    	$body = [
    		'client_id' => $this->helper->getClientId(),
		    'client_secret' => $this->helper->getClientSecret(),
		    'code' => $code
	    ];
    	
    	//get the response with the oauth token
		$response = Requests::post($token_url, [], $body);
		
		if($response->status_code == 200) {
			$oauth = json_decode($response->body, true);
			
			//event, we have a token, do we need it anywhere else?
			$this->bus->dispatch(new \onefasteuro\ShopifyAuth\Events\TokenWasSaved($app->token));
			
			//save our token
			$app = $this->createShopifyAppInstance($shopdomain, $appname, $oauth);
			
			
			$query_string = [
				'shop_id' => $app->shop_id,
				'shop_domain' => $app->shop_domain,
				'app_name' => $app->app_name
			];
			
			//returns to the last auth url
			$return_url = $this->helper->getReturnUrl($query_string);
			
			return redirect()->to($return_url);

		}
		else {
			//error? handle me
			dd('error');
		}
    }
    
    protected function createShopifyAppInstance($domain, $appname, $oauth)
    {
	    //let's get the shop details
	    $gql = $this->getGraphDetails($oauth['access_token'], $domain);
	    
	    
	    //no app found
	    if(! ( $app = ShopifyApp::findInstallation($domain, $appname, $gql['app']['id']) ) ) {
	    	$app = new ShopifyApp;
	    }
	    
	    //properties necessary
	    $app->shop_name = $gql['shop']['name'];
	    $app->shop_domain = $gql['shop']['domain'];
	    $app->shop_id = Helpers::gidParse($gql['shop']['id']);
	    $app->app_installation_id = Helpers::gidParse($gql['app']['id']);
	    $app->shop_email = $gql['shop']['email'];
	    $app->app_name = $appname;
	    $app->token = $oauth['access_token'];
	    $app->scope = $oauth['scope'];
	    
	    $app->save();
	    
	    $this->bus->dispatch(new ModelWasSaved($app));
	    
	    return $app;
    }
	
	
	/**
	 * Fetches a bit more details about this shop from Shopify
	 * @param $token
	 * @param $shop
	 * @return mixed
	 */
    protected function getGraphDetails($token, $shop)
    {
        $this->client->init($shop, $token);
	
	    $call = 'query {
			  app: appInstallation {
			    id
			    launchUrl
			    uninstallUrl
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
