<?php

$servidor1 	= "localhost";
$user1 		= "root";
$senha1		= "edualb";
$db1 		= "ftproc";
$conexao1 	= mysql_connect($servidor1,$user1,$senha1) or die (mysql_error());
$banco1		= mysql_select_db($db1, $conexao1) or die(mysql_error());

/*
$servidor2 	= "localhost";
$user2 		= "root";
$senha2		= "torres10";
$db2 		= "torre_iniciais";
$conexao2 	= mysql_connect($servidor2,$user2,$senha2) or die (mysql_error());
$banco2		= mysql_select_db($db2, $conexao2) or die(mysql_error());
*/

?>