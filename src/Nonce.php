<?php

namespace onefasteuro\ShopifyApps;

use Illuminate\Support\Facades\Session;
use Illuminate\Session\Store;

class Nonce
{

    /**
     * Default clock skew, i.e. how long in the past we're willing to allow for.
     *
     * @var int
     * @see validate()
     */
    protected $clockSkew = 18000;
    protected $session;
    
    const STORE_KEY = 'shopify_nonce';

    /**
     * Sets the OP endpoint URL, and optionally the clock skew and custom storage
     * driver.
     *
     * @param string $opEndpointURL OP Endpoint URL
     * @param int    $clockSkew     How many seconds old can a
     *                               nonce be?
     *
     * @return void
     */
    public function __construct($clockSkew = null)
    {
        if ($clockSkew) {
            $this->clockSkew = $clockSkew;
        }
    }

    
    public function setStore(Store $s)
    {
    	$this->session = $s;
    	return $this;
    }


    /**
     * Validates the syntax of a nonce, as well as checks to see if its timestamp is
     * within the allowed clock skew
     *
     * @param mixed $nonce The nonce to validate
     *
     * @return bool true on success, false on failure
     * @see $clockSkew
     */
    public function validate($nonce)
    {
        if (strlen($nonce) > 255) {
            return false;
        }

        $result = preg_match('/(\d{4})-(\d\d)-(\d\d)T(\d\d):(\d\d):(\d\d)Z(.*)/',
            $nonce,
            $matches);
        if ($result != 1 || count($matches) != 8) {
            return false;
        }

        $stamp = gmmktime($matches[4],
            $matches[5],
            $matches[6],
            $matches[2],
            $matches[3],
            $matches[1]);

        $time = time();
        if ($stamp < ($time - $this->clockSkew)
            || $stamp > ($time + $this->clockSkew)) {

            return false;
        }

        return true;
    }

    /**
     * Creates a nonce, but does not store it.  You may specify the lenth of the
     * random string, as well as the time stamp to use.
     *
     * @param int $length Lenth of the random string, defaults to 6
     * @param int $time   A unix timestamp in seconds
     *
     * @return string The nonce
     * @see createNonceAndStore()
     */
    public function create($length = 6, $time = null)
    {
        $time = ($time === null) ? time() : $time;

        $nonce = gmstrftime('%Y-%m-%dT%H:%M:%SZ', $time);
        if ($length < 1) {
            return $nonce;
        }

        $length = (int) $length;
        $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $chars .= 'abcdefghijklmnopqrstuvwxyz';
        $chars .= '1234567890';

        $unique = '';
        for ($i = 0; $i < $length; $i++) {
            $unique .= substr($chars, (rand() % (strlen($chars))), 1);
        }

        return $nonce . $unique;
    }

	public function createAndSave($length = 6, $time = null)
	{
		if($this->session === null) {
			throw new \Exception('Session store not set on the library');
		}
		
		$nonce = $this->create($length, $time);
		$this->save($nonce);
		
		return $nonce;
	}
	
	protected function save($nonce)
	{
		$this->session->put(static::STORE_KEY, $nonce);
	}
	
	public function retrieve()
	{
		return $this->session->get(static::STORE_KEY);
	}
}