<?php

namespace onefasteuro\ShopifyApps\Http\Controllers;


//app
use Illuminate\Http\Request;
use onefasteuro\ShopifyApps\Nonce;
use onefasteuro\ShopifyApps\Repositories\AppRepositoryInterface;
use onefasteuro\ShopifyApps\Services\AuthServiceInterface;
use onefasteuro\ShopifyClient\GraphResponse;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;

//middleware
use onefasteuro\ShopifyApps\Http\Middlewares\AuthMiddleware;
use onefasteuro\ShopifyApps\Http\Middlewares\SaveNonceStoreMiddleware;
use onefasteuro\ShopifyApps\Http\Middlewares\SetNonceStoreMiddleware;
use onefasteuro\ShopifyUtils\ShopifyUtils;



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
		$this->middleware([SetNonceStoreMiddleware::class, SaveNonceStoreMiddleware::class])->only('redirectToAuth');
		$this->middleware([SetNonceStoreMiddleware::class, AuthMiddleware::class])->only('completeAuth');
	}
	
	protected function view($view, array $data = [])
	{
		return view('shopifyapps::' . $view, $data);
	}
	
	public function redirectToAuth($app_handle, $shop_domain)
	{
        $service = Container::getInstance()->make(AuthServiceInterface::class);

		$client_id = $this->config->get("shopifyapps.$app_handle.client_id");
		$scope = $this->config->get("shopifyapps.$app_handle.scope");
		$state = $this->nonce->retrieve();
		$redirect_url = route('shopify.auth.complete', ['app_handle' => $app_handle]);


		$redirect = $service->getOAuthUrl($shop_domain, $client_id, $scope, $state, $redirect_url);

		return redirect()->to($redirect);
	}
	

    public function completeAuth(Request $request, $app_handle)
    {
        $service = Container::getInstance()->make(AuthServiceInterface::class);

    	$client_id = $this->config->get("shopifyapps.$app_handle.client_id");
    	$client_secret =  $this->config->get("shopifyapps.$app_handle.client_secret");
    	$code = $request->get('code');
    	$shop = $request->get('shop');
    	
	    //exchange code for a token
		$token_response = $service->exchangeCodeForToken($shop, $code, $client_id, $client_secret);
		
		if(!$token_response->isOk()){
			return response()->make($token_response->body());
		}
		
	    //resolve our repository
	    $shopify_app = $this->saveShopifyApp($request->get('shop'), $token_response);
	    
	    return redirect()->to($shopify_app->launch_url);
    }
	
    
	/**
	 * Saves our shopify app in the data store
	 * @param $token_response
	 * @param GraphResponse $shop_info
	 * @return mixed
	 */
    protected function saveShopifyApp($domain, GraphResponse $token_response)
    {
    	$handle = ShopifyUtils::getHandleFromDomain($domain);
    	
    	return $this->repository->create(
    		$handle,
		    $token_response->body('access_token'),
		    $token_response->body('scope')
	    );
    }
}
