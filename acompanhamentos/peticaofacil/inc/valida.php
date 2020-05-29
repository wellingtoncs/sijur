<?php

session_start();
include("seguranca.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
	$usuario = (isset($_POST['username'])) ? $_POST['username'] : '';
	$senha2  = (isset($_POST['passwd'])) ? $_POST['passwd'] : '';
	$senha   = md5($senha2);
	
	if (validaUsuario($usuario, $senha) == true)
	{
		mysql_query("UPDATE tp_usu_tb SET acesso_usu = '" . date("Y-m-d H:i:s") . "' where id_usu = " . $_SESSION['usuarioID'] . " ");
		header("Location: ../form.php");
	}
	else
	{
		expulsaVisitante();
	}
}
?>