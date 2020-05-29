<?php

session_start();

$_SG['conectaServidor'] = true;
$_SG['caseSensitive'] 	= false;
$_SG['validaSempre'] 	= true;
$_SG['servidor'] 		= 'localhost';
$_SG['usuario'] 		= 'root';
$_SG['senha'] 			= 'edualb';
$_SG['banco'] 			= 'sistemas_gm';
$_SG['paginaLogin'] 	= 'index.php';
$_SG['tabela'] 			= 'tp_usu_tb';

if ($_SG['conectaServidor'] == true) 
{
	$conexao1 = mysql_connect($_SG['servidor'], $_SG['usuario'], $_SG['senha']) or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
	mysql_select_db($_SG['banco'], $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");
}
function validaUsuario($usuario, $senha) 
{
	global $_SG;
	$cS 		= ($_SG['caseSensitive']) ? 'BINARY' : '';
	$nusuario 	= addslashes($usuario);
	$nsenha   	= addslashes($senha);
	$sql 	  	= "SELECT * FROM `".$_SG['tabela']."` WHERE " . $cS . " `login_usu` = '" . $nusuario . "' AND ".$cS." `senha_usu` = '".$nsenha."' LIMIT 1";
	$query 	    = mysql_query($sql);
	$resultado 	= mysql_fetch_assoc($query);
	
	if (empty($resultado)) 
	{
		return false;
	} 
	else 
	{
		$_SESSION['usuarioID'] 	  = $resultado['id_usu']; 
		$_SESSION['usuarioNome']  = $resultado['nome_usu']; 
		$_SESSION['usuarioNivel'] = $resultado['nivel_usu'];
		$_SESSION['usuarioST'] 	  = $resultado['status_usu'];
		$_SESSION['usuarioSetor'] = $resultado['id_setor'];
		

		if ($_SG['validaSempre'] == true)
		{
			$_SESSION['usuarioLogin'] = $usuario;
			$_SESSION['usuarioSenha'] = $senha;
		}
		return true;
	}
}
function protegePagina() 
{
	global $_SG;
	if (!isset($_SESSION['usuarioID']) OR !isset($_SESSION['usuarioNome'])) 
	{
		expulsaVisitante();
	}
	else if (!isset($_SESSION['usuarioID']) OR !isset($_SESSION['usuarioNome'])) 
	{
		if ($_SG['validaSempre'] == true) 
		{
			if (!validaUsuario($_SESSION['usuarioLogin'], $_SESSION['usuarioSenha'])) 
			{
				expulsaVisitante();
			}
		}
	}
}
function expulsaVisitante() 
{
	global $_SG;
	unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
	echo ('<script language="javascript">alert("Usuário ou senha Inválidos!")</script>');
	exit ('<SCRIPT LANGUAGE="JavaScript">window.location="./index.php";</script>');
}

function expulsaVisitante2() 
{
	global $_SG;
	unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
	exit ('<SCRIPT LANGUAGE="JavaScript">window.location="./index.php";</script>');
}

?>