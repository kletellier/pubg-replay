<?php
 
namespace App\Classes;

use Carbon\Carbon;
use Stringy\Stringy as S; 
use App\Classes\Request;
use Illuminate\Support\Facades\Storage;

class TelemetryProvider   
{	
	public static function getTelemetry($id,$url)
	{
		$ret = FALSE;
		 
		$path = "telemetry/$id.json";
		if(Storage::disk('local')->exists($path))
		{
			$ret = Storage::disk('local')->get($path);
		}
		else
		{ 
			$ret = Request::url($url);
			if($ret!==FALSE)
			{
				Storage::disk('local')->put($path,$ret);
			}
		}
		 
		return $ret;
	}
}