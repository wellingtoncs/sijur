<?php
$htmlA = "";
$htmlB = "";
$table = "";
$table .= "<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<title>Acompanhamento dos Processos</title>";

$table .= "<style>
.piscar{
	font-weight:bold;
}
.titulo{
	color:blue; 
	text-align:center;
}
.emdia{
	color:blue; 
}
</style>
</head>
<body>";

$conexao1 = mysql_connect("192.168.2.12", "new_pwd_cons", "r567e234") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("abraz", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");

$query = mysql_query("SELECT c.nmcont
FROM cadastros_tb AS c
JOIN heventos_tb AS h ON h.cod_cli=c.cod_cad
JOIN eventos_tb AS e ON e.st=h.evento
JOIN processos_tb AS p ON p.numproc = h.numproc
WHERE c.cod_cli = 282 
AND h.evento IN (1006,1007,1269,1009,1250,1200,1089,1036,1228,1019,1021,1022,1244,1027,1247,1029,1201,1090,1335,1131,1280,1281,1032) 
AND p.acao IN ('BUAPR','REPOS','REBEM') 
AND p.autreu = 'A'
GROUP BY c.nmcont");

$arr_cad = "";
$num=0;
while($while=mysql_fetch_array($query)){
	if($num>0){
		$arr_cad .= ",";
	}
	$arr_cad .= "'";
	$arr_cad .= $while['nmcont'];
	$arr_cad .= "'";
	$num++;
}

//Estado de Isabelle: 
$qr = mysql_query("SELECT p.nmcont, c.nomecli, p.numproc,
concat(c.endresi,', ', c.nrresi,', ', c.compresi,', ', c.bairesi,', ', c.cidresi,' - ', c.estresi,', ', c.cepresi,', (', c.dddresi,') ', c.telresi) as 'EndRes',
concat(c.endcom,', ', c.nrcom,', ', c.compcom,', ', c.baicom,', ', c.cidcom,' - ', c.estcom,', ', c.cepcom,', (', c.dddcom,') ', c.telcom) as 'EndCom', 
concat('marca:',b.marca,', modelo:', b.modelo,', ano:',b.anofab,', cor:',b.cor,', chassi:',b.chassi,', placa:',b.placa) as bem,
c.cidresi, c.estresi, e.dst, h.data_h1, date_format(h.data_h1,'%d/%m/%Y') as dataeve 
FROM processos_tb AS p 
JOIN cadastros_tb AS c ON c.nmcont = p.nmcont 
JOIN heventos_tb AS h ON h.cod_cli=c.cod_cad 
JOIN eventos_tb AS e ON e.st=h.evento 
JOIN receber_tb as r ON r.nmcont=c.nmcont 
left join bens_tb as b on b.nmcont=c.nmcont 
WHERE c.cod_cli = 282 AND p.autreu = 'A' 
AND (r.stparc < 50 OR r.stparc IN ('105','109')) 
AND p.acao IN ('BUAPR','REPOS','REBEM') 
AND h.evento IN (1004,1237,1315)  
AND c.nmcont not in ($arr_cad)
GROUP BY c.cod_cad 
ORDER BY h.data_h1 DESC");
	
$tl=0;
$a =0;
$contratos = "";
while($wr = mysql_fetch_array($qr)){

	$timeiA = strtotime($wr['data_h1']);
	$timefA = strtotime(date("Y-m-d"));
	$difeA  = $timefA - $timeiA;
	$diasA  = (int)floor( $difeA / (60 * 60 * 24));
	
	if($diasA<30){ $trcolA = "class='emdia'";} else { $trcolA = ""; $tl++;}
	$a++;

	$htmlA .= "<tr $trcolA style='height:30px; text-align:center;' >
				<td style='width:80px'>" . $wr['nmcont'] 	. "</td>
				<td style='width:80px'>" . $wr['nomecli']	. "</td>
				<td style='width:80px'>" . $wr['numproc'] 	. "</td>
				<td style='width:80px'>" . $wr['EndRes'] 	. "</td>
				<td style='width:80px'>" . $wr['EndCom'] 	. "</td>
				<td style='width:80px'>" . $wr['bem'] 		. "</td>
				<td style='width:80px'>" . $wr['cidresi'] 	. "</td>
				<td style='width:80px'>" . $wr['estresi'] 	. "</td>
				<td style='width:80px'>" . $wr['dst'] 		. "</td>
				<td style='width:80px'>" . $wr['dataeve'] 	. "</td>
			  </tr>";
	$contratos .= ($a>1?",'":"'").$wr['nmcont']."'";
}

$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='990px' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:8pt; font-family:arial; width:990px'>";
$table .= "<tr>
			<th style='color:blue; width:80px' >Contrato</td>
			<th style='color:blue; width:80px' >Nome</td>
			<th style='color:blue; width:80px' >Dados do Processo</td>
			<th style='color:blue; width:80px' >End. Residencial</td>
			<th style='color:blue; width:80px' >End. Comercial</td>
			<th style='color:blue; width:80px' >Dados do Bem</td>
			<th style='color:blue; width:80px' >Comarca</td>
			<th style='color:blue; width:80px' >UF</td>
			<th style='color:blue; width:80px' >Evento</td>
			<th style='color:blue; width:80px' >Data Evento</td>
		  </tr>";
$table .= $htmlA;
$table .= "</table>";

header("Content-type: application/vnd.ms-excel");   
header("Content-type: application/force-download");  
header("Content-Disposition: attachment; filename=mandados.xls");
header("Pragma: no-cache");
  
echo $table;

?>