<?php
 
namespace App\Classes;
 

class UrlMaker   {	
	
   const BASE_URL  = "https://api.playbattlegrounds.com/shards/";
  
   public static function getUrlPlayer($player,$shards)
   {
      $ret = self::BASE_URL;
      $ret .= "$shards/players?filter[playerNames]=" . trim($player);     
      return $ret;
   }

   public static function getUrlMatch($id,$shards)
   {
      $ret = self::BASE_URL;
      $ret .= "$shards/matches/" . trim($id);     
      return $ret;
   }
	
}