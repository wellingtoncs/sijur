<?php

$address = 'rua+feira+de+santana,258,olinda,pernambuco';
if($address!=''){	
	$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
	$output= json_decode($geocode);
	$lat = $output->results[0]->geometry->location->lat;
	$long = $output->results[0]->geometry->location->lng;
	echo '{"Id": 1,"Latitude": '.$lat.',"Longitude": '.$long.',"Descricao": "Conteúdo do InfoBox 1"}';
}

?>

