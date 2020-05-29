<?php

header("Content-Type: text/html; charset=ISO-8859-1",true);

include("conectar.php");

$tabela = $_POST['tabela'] 	? $_POST['tabela'] : "''";
$campo0 = $_POST['campo0']	? $_POST['campo0'] : "''";
$campo1 = $_POST['campo1']	? $_POST['campo1'] : "''";
$campo2 = $_POST['campo2']	? $_POST['campo2'] : "''";
$id_ref = $_POST['id_ref']	? $_POST['id_ref'] : "''";
$id_val = $_POST['id_val']	? $_POST['id_val'] : "''";
if($_POST['conex']==1)
{
	$conex 	= $conexao1;
}
elseif($_POST['conex']==2)
{
	$conex 	= $conexao2;
}
$sel  = " SELECT ";
$sel .= " $campo0,";
$sel .= " $campo1 ";
$sel .= " FROM $tabela";
$sel .= " where ";
$sel .= " $id_ref = $id_val";

$query = mysql_query($sel,$conex);
$while = mysql_fetch_array($query);
echo $while[0] . "_|_" . $while[1];

?>
