<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Notícias Dev Media</title>
</head>
<body>
<h1>Notícias Dev Media</h1>
<?php
	$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
	mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");

    $link = "http://acessoweb.sytes.net:8080/sapweb/1/publicacao/xml_webservice_todos.php?EMPRESA=LIGCONTATO&CLIENTE_CONTRATANTE=50&DATA_INICIAL=15/10/2015&DATA_FINAL=15/10/2015"; //link do arquivo xml
if( ! $xml = simplexml_load_file($link) ) {
	echo 'XML não existe';
} else {
	foreach( $xml as $produto ){
		echo '<br><br>N_RECORTE: '.$produto->N_RECORTE;
		echo '<br>JORNAL: '.$produto->JORNAL;
		echo '<br>DATA_PUBLICACAO: '.$produto->DATA_PUBLICACAO;
		echo '<br>NOME_PESQUISADO: '.$produto->NOME_PESQUISADO;
		echo '<br>TRIBUNAL: '.$produto->TRIBUNAL;
		echo '<br>SECRETARIA: '.$produto->SECRETARIA;
		echo '<br>NUMERO_PROCESSO: '.$produto->NUMERO_PROCESSO;
		echo '<br>PUBLICACAO: '.$produto->PUBLICACAO;
		$qCASE = mysql_query("SELECT c.id_case FROM lcm_case as c where c.processo = '".$produto->NUMERO_PROCESSO."' ",$conexao1);
		if(mysql_num_rows($qCASE)){  
			echo '<br>No SIJUR';
		}else{
			echo '<br>Nao encontrado!';
		}
	}
}

?>
</body>
</html>