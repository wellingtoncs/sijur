<?php

class Maps {

  //chave publica de acesso
  private static $googleKey = 'AIzaSyB6dcJUAFg_R54Z8NTZfWWgcdM4YUJUmzM';
  //private static $googleKey = 'AIzaSyDaoYEDYrgVP78flxCQ52acq42TmkncOD8';

	static function loadUrl($url){
		$cURL = curl_init($url);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
		$result = curl_exec($cURL);
		curl_close($cURL);

		if($result) {
			return $result;
		}else{
			return false;        
		}
	}

	static function getLocal($address) {
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='. urlencode($address) .'&key='.self::$googleKey;    
		$result = self::loadUrl($url);

		$json = json_decode($result);

		if($json->{'status'} == 'OK') {        
			return $json->{'results'}[0]->{'geometry'}->{'location'};  
		}else{
			return false;
		}
	}
}

?>