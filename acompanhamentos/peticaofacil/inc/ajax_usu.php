<?php

include("seguranca.php");
protegePagina();

if($_POST['flag']=="E")
{
	$id_usu = $_POST['id_usu'];
	$return = "";
	$i = 0;
	$q  = " SELECT * FROM tp_usu_tb";
	$q .= " WHERE id_usu = $id_usu";
	$query = mysql_query($q);
	$while = mysql_fetch_row($query);
	
	foreach($while as $w)
	{
		echo $w . "-|-";
	}
}
elseif($_POST['flag']=="I")
{
	$i  = " INSERT INTO tp_usu_tb SET";
	$i .= " nome_usu = '" 	. $_POST['nome_usu'] 	. "', " ;
	$i .= " login_usu = '" 	. $_POST['login_usu'] 	. "', " ;
	if($_POST['senha_usu1']!="")
	{
		$i .= " senha_usu = '" 	. md5($_POST['senha_usu1']) . "', " ;
	}
	$i .= " email_usu  = '"	. $_POST['email_usu'] . "', " ;
	$i .= " nivel_usu  = '"	. $_POST['nivel_usu'] . "', " ;
	$i .= " acesso_usu = '0000-00-00 00:00:00', " ;
	$i .= " data_cad   = '"	. date("Y-m-d H:i:s") . "' " ;
	$query = mysql_query($i);
	echo 1;
}
elseif($_POST['flag']=="U")
{
	$i  = " UPDATE tp_usu_tb SET";
	$i .= " nome_usu = '"  . $_POST['nome_usu']  . "', " ;
	$i .= " login_usu = '" . $_POST['login_usu'] . "', " ;
	if($_POST['senha_usu1']!="")
	{
		$i .= " senha_usu = '" 	. md5($_POST['senha_usu1']) . "', " ;
	}
	$i .= " email_usu 	 = '" . $_POST['email_usu'] . "', " ;
	$i .= " nivel_usu 	 = '" . $_POST['nivel_usu'] . "' " ;
	$i .= " WHERE id_usu = " . $_POST['id_usu'] 	. " " ;
	$query = mysql_query($i);
	echo 1;
}
elseif($_POST['flag']=="D")
{
	mysql_query("DELETE FROM `tp_usu_tb` WHERE `id_usu`='" . $_POST['id_usu'] . "' LIMIT 1");
	echo 1;
}
?>