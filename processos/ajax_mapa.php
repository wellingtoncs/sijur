<?php
if($_POST['adress']!=""){
	$address = str_replace(", ","+",$_POST['adress']);
	$address = str_replace(" ","",$address);
	$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
	$output= json_decode($geocode);
	$lat = $output->results[0]->geometry->location->lat;
	$long = $output->results[0]->geometry->location->lng;
	if($lat!=""){
		echo $lat . "," . $long;
	}
}
?>  