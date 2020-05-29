<?php

if(isset($PHPSESSID)){
	if($usuario!=''){
			$sess = $usuario;
			$sess = str_replace("-",'8',$sess);
			$sess = str_replace("_",'9',$sess);
			session_id($sess);
	}

	session_name($PHPSESSID);
	session_start();
}else{
	if($usuario!=''){
			$sess = $usuario;
			$sess = str_replace("-",'8',$sess);
			$sess = str_replace("_",'9',$sess);
			session_id($sess);
	}
	session_start();
}

session_register('username');
session_register('senha');

if (!isset($username) || trim($username) == "")
{
	echo "Acesso n&atilde;o permitido !";
	exit;
}

include("conectar.php");

if($_POST['flag']=="I")
{
	header("Content-Type: text/html; charset=UTF-8");
	
	$toptitle = $_POST['toptitle'] ? (strtoupper($_POST['toptitle'])) : "''";
	$tipo_id =  $_POST['tipo_id']  ? $_POST['tipo_id']  : "''";
	
	$title = '<div class="titulos">' . $toptitle . '</div><p>&nbsp;</p><p align="left"></p>';
	$qOrder = mysql_query("SELECT MAX(fund_order) FROM `tp_funda_tb` WHERE `tipo_id` = $tipo_id LIMIT 1",$conexao1);
	$wOrder = mysql_fetch_array($qOrder);
	
	$query = mysql_query("INSERT INTO `tp_funda_tb` SET `tipo_id` = $tipo_id, `fund_titulo` = '" . $toptitle . "', `fund_text` = '$title', `fund_order` = " . ($wOrder[0]+1) . " ",$conexao1)or die("ERRO");
	print "OK";
	
}
elseif($_POST['flag']=="S")
{
	header("Content-Type: text/html; charset=UTF-8");

	$fund_id   = $_POST['fund_id']   ? $_POST['fund_id']   : "''";
	$fund_text = $_POST['fund_text'] ? str_replace("%u2013","-",$_POST['fund_text']) : "''";

	$query = mysql_query("UPDATE `tp_funda_tb` SET `fund_text` = '" . $fund_text . "' WHERE `fund_id` = " . $fund_id . " ", $conexao1) or die("ERRO");
	
	print "OK";
	exit;
}
elseif($_POST['flag']=="T")
{
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	
	$tipotitle = $_POST['tipotitle'] ? $_POST['tipotitle'] : "''";
	
	$query = mysql_query("INSERT INTO `tp_tipo_tb` SET `tipo_nome` = '" . strtoupper($tipotitle) . "', `tipo_usu` = " . $cod_usu . ", `tipo_data` = now(), `tipo_stt` = 'Y' ",$conexao1)or die("ERRO");
	//print "INSERT INTO `tp_tipo_tb` SET `tipo_nome` = '" . strtoupper($tipotitle) . "', `tipo_usu` = " . $cod_usu . " `tipo_data` = now(), `tipo_stt` = 'Y' ";
	print "OK";
}
elseif($_POST['flag']=="D")
{
	$query = mysql_query("DELETE FROM `tp_funda_tb` WHERE `fund_id`= " . $_POST['idvalor'] . " LIMIT 1 ",$conexao1) or die("ERRO");
	print "OK";
}


?>