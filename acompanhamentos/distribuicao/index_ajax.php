<?php

include('../php/functions.php');

$htmlA = "";
$htmlB = "";
$table = "";
$table .= "<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
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
</script>
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

$meses = array(1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',5=>'Mai',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Set',10=>'Out',11=>'Nov',12=>'Dez',);

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");

$qr = mysql_query(" SELECT *, 
	month(f.date_start) as mes, day(f.date_start) as dia, year(f.date_start) as ano,
	date_format(f.date_start,'%d/%m/%Y') as dataeve, 
	date_format(f.date_cad,'%d/%m/%Y') as datacad,
	SUM(IF(UNIX_TIMESTAMP(f.date_end) > 0, UNIX_TIMESTAMP(f.date_end) - UNIX_TIMESTAMP(f.date_start), 0)) AS TIME, 
	SUM(sumbilled) AS sumbilled
	FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
	WHERE f.`type` = 'followups24' AND k.id_keyword = 71
	GROUP BY c.id_case
	ORDER BY f.date_cad DESC ");
	
$tl=0;
$a =0;
$contratos = "";
while($wr = mysql_fetch_array($qr)){
	

		
		$tipodeacao = preg_replace("/[^a-zA-Z0-9_]/i", "\\1", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
		
		if($tipodeacao=='BUSCAEAPREENS_O' || $tipodeacao=='REINTEGRA_ODEPOSSE' || $tipodeacao==''){
		
			$a++;
			$htmlA .= "<tr $trcolA style='height:30px; text-align:center;' >
					<td style='width:80px' class='cls_td'><a href='/processos/case_det.php?case=" . $wr['id_case'] . "'  target='_blank'>" . htmlentities($wr['id_case'])	. "</a></td>
					<td style='width:80px'>" . htmlentities($wr['p_cliente']) 	. "</td>
					<td style='width:80px'>" . htmlentities($wr['p_adverso']) 	. "</td>
					<td style='width:80px'>" . htmlentities($wr['processo'])	. "</td>
					<td style='width:80px'>" . htmlentities($wr['vara']) 		. "</td>
					<td style='width:80px'>" . htmlentities($wr['legal_reason']). "</td>
					<td style='width:80px'>".htmlentities($andamentos['kw_followups_'.$wr['type'].'_title'])."</td>
					<td style='width:80px' title='Cadastrado em: " . $wr['datacad'] . "'>" . $wr['dataeve']. "</td>
					<td style='width:80px' title='Cadastrado em: " . $wr['datacad'] . "'>" . $meses[$wr['mes']]. "</td>
					<td style='width:80px' title='Cadastrado em: " . $wr['datacad'] . "'>" . $wr['ano']. "</td>
					<td style='width:80px'>" . htmlentities($wr['comarca']) 	. "</td>
					<td style='width:80px'>" . htmlentities($wr['state']) 		. "</td>
					<td style='width:80px'>" . htmlentities(format_time_interval_prefs($wr['TIME'])) . "</td>
				  </tr>";
		}
	
}

$table .= "<table align='center' width='990px' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:9pt; font-weight:bold; font-family:arial'>
	<tr>
		<td align='center'>ACOMPANHAMENTO DAS DISTRIBUIÇÕES SE LIMINARES NO SIJUR - $a 
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
			<th style='color:blue; width:80px' class='comFiltro' >Processo</td>
			<th style='color:blue; width:80px' class='comFiltro' >Vara</td>
			<th style='color:blue; width:80px' class='comFiltro' >Ação</td>
			<th style='color:blue; width:80px' class='comFiltro' >Evento</td>
			<th style='color:blue; width:80px' class='comFiltro' >Data</td>
			<th style='color:blue; width:80px' class='comFiltro' >Mês</td>
			<th style='color:blue; width:80px' class='comFiltro' >Ano</td>
			<th style='color:blue; width:80px' class='comFiltro' >Comarca</td>
			<th style='color:blue; width:80px' class='comFiltro' >Estado</td>
			<th style='color:blue; width:80px' class='comFiltro' >Fat.</td>
		  </tr>";
$table .= $htmlA;
$table .= "<tr><td colspan='12' style='color:blue'>Total de mandados: $a  </td></tr>";
$table .= "</table></table><br><br>";

echo $table;

?>