<?php

include("seguranca.php");
protegePagina();

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
	$cod_usu = '2156';
	$tiposetor = $_POST['tiposetor'] ? $_POST['tiposetor'] : "";
	
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	$tipotitle = $_POST['tipotitle'] ? $_POST['tipotitle'] : "''";
	$query = mysql_query("INSERT INTO `tp_tipo_tb` SET 
		`tipo_nome` = '" . strtoupper($tipotitle) . "', 
		`tipo_usu` = '" . $cod_usu . "', 
		`tipo_data` = now(), 
		`tipo_stt` = 'Y', 
		`id_setor` = '" . $tiposetor . "' 
		",$conexao1)or die("ERRO");
	//print "INSERT INTO `tp_tipo_tb` SET `tipo_nome` = '" . strtoupper($tipotitle) . "', `tipo_usu` = " . $cod_usu . " `tipo_data` = now(), `tipo_stt` = 'Y' ";
	
	$tiposerve = $_POST['tiposerve'] ? $_POST['tiposerve'] : "";
	$tipobanco = $_POST['tipobanco'] ? $_POST['tipobanco'] : "";
	$tipousuar = $_POST['tipousuar'] ? $_POST['tipousuar'] : "";
	$tiposenha = $_POST['tiposenha'] ? $_POST['tiposenha'] : "";
	$tipotable = $_POST['tipotable'] ? $_POST['tipotable'] : "";
	$tipochave = $_POST['tipochave'] ? $_POST['tipochave'] : "";
	$tipoquery = $_POST['tipoquery'] ? $_POST['tipoquery'] : "";
	$tipowhere = $_POST['tipowhere'] ? $_POST['tipowhere'] : "";
	
	if(	isset($_POST['tiposerve']) && 
		isset($_POST['tipobanco']) && 
		isset($_POST['tipousuar']) && 
		isset($_POST['tiposenha']) && 
		isset($_POST['tipotable']) && 
		isset($_POST['tipochave']))
	{
		
		$qOrder = mysql_query("SELECT MAX(tipo_id) FROM `tp_tipo_tb` LIMIT 1",$conexao1);
		$wOrder = mysql_fetch_array($qOrder);
		
		$query2 = mysql_query("INSERT INTO `tp_config_db` SET 
		`tipo_id`  = '" . $wOrder[0] . "', 
		`ip_db`    = '" . $tiposerve . "', 
		`data_db`  = '" . $tipobanco . "', 
		`usu_db`   = '" . $tipousuar . "', 
		`senha_db` = '" . $tiposenha . "', 
		`table_db` = '" . $tipotable . "', 
		`chave_db` = '" . $tipochave . "', 
		`query_db` = '" . $tipoquery . "', 
		`where_db` = '" . $tipowhere . "' ",$conexao1)or die("ERRO");
	}
	
	print "OK";
}
elseif($_POST['flag']=="D")
{
	$query = mysql_query("DELETE FROM `tp_funda_tb` WHERE `fund_id`= " . $_POST['idvalor'] . " LIMIT 1 ",$conexao1) or die("ERRO");
	print "OK";
}
elseif($_POST['flag']=="DT")
{
	$query1 = mysql_query("DELETE FROM `tp_tipo_tb`   WHERE `tipo_id`= " . $_POST['tipoid'] . " LIMIT 1 ",$conexao1) or die("ERRO");
	$query2 = mysql_query("DELETE FROM `tp_config_db` WHERE `tipo_id`= " . $_POST['tipoid'] . " LIMIT 1 ",$conexao1) or die("ERRO");
	print "OK";
}
elseif($_POST['flag']=="C")
{
	$query = mysql_query("UPDATE `tp_tipo_tb` SET `cod_cabec` = '" . $_POST['fund_text'] . "' WHERE `tipo_id` = " . $_POST['fund_id'] . " ", $conexao1) or die("ERRO");
	print "OK";
	exit;
}
elseif($_POST['flag']=="R")
{
	$query = mysql_query("UPDATE `tp_tipo_tb` SET `cod_rodap` = '" . $_POST['fund_text'] . "' WHERE `tipo_id` = " . $_POST['fund_id'] . " ", $conexao1) or die("ERRO");
	print "OK";
	exit;
}


?>