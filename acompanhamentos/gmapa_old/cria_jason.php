<?php 

$contratos = $_POST['contratos'];
$cont_arra = explode("_|_",$contratos);


$fileHandle = fopen("enderecos.csv", "r");
$n = 0;
$array='';

while (($row = fgetcsv($fileHandle, 0, "\r")) !== FALSE) {
    foreach($row as $rr){
		if($n>0){
			
			$address = str_replace(";",",",$rr);
			$address1 = explode("_|_,",$address);
			$address2 = $address1[1];
			if($address!=''){
				
				$cont_mapa = explode(",",$address1[0]);
				
				//if (in_array($cont_mapa[0], $cont_arra)) { 
				
					$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address2.'&sensor=false');
					$output= json_decode($geocode);
					$lat = $output->results[0]->geometry->location->lat;
					$long = $output->results[0]->geometry->location->lng;
					if($lat!=''){
						if($n>1){
							$array .= ',<br>';
						}
						$array .= '{"Id": '.$n.',"Latitude": '.$lat.',"Longitude": '.$long.',"Descricao": "'.$address1[0].'"}';
					}
				//}
			}
			
			//echo str_replace(";","+",$address1[1])."<br>";
		}
		$n++;
	}
}
print_r($array);
//$fp = fopen("bloco1.txt", "w");
//$escreve = fwrite($fp, $array);
//fclose($fp);

?>