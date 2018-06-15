<?php
 
namespace App\Classes;
 

class Shards   {	
	
   const XBOX_AS  = "xbox-as";
   const XBOX_EU   = "xbox-eu";
   const XBOX_NA   = "xbox-na";
   const XBOX_OC   = "xbox-oc";
   const PC_KRJP   = "pc-krjp";
   const PC_NA = "pc-na";
   const PC_EU  = "pc-eu";
   const PC_OC  = "pc-oc";
   const PC_KAKAO  = "pc-kakao";
   const PC_SEA = "pc-sea";
   const PC_SA = "pc-sa";
   const PC_AS = "pc-as"; 	

   public static function getShardsArray()
   {
      $ret = array();

      $ids = array("pc-krjp","pc-na","pc-eu","pc-oc","pc-kakao","pc-sea","pc-sa","pc-as");
      foreach ($ids as $id) {
         $shd = new \stdClass();
         $shd->value = $id;
         $shd->selected = ("pc-eu"==$id) ? "SELECTED" : "";
         $shd->name = strtoupper($id);
         $ret[] = $shd;
      }
      return $ret;
   }
	
}