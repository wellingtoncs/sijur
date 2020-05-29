<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr" >
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title>Fichas</title> 
</head>
<body>

<?php
	include('../php/functions.php');
	$conexao2 = mysql_connect("localhost", "fabio", "torres@#",TRUE) or die("MySQL: Não foi possível conectar-se ao servidor.");
	mysql_select_db("contratos_db", $conexao2) or die("MySQL: Não foi possível conectar-se ao banco de dados.");

	
	echo "<form action='index.php' method='post'>";	

		$ficha = $_POST['ficha'];
		$filename = "modelo_de_ficha.rtf";
		$fp=fopen($filename,'r');
		$output=fread($fp,filesize($filename));
		fclose($fp);
		
		$Qend = mysql_query("SELECT 
						(select CONCAT_WS(' ',e1.logradouro,' ', e1.number,'',e1.complement,' ',e1.district,' ',e1.city,' ',e1.state,' CEP:',e1.zipcode,' FONES: ',e1.fone_1,' ',e1.fone_2) from lcm_cont_lograd as e1 where e1.id_cont = e.id_cont and `type`='resi' ) as 'resi',
						(select CONCAT_WS(' ',e2.logradouro,' ', e2.number,'',e2.complement,' ',e2.district,' ',e2.city,' ',e2.state,' CEP:',e2.zipcode,' FONES: ',e2.fone_1,' ',e2.fone_2) from lcm_cont_lograd as e2 where e2.id_cont = e.id_cont and `type`='com' ) as 'com',
						(select CONCAT_WS(' ',e3.logradouro,' ', e3.number,'',e3.complement,' ',e3.district,' ',e3.city,' ',e3.state,' CEP:',e3.zipcode,' FONES: ',e3.fone_1,' ',e3.fone_2) from lcm_cont_lograd as e3 where e3.id_cont = e.id_cont and `type`='other' ) as 'other'
						from lcm_cont_lograd as e where e.id_cont = '".$_POST['idcont_'.$ficha]."' and e.logradouro <> '' limit 1 ",$conexao2);
						
		$Wend = mysql_fetch_array($Qend);		
		$idcont = $_POST["idcont_".$ficha];
		
		$Qfow = mysql_query("SELECT *, date_format(fu.date_start, '%d/%m/%Y') as 'dataatual' from lcm_cont_followup as fu where fu.id_case = '". $idcont ."' order by fu.date_start DESC ",$conexao2);
		
		$followup = "";
		while($Wfow = mysql_fetch_array($Qfow)){
			$followup .= $Wfow['dataatual'] . " | ".$andamentos['kw_followups_'.$Wfow['type'].'_title'] . " | ".$Wfow['description'] . "\\par ___________________________________________________________________________________________ ";
		}

		$output=str_replace("<<banco>>",		trim($_POST["banco_".$ficha]),$output);
		$output=str_replace("<<processo>>",		trim($_POST["processo_".$ficha]),$output);
		$output=str_replace("<<vara>>",			trim($_POST["vara_".$ficha]),$output);
		$output=str_replace("<<comarca>>",		trim($_POST["comarca_".$ficha]),$output);
		$output=str_replace("<<estado>>",		trim($_POST["estado_".$ficha]),$output);
		$output=str_replace("<<cpfcnpj>>",		trim($_POST["cpfcnpj_".$ficha]),$output);
		$output=str_replace("<<adverso>>",		trim($_POST["adverso_".$ficha]),$output);
		$output=str_replace("<<veiculo>>",		trim($_POST["veiculo_".$ficha]),$output);
		$output=str_replace("<<endereco1>>",	trim($Wend["resi"]?$Wend["resi"]:''),$output);
		$output=str_replace("<<endereco2>>",	trim($Wend["com"]?$Wend["com"]:''),$output);
		$output=str_replace("<<endereco3>>",	trim($Wend["other"]?$Wend["other"]:''),$output);
		$output=str_replace("<<profissao>>",	trim($_POST["profissao_".$ficha]),$output);
		$output=str_replace("<<conjuge>>",		trim($_POST["conjuge_".$ficha]),$output);
		$output=str_replace("<<endereco>>",		trim($_POST["endereco_".$ficha]),$output);
		$output=str_replace("<<tel>>",			trim($_POST["tel_".$ficha]),$output);
		$output=str_replace("<<oficial>>",		trim($_POST["oficial_".$ficha]),$output);
		$output=str_replace("<<expediente>>",	trim($_POST["expediente_".$ficha]),$output);
		$output=str_replace("<<outros>>",		trim($_POST["outros_".$ficha]),$output);
		$output=str_replace("<<historico>>",	trim($followup),$output);
		
		
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
