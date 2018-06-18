<?php
 
namespace App\Classes;


use Illuminate\Support\Facades\Redis;
use App\Classes\UrlMaker;
use App\Classes\Requester; 
use App\Classes\BladeHelper;

class Match   {
	
	 private $map;
	 private $isOk;
	 private $json;
	 private $id;
	 private $shards;
	 private $telemetry_url;
	 private $mode;
	 private $duration;
	 private $createdAt; 
	 private $last_http_code;

	 public function __construct()
	 {
	 	$this->Init();
	 }	

	 public function requestData()
	 {
	 	$ret = false;
	 	// test if json was stored in cache system
	 	$json = Redis::get($this->getCacheId());
	 	if($json!==null && $json!=='')
	 	{
	 		$this->setJson($json);
	 		$ret = true;
	 	}
	 	else
	 	{
	 		// no json in cache, download it
	 		$url = UrlMaker::getUrlMatch($this->id,$this->shards);	 		 
	 		$requester = new Requester();
	 		$requester->sendRequest($url);
	 		$this->last_http_code = $requester->getLastHttpCode();
	 		if($requester->isResponseOk())
	 		{
	 			$json = $requester->getBody();
	 			$this->setJson($json);
	 			if($this->id!=="")
	 			{
	 				$data_match_expire = env("REDIS_MATCH_TIMEOUT",1209600);

	 				Redis::set($this->getCacheId(),$json);
	 				Redis::expire($this->getCacheId(),$data_match_expire);
	 			}	 			
	 			$ret = true;
	 		}
	 		else
	 		{
	 			$ret = false;
	 			$this->error = "Http Code : " . $requester->getLastHttpCode() . " - Body : " . $requester->getBody();
	 		}
	 	}
	 	return $ret;
	 }

	 private function getCacheId()
	 {
	 	return "match_" . $this->id . "_" . $this->shards;
	 }

	 private function fillData()
	 {
	 	$objm = json_decode($this->json);
	 	$idtelemetry = "";
                                
        $this->createdAt = $objm->data->attributes->createdAt;
        $this->duration = $objm->data->attributes->duration;
        $this->mode = $objm->data->attributes->gameMode;
        $this->map = "";
        $this->telemetry_url = "";

        $idtelemetry = $objm->data->relationships->assets->data[0]->id;
          
        foreach ($objm->included as $elem) {
            
            if($elem->type ==="asset" && $elem->id===$idtelemetry)
            {                 
                $telemetry = $elem->attributes->URL;
                $this->telemetry_url = $telemetry;                                            
            }
        }
        $this->isOk = ($this->telemetry_url!=='');
	 }

	 public function getModeHtml()
	 {
	 	$ret = "";
	 	switch ($this->mode) {
	 		case 'solo':
	 		case 'solo-fpp':
	 			$ret = "<i class='solo fa fa-user'></i>";
	 			break;
	 		case 'duo':
	 		case 'duo-fpp':
	 			$ret = str_repeat("<i class='fa fa-user duo'></i>",2);
	 			break;
	 		case 'squad':
	 		case 'squad-fpp':
	 			$ret = str_repeat("<i class='fa fa-user squad'></i>",4);
	 			break;
	 		default:
	 			$ret = $this->mode;
	 			break;
	 	}
	 	return $ret;
	 }

	 public function extractPlace($name)
	 {
	 	$objm = json_decode($this->json);
	 	$ret = "";
	 	
	 	foreach ($objm->included as $elem) {
	 		if($elem->type==="participant")
	        {
	            $attrs = $elem->attributes->stats;
	            if($attrs->name==$name)
	            {
	            	$ret = $attrs->winPlace;
	            	break;
	            }	            
	        }	        
	 	}
	 	return $ret;
	 }

	 private function Init()
	 {
	 	$this->map = "";
		$this->json = "";
		$this->id = "";
		$this->shards = "";
		$this->telemetry_url = "";
		$this->mode = "";
		$this->duration = "";
		$this->createdAt = ""; 
		$this->last_http_code = "";
		$this->isOk = false;
	 }

	 public function isOk()
	 {
	 	return $this->isOk;
	 }
	
 
	/**
	* Get value of map.
	*/
	public function getMap()
	{
		return $this->map;
	}
	 
	/**
	* Set value of map.
	*
	* @param mixed $map the map
	*
	* @return self
	*/
	public function setMap($map)
	{
		$this->map = $map;
		return $this;
	}
	 
	/**
	* Get value of id.
	*/
	public function getId()
	{
		return $this->id;
	}
	 
	/**
	* Set value of id.
	*
	* @param mixed $id the id
	*
	* @return self
	*/
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	 
	/**
	* Get value of telemetry_url.
	*/
	public function getTelemetryUrl()
	{
		return $this->telemetry_url;
	}
	 
	/**
	* Set value of telemetry_url.
	*
	* @param mixed $telemetry_url the telemetry url
	*
	* @return self
	*/
	public function setTelemetryUrl($telemetry_url)
	{
		$this->telemetry_url = $telemetry_url;
		return $this;
	}
 
 
	   
	 
	/**
	* Get value of mode.
	*/
	public function getMode()
	{
		return $this->mode;
	}
	 
	/**
	* Set value of mode.
	*
	* @param mixed $mode the mode
	*
	* @return self
	*/
	public function setMode($mode)
	{
		$this->mode = $mode;
		return $this;
	}
	 
	/**
	* Get value of duration.
	*/
	public function getDuration()
	{
		return $this->duration;
	}
	 
	/**
	* Set value of duration.
	*
	* @param mixed $duration the duration
	*
	* @return self
	*/
	public function setDuration($duration)
	{
		$this->duration = $duration;
		return $this;
	}
	  
	 
	/**
	* Get value of createdAt.
	*/
	public function getCreatedAt()
	{
		return BladeHelper::ftDate($this->createdAt);
	}
	 
	/**
	* Set value of createdAt.
	*
	* @param mixed $createdAt the created at
	*
	* @return self
	*/
	public function setCreatedAt($createdAt)
	{
		$this->createdAt = $createdAt;
		return $this;
	}
	 
	/**
	* Get value of shards.
	*/
	public function getShards()
	{
		return $this->shards;
	}
	 
	/**
	* Set value of shards.
	*
	* @param mixed $shards the shards
	*
	* @return self
	*/
	public function setShards($shards)
	{
		$this->shards = $shards;
		return $this;
	}
	 
	/**
	* Get value of json.
	*/
	public function getJson()
	{
		return $this->json;
	}
	 
	/**
	* Set value of json.
	*
	* @param mixed $json the json
	*
	* @return self
	*/
	public function setJson($json)
	{
		$this->json = $json;
		$this->fillData();
		return $this;
	}
	 
	 
	/**
	* Get value of last_http_code.
	*/
	public function getLastHttpCode()
	{
		return $this->last_http_code;
	}
	 
	/**
	* Set value of last_http_code.
	*
	* @param mixed $last_http_code the last http code
	*
	* @return self
	*/
	public function setLastHttpCode($last_http_code)
	{
		$this->last_http_code = $last_http_code;
		return $this;
	}
	}