<?php 
 
include('inc/inc.php');

$pasta = trim($_GET['pasta']);
$ender = trim($_GET['ender']);
$cords = trim($_GET['cords']);
$tipo  = trim($_GET['tipo']);

if($pasta!=""){	
	$Qcase = mysql_query("SELECT cl.id_adverso FROM lcm_case_adverso_cliente as cl where cl.id_case = '$pasta' and cl.id_adverso != 0 limit 1");
	$rows = mysql_num_rows($Qcase);
	$Wadvr = mysql_fetch_array($Qcase);
	$q = "";
	if($rows==1){
		$q .= "INSERT INTO lcm_contact SET ";
		$q .= "type_person =  'adverso', ";
		$q .= "id_of_person =  '" . $Wadvr['id_adverso'] . "', ";
		$q .= "value =  '$ender', ";
		$q .= "type_contact =  '".$tipo."', ";
		$q .= "date_update =  now(), ";
		$q .= "extra =  '$cords' ";
		//echo $q;
		$insert = mysql_query($q);
	}
	if($insert){
		header("location:http://www.direito2010.com.br/gmapa/ajax_iframe.php?mens=Endereço inserido com sucesso!"); 
	}else{
		header("location:http://www.direito2010.com.br/gmapa/ajax_iframe.php?mens=Não inserido!"); 
	}
}else{
	 header("location:http://www.direito2010.com.br/gmapa/ajax_iframe.php?mens=Pasta inexistente!"); 
}

?>