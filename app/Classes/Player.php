<?php
 
namespace App\Classes;

use Illuminate\Support\Facades\Redis;
use App\Classes\UrlMaker;
use App\Classes\Requester;
use App\Classes\Match;

class Player   {
	
	 private $player;
	 private $shards;
	 private $json;
	 private $matchs;
	 private $id; 
	 private $error;
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
	 		$url = UrlMaker::getUrlPlayer($this->player,$this->shards);
	 		 
	 		$requester = new Requester();
	 		$requester->sendRequest($url);
	 		$this->last_http_code = $requester->getLastHttpCode();
	 		if($requester->isResponseOk())
	 		{
	 			$json = $requester->getBody();
	 			$this->setJson($json);
	 			if($this->id!=="")
	 			{
	 				Redis::set($this->getCacheId(),$json);
	 				Redis::expire($this->getCacheId(),1800);
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

	 private function fillData()
	 {
	 	$obj = json_decode($this->json);
		$this->id = $obj->data[0]->id;                     
	 	$matches_ok = isset($obj->data[0]->relationships->matches->data);
	 	if($matches_ok)
	 	{
	 		$matches =  $obj->data[0]->relationships->matches->data;
            foreach ($matches as $match) 
            {
               $idmatch = $match->id;
               $match = new Match();
               $match->setShards($this->shards);
               $match->setId($idmatch);
               $this->addMatch($match);
            }
	 	}
	 }

	 private function getCacheId()
	 {
	 	return "player_" . $this->player . "_" . $this->shards;
	 }

	 private function Init()
	 {
	 	$this->player = "";
		$this->shards = "";
		$this->json = "";
		$this->matchs = array();
		$this->id = "";
		$this->error = "";
		$this->last_http_code = "";
	 }	 

	 public function addMatch(Match $match)
	 {
	 	$this->matchs[] = $match;
	 }
 
	/**
	* Get value of player.
	*/
	public function getPlayer()
	{
		return $this->player;
	}
	 
	/**
	* Set value of player.
	*
	* @param mixed $player the player
	*
	* @return self
	*/
	public function setPlayer($player)
	{
		$this->player = $player;
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
	* Get value of matchs.
	*/
	public function getMatchs()
	{
		return $this->matchs;
	}
	 
	/**
	* Set value of matchs.
	*
	* @param mixed $matchs the matchs
	*
	* @return self
	*/
	public function setMatchs($matchs)
	{
		$this->matchs = $matchs;
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
	* Get value of error.
	*/
	public function getError()
	{
		return $this->error;
	}
	 
	/**
	* Set value of error.
	*
	* @param mixed $error the error
	*
	* @return self
	*/
	public function setError($error)
	{
		$this->error = $error;
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