<?php
 
namespace App\Classes;
 

use Illuminate\Support\Facades\Storage;
use App\Classes\PathProvider;
use App\Classes\Match;
use App\Classes\TelemetryProvider;
class ReplayProvider   
{	
	public static function getReplay($id,$shards,$user)
	{		 
		$ret = FALSE;

		$match = new Match();
		$match->setId($id);
		$match->setShards($shards);
		$match->requestData();


		if($match->isOk())
		{
			$path = "replay/$id.json";
			if(Storage::disk('local')->exists($path))
			{
				// find replay json in storage folder
				$ret = json_decode(Storage::disk('local')->get($path));
			}
			else
			{
				// if not exists , extract telemetry, parse it, store it
				$telemetry = TelemetryProvider::getTelemetry($id,$match->getTelemetryUrl());
				$ret  = PathProvider::getFromTelemetry($telemetry,$user);	
				Storage::disk('local')->put($path,json_encode($ret));
			}
			// parse replay file to affect players color
			$plys = collect($ret->players);
			$ply = $plys->where('name',$user)->first();
			$teamid = $ply->teamId;
			$plya = array();
			foreach ($ret->players as $yla) {
				$yla->color = ($yla->teamId==$teamid) ? "#00ff00": "#ffffff";
				$yla->isplayer =  ($yla->teamId==$teamid) ? 1 : 0;
				$plya[] = $yla;				 	 
			} 	
			$ret->players = $plya;	
		}
		 
		return $ret;
	} 
}