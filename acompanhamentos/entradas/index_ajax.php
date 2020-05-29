<?php

include('../php/functions.php');

$htmlA = "";
$htmlB = "";
$table = "";
$table .= "<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<title>Acompanhamento dos Processos</title>
<script type='text/javascript' src='../js/jquery-1.8.3.min.js'></script>
<script type='text/javascript' src='../js/jFilterXCel2003.js'></script>
<script type='text/javascript'>
$(window).load(function(){
	function blink(selector) {
		$(selector).fadeOut('slow', function() {
			$(this).fadeIn('slow', function() {
				blink(this);
			});
		});
	}
	blink('.piscar');
	carregarFiltros('tbf1');
});

</script>";

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

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");

$qr = mysql_query(" SELECT * FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case	JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
	WHERE f.`type` = 'followups11' AND c.`status` = 'open' AND k.id_keyword = 71
	GROUP BY c.id_case
	ORDER BY f.date_start DESC ");
	
$tl=0;
$a =0;
$contratos = "";
while($wr = mysql_fetch_array($qr)){
	
	$qr2 = mysql_query(" SELECT *, max(f.id_followup) as 'andamentos' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case	JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
	WHERE c.id_case = " . $wr['id_case'] . "
	GROUP BY c.id_case
	ORDER BY f.date_start DESC ");
	$wr2 = mysql_fetch_array($qr2);
	
	$qandam = mysql_query(" SELECT *, 
	date_format(f.date_start,'%d/%m/%Y') as data_atual, 
	date_format(f.date_cad,'%d/%m/%Y') as data_cad,
	datediff(curdate(),f.date_cad) as 'diff_dias'
	FROM lcm_followup AS f where f.id_followup = " . $wr2['andamentos']);
	$wdilig = mysql_fetch_array($qandam);
	
	$qdilig = mysql_query(" SELECT c.id_case 
	FROM lcm_case AS c
	JOIN lcm_followup AS f ON f.id_case = c.id_case
	WHERE f.`type` in ('followups23','followups24','followups44','followups17','followups10') AND c.`status` = 'open' AND c.id_case = '".$wr['id_case']."' AND f.date_start > '".$wr['date_start']."'
	GROUP BY c.id_case
	ORDER BY f.date_start DESC ");
		if(mysql_num_rows($qdilig)==0){
			
			$tipodeacao = preg_replace("/[^a-zA-Z0-9_]/i", "\\1", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
		
			if($tipodeacao=='BUSCAEAPREENS_O' || $tipodeacao=='REINTEGRA_ODEPOSSE' || $tipodeacao==''){
			
				$a++;
				$htmlA .= "<tr $trcolA style='height:30px; text-align:center;' >
						<td style='width:80px' class='cls_td'><a href='/processos/case_det.php?case=" . $wr['id_case'] . "'  target='_blank'>" . htmlentities($wr['id_case'])	. "</a></td>
						<td style='width:80px'>" . htmlentities($wr['p_cliente']) 	. "</td>
						<td style='width:80px'>" . htmlentities($wr['p_adverso']) 	. "</td>
						<td style='width:80px'>" . htmlentities($andamentos['kw_followups_'.$wdilig['type'].'_title']) . "</td>
						<td style='width:80px'>" . htmlentities($wr['date_start']). "</td>
						<td style='width:80px'>" . htmlentities($wr['comarca']) 	. "</td>
						<td style='width:80px'>" . htmlentities($wr['state']) 		. "</td>
					  </tr>";
			}
		}
}

$table .= "<table align='center' width='990px' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:9pt; font-weight:bold; font-family:arial'>
			<tr>
				<td align='center'>ACOMPANHAMENTO DO QUE ESTÁ AGUARDANDO A DISTRIBUIÇÃO NO SIJUR - $a 
					<!--span style='float:right'>
						<a href='#' onclick='window.open(\"../emails/index.php\", \"Pagina\", \"STATUS=NO, TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=NO, TOP=10, LEFT=10, WIDTH=810, HEIGHT=570\");'>Emails das Filiais</a>
					</span-->
					<br>
					<!--span style='float:right'>
						<a href='index_excel.php' >Base em Excel</a>
					</span-->
					<div id='id_sel' style='font-size:9pt;font-weight:normal'><i>Total selecionado: $a</i></div> 
				</td>
			</tr>
			<tr><td>";
$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='990px' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:7pt; font-family:arial; width:990px'>";
$table .= "<tr>
			<th style='color:blue; width:80px' class='comFiltro' >Pasta</td>
			<th style='color:blue; width:80px' class='comFiltro' >Banco</td>
			<th style='color:blue; width:80px' class='comFiltro' >Adverso</td>
			<th style='color:blue; width:80px' class='comFiltro' >Evento</td>
			<th style='color:blue; width:80px' class='comFiltro' >Data</td>
			<th style='color:blue; width:80px' class='comFiltro' >Comarca</td>
			<th style='color:blue; width:80px' class='comFiltro' >Estado</td>
		  </tr>";
$table .= $htmlA;
$table .= "<tr><td colspan='12' style='color:blue'>Total de mandados: $a  </td></tr>";
$table .= "</table></table><br><br>";

echo $table;

?>