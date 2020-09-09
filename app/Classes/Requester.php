<?php
 
namespace App\Classes;
 
use \Curl\Curl;
use Illuminate\Support\Facades\Redis;

class Requester   
{
	protected $curl;  
	protected $last_http_code;
	protected $body;

	 public function __construct()
	 {
	 	$this->curl = new Curl();
	 }

	 private function parseResponse()
	 {
	 	$this->last_http_code = $this->curl->httpStatusCode;
	 	if(!$this->curl->error)
        {             	
        	
        	$rate_limit = $this->curl->responseHeaders['X-RateLimit-Limit']; 
        	$rate_remaining = $this->curl->responseHeaders['X-RateLimit-Remaining'];     
        	$rate_reset = $this->curl->responseHeaders['X-RateLimit-Reset'];

        	if($rate_remaining!==null) Redis::set("remaining",$rate_remaining);
        	if($rate_reset!==null) Redis::set("reset",$rate_reset); 
        	if($rate_limit!==null) Redis::set("limit",$rate_limit); 
        	
        	Redis::incr("nbrequest");

            $this->body = $this->curl->rawResponse; 
        }
        else
        {
        	dd($this->curl);
        	$this->body=FALSE;
        }
	 }

	 public function sendRequest($url,$with_pubg_key = true)
	 {
	 	if($with_pubg_key)
	 	{
	 		$key = env("PUBG_KEY",null);
	 		$this->curl->setHeader('Authorization', 'Bearer ' . $key);
			$this->curl->setHeader('Accept', 'application/vnd.api+json');
	 	}	 	
	 	$this->curl->setOpt(CURLOPT_ENCODING , 'gzip');
		$this->curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
		$this->curl->setOpt(CURLOPT_FOLLOWLOCATION, TRUE);
		$this->curl->setOpt(CURLOPT_TIMEOUT, 45);
		$this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
		$this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0); 
		$this->curl->get($url);
		$this->parseResponse();		 
	 }

	 public function getNbRequest()
	 {
	 	return Redis::incr("nbrequest");
	 }

	 public function getBody($jdecode = false)
	 {
	 	if($jdecode)
	 	{
	 		return json_decode($this->body);
	 	}
	 	else
	 	{
	 		return $this->body;
	 	}
	 	
	 }

	 public function isResponseOk()
	 {
	 	return ($this->getLastHttpCode()===200);
	 }

	 public function getLastHttpCode()
	 {
	 	return $this->last_http_code;
	 }

	 public function getNextReset()
	 {
	 	return self::NextReset();
	 }

	 public function getRemainingRequest()
	 {
	 	return self::RemainingRequests();
	 }

	 public static function NextReset()
	 {
	 	$diff = FALSE;
	 	$reset = Redis::get("reset");
	 	if($reset!=="" && $reset!==null)
	 	{
	 		$now = \Carbon\Carbon::now('UTC')->format('U');
			$diff = $reset - $now;
			if($diff < 0)
		 	{
		 		$diff = FALSE;
		 	}
	 	}
		return $diff;
	 }
	 
	 public static function RemainingRequests()
	 {
	 	$remain = Redis::get("remaining");
	 	$limit = Redis::get("limit");
	 	if($limit==="" || $limit===null) $limit = 10;
	 	$nextReset = self::NextReset();
	 	$ret = $limit;
	 	if($nextReset!==FALSE)
	 	{
	 		 $ret = $remain;
	 	}
	 	return $ret; 
	 }
}