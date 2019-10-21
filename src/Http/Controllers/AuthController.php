<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


//app
use Illuminate\Http\Request;
use onefasteuro\ShopifyApps\Nonce;
use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;
use Illuminate\Config\Repository as ConfigRepository;
use onefasteuro\ShopifyApps\Services\AuthService;
use onefasteuro\ShopifyClient\AdminClientInterface;

//middleware
use onefasteuro\ShopifyApps\Http\Middlewares\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\Middlewares\SaveNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Http\Middlewares\SetNonceStoreMiddleware;



class AuthController extends AbstractBaseController
{
	protected $nonce;
	
	public function __construct(ConfigRepository $config, AppRepositoryInterface $repository, Nonce $nonce)
	{
		$this->nonce = $nonce;
		parent::__construct($config, $repository);
	}
	
	protected function init()
	{
		//sets up the required middlewares
		$this->middleware([SetNonceStoreMiddleware::class, SaveNonceStoreMiddleware::class])->only('redirectToAuth');
		$this->middleware([SetNonceStoreMiddleware::class, AuthMiddleware::class])->only('completeAuth');
	}
	
	protected function view($view, array $data = [])
	{
		return view('shopifyapps::' . $view, $data);
	}
	
	public function redirectToAuth($app_handle, $shop_domain)
	{
		$client_id = $this->config->get("shopifyapps.$app_handle.client_id");
		$scope = $this->config->get("shopifyapps.$app_handle.scope");
		$state = $this->nonce->retrieve();
		$redirect_url = route('shopify.auth.complete', ['app_handle' => $app_handle]);


		$redirect = AuthService::getOAuthUrl($shop_domain, $client_id, $scope, $state, $redirect_url);

		return redirect()->to($redirect);
	}
	

    public function completeAuth(Request $request, $app_handle)
    {
    	$client_id = $this->config->get("shopifyapps.$app_handle.client_id");
    	$client_secret =  $this->config->get("shopifyapps.$app_handle.client_secret");
    	$code = $request->get('code');
    	$shop = $request->get('shop');
    	
	    //exchange code for a token
		$token_response = AuthService::exchangeCodeForToken($shop, $code, $client_id, $client_secret);
		
		if(!$token_response->isOk()){
			return response()->make($token_response->getBody());
		}
		
		$graph_data = $this->getAppInfo($shop, $token_response->getBody('access_token'));
		
		
	    //resolve our repository
	    $shopify_app = $this->repository->create($token_response->getBody('access_token'),
		    $graph_data->getBody('data.install.app.id'),
	        $graph_data->getBody('data.install.id'),
	        $graph_data->getBody('data.shop.myshopifyDomain')
	    );
		
	    return redirect()->to($graph_data->getBody('data.install.launchUrl'));
    }
    
    
    protected function getAppInfo($domain, $token)
    {
	    $call = '{
    	    install: appInstallation {
    	        id
    	        launchUrl
    	        app {
    	            id
    	        }
    	    }
    	    shop {
    	        myshopifyDomain
    	    }
    	}';
	    
    	
    	return $this->getHttpClient($domain, $token)->query($call);
    }
}
