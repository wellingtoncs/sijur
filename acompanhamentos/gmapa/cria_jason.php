<?php 
$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor eduardoalbuquerque.no-ip.biz ");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");
$ids_case = $_GET['contratos'];
$cases = str_replace("_|_",",",$ids_case);
include("maps.php");

$n=0;

$Qctt = mysql_query("SELECT c.id_case, c.p_adverso, c.notes, ct.value
FROM lcm_case AS c
JOIN lcm_case_adverso_cliente AS ca ON ca.id_case=c.id_case
JOIN lcm_contact AS ct ON ct.id_of_person=ca.id_adverso
WHERE c.id_case in (".$cases.")
and ct.type_contact=7
GROUP BY ct.id_contact ");

$array=array();
$arr_cases=array();
$arr_no="";
while($Wctt = mysql_fetch_array($Qctt)){
		$address = htmlentities($Wctt['value']);
		if($address!=''){
			$return = Maps::getLocal($address);
			$lat = $return->lat;
			$lng = $return->lng;
			if($lat!=''){
				$pt = trim($Wctt['id_case']);
				$nm = htmlentities($Wctt['p_adverso']);
				$vc = htmlentities(trim(str_replace("\n","",$Wctt['notes'])));
				$array[] = '{"Id": '.$n.',"Latitude": '.$lat.',"Longitude": '.$lng.',"Descricao":"<p>Pasta: '.$pt.'</p><p>Nome: '.$nm.'</p><p>Endereço: '.str_replace(",",", ",str_replace("+"," ",$address)).'</p><p>Veículo:'.$Wctt['notes'].'</p>"}';
				$arr_cases[] = $pt;
			} 
		}
	$n++;
}
$implode = implode(",",$array);
$arq = "[".$implode."]";
$fp = fopen("js/pontos.json", "w");
$escreve = fwrite($fp, $arq);
fclose($fp);

$cases_get = explode(",",$cases);
foreach($cases_get as $get){
	if(!in_array($get, $arr_cases)){
		$arr_no .= $get . "\n";
	}
}

$fn = fopen("js/arr_no.txt", "w");
$escreve = fwrite($fn, $arr_no);
fclose($fn);

header('location: index.php');    
?>