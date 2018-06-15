<?php

namespace App\Classes;

use Carbon\Carbon;
use Stringy\Stringy as S;

class BladeHelper   
{

	public static function random_color_part() {
		return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	}

	public static function random_color() {
		return self::random_color_part() . self::random_color_part() . self::random_color_part();
	}


	public static function toHis($str)
	{
		return Carbon::createFromFormat('Y-m-d H:i:s',"2000-01-01 00:00:00")->addSeconds($str)->format('i:s');
	}

	public static function ftDate($str)
	{
		$tz = env('TIMEZONE','Europe/Paris');
		$date = S::create($str)->replace("T"," ")->replace("Z","")->__toString(); 
		return \Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$date,"UTC")->setTimezone($tz)->format('d/m/Y H:i');
	}

	public static function ftDateDot($str)
	{
		$tz = env('TIMEZONE','Europe/Paris');
		$obj = S::create($str)->replace("T"," ")->replace("Z","");
		$posdash = $obj->indexOfLast("."); 
		$date = $obj->substr(0,$posdash)->__toString();

		return  \Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$date,"UTC")->setTimezone($tz)->format('d/m/Y H:i:s');
	}
}