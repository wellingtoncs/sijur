<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr" >
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title>Fichas</title> 
</head>
<body>

<?php
	
	echo "<form action='index.php' method='post'>";	

		$ficha = $_POST['ficha'];
		$filename = "modelo_de_ficha.rtf";
		$fp=fopen($filename,'r');
		$output=fread($fp,filesize($filename));
		fclose($fp);
		
		$output=str_replace("<<banco>>",		trim($_POST["banco_".$ficha]),$output);
		$output=str_replace("<<processo>>",		trim($_POST["processo_".$ficha]),$output);
		$output=str_replace("<<vara>>",			trim($_POST["vara_".$ficha]),$output);
		$output=str_replace("<<comarca>>",		trim($_POST["comarca_".$ficha]),$output);
		$output=str_replace("<<estado>>",		trim($_POST["estado_".$ficha]),$output);
		$output=str_replace("<<cpfcnpj>>",		trim($_POST["cpfcnpj_".$ficha]),$output);
		$output=str_replace("<<adverso>>",		trim($_POST["adverso_".$ficha]),$output);
		$output=str_replace("<<marca>>",		trim($_POST["marca_".$ficha]),$output);
		$output=str_replace("<<modelo>>",		trim($_POST["modelo_".$ficha]),$output);
		$output=str_replace("<<ano>>",			trim($_POST["ano_".$ficha]),$output);
		$output=str_replace("<<cor>>",			trim($_POST["cor_".$ficha]),$output);
		$output=str_replace("<<chassi>>",		trim($_POST["chassi_".$ficha]),$output);
		$output=str_replace("<<renavam>>",		trim($_POST["renavam_".$ficha]),$output);
		$output=str_replace("<<placa>>",		trim($_POST["placa_".$ficha]), $output);
		$output=str_replace("<<endereco1>>",	trim($_POST["endereco1_".$ficha]),$output);
		$output=str_replace("<<emdereco2>>",	trim($_POST["emdereco2_".$ficha]),$output);
		$output=str_replace("<<emdereco3>>",	trim($_POST["emdereco3_".$ficha]),$output);
		$output=str_replace("<<profissao>>",	trim($_POST["profissao_".$ficha]),$output);
		$output=str_replace("<<conjuge>>",		trim($_POST["conjuge_".$ficha]),$output);
		$output=str_replace("<<endereco>>",		trim($_POST["endereco_".$ficha]),$output);
		$output=str_replace("<<tel>>",			trim($_POST["tel_".$ficha]),$output);
		$output=str_replace("<<oficial>>",		trim($_POST["oficial_".$ficha]),$output);
		$output=str_replace("<<expediente>>",	trim($_POST["expediente_".$ficha]),$output);
		$output=str_replace("<<outros>>",		trim($_POST["outros_".$ficha]),$output);
		
		
		$comarca = $_POST["comarca_".$ficha];
		$comarca = str_replace(" ","_",preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($comarca))));
		$adverso = $_POST["adverso_".$ficha];
		$adverso = str_replace(" ","_",preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($adverso))));
	
		$novo = fopen("E://Publico/_BANCO GMAC - BUSCA/FICHAS_DE_LOCALIZACAO/".$_POST["estado_".$ficha]."/".$adverso."-FICHA_DE_LOCALIZACAO-".$comarca.".rtf","w");
		fwrite($novo,$output);
		fclose($novo);
		$count++;
	
	header("location:P:\\_BANCO GMAC - BUSCA\FICHAS_DE_LOCALIZACAO\'".$_POST["estado_".$ficha]."/".$_POST["adverso_".$ficha]."-FICHA.rtf");
	//header("location:P:\\");
	
	echo "</form>";
?>
</body>
</html>
