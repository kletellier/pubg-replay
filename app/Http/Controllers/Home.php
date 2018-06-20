<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Classes\Shards;
use App\Classes\Player;
use App\Classes\Requester;

class Home extends Controller
{
    public function test()
    {
        if(!env('APP_DEBUG',false))
        {
          abort(404);
        } 
        $reset = Requester::NextReset();
         $message = "API request quota excedeed, please retry in $reset second(s)";
            return view("Home/message",array('title'=>'Error','message'=>$message));
    }

    public function player(Request $request,$player="",$page=1)
    {
        $name = $request->input('inpName');
        $shards = $request->input('inpShards');
      
        $more_request_needed = false;

        $nb = Requester::RemainingRequests();
        if($nb<2)
        {
            $reset = Requester::NextReset();
            $message = "API request quota excedeed, please retry in $reset second(s)";
            return view("Home/message",array('title'=>'Error','message'=>$message));
        }

        $player = new Player();
        $player->setPlayer($name);
        $player->setShards($shards);

        $ok = $player->requestData();

        if(!$ok)
        {
        	$message = $player->getError();
        	if($player->getLastHttpCode()==404)
        	{
        		$message = "Player : " . $name . " is unknown, check name case !!";
        	}        	 
            return view("Home/message",array('title'=>'Error','message'=>$message));
        }
        else
        {
           $pagecount = env("PAGECOUNT",10);
           $matchs_arr = $player->getMatchs();
           $matchs_col = collect($matchs_arr);
           $nb = $matchs_col->count();

           $nbpage = round($nb / $pagecount);
           
           $skip = ($page-1) * $pagecount;
           $matchs = $matchs_col->skip($skip)->take($pagecount);

           $pageprev = $page-1;
           $pagenext = $page+1;

           if($pageprev<2){$pageprev="";}
           if($pageprev>$nb){$pagenext="";}

           foreach ($matchs as $match) 
           {
                $ret = $match->requestData();
                if($ret===false)
                {
                    $more_request_needed = true;
                    break;
                }
           }         
        }

        $param = array('missing'=>$more_request_needed,'page'=>$page,'nbpage'=>$nbpage,'pageprev'=>$pageprev,'pagenext'=>$pagenext,'name'=>$name,'shards'=>$shards,'title'=>$name,'matches'=>$matchs);
        return view("Home/player",$param);
    }
     
    public function replay(Request $request,$id,$shards,$user)
    {
        $size = "";
        if($request->get('size')!="")
        {
            $size = $request->get('size');
        }
        $param = array('title'=>'Replay','id'=>$id,'shards'=>$shards,'user'=>$user,'size'=>$size);
        return view("Home/replay",$param);
    }

    public function index()
    {         
        $param = array();
        $param["title"] = "Pubg Replay";
        $param["shards"] = Shards::getShardsArray(); 
    	return view("Home/index",$param);
    }  
}
