<?php
 
namespace App\Classes;
 
use \Curl\Curl;

class Request{

	public static function url($url)
	{ 
		$data = "";
		try 
		{
			$ch = curl_init();

			$headers = array(
		        'Accept-Encoding: gzip'
		    );      

		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		 	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		    $data = curl_exec($ch);
		    curl_close($ch);
		} 
		catch (Exception $e) 
		{
		
		}
	    return $data;
	}
}