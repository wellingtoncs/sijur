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

$qr = mysql_query(" SELECT *,(5 - (datediff(curdate(),if(f.date_cad<>null, f.date_cad, f.date_start )))) as 'difdias', if(f.date_cad<>null, f.date_cad, f.date_start ) as 'datacad' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case	JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
	WHERE f.`type` = 'followups11' AND c.`status` = 'open' AND k.id_keyword = 71
	GROUP BY c.id_case
	ORDER BY f.date_start DESC ");
	
$tl=0;
$a =0;
$contratos = "";
while($wr = mysql_fetch_array($qr)){
	
	$qr2 = mysql_query(" SELECT *, max(f.id_followup) as 'andamentos' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
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
	WHERE f.`type` in ('followups23','followups24','followups44','followups17','followups10','followups67') AND c.`status` = 'open' AND c.id_case = '".$wr['id_case']."' AND f.date_start > '".$wr['date_start']."'
	GROUP BY c.id_case ORDER BY f.date_start DESC ");
		if(mysql_num_rows($qdilig)==0){
			
			$ulteveq = mysql_query("SELECT max(f.`type`) as 'ultevento', date_format(f.date_start,'%d/%m/%Y') as 'datestart' FROM lcm_followup as f where f.id_case = ".$wr['id_case']." and f.`type`in ('followups11','followups77','followups81','followups68')");
			$ultevew = mysql_fetch_array($ulteveq);
			
			$tipodeacao = preg_replace("/[^a-zA-Z0-9_]/i", "\\1", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
		
			if($tipodeacao=='BUSCAEAPREENS_O' || $tipodeacao=='REINTEGRA_ODEPOSSE' || $tipodeacao==''){
				
				//if(diasemana($wr['datacad'])==6) {
				//	$somadia = 2;
				//}elseif(diasemana($wr['datacad'])==7){
				//	$somadia = 2;
				//}else{
				//	$somadia = 2;
				//}
				$somadia = 2;
				$difdias = $wr['difdias'] + $somadia;
				$difdias>0?($corsrs = "background:green;color:#fff;border:1px solid #006400").($cor='color:blue'):($difdias==0?($corsrs="background:yellow;color:green;border:1px solid #DAA520").($cor='color:blue'):($corsrs="background:red;color:#fff;border:1px solid #B22222").($cor='color:red'));
				
				$a++;
				$htmlA .= "<tr $trcolA style='height:30px; text-align:center;' id='trs_$a' onclick='javascrip:$(\"#trs_$a\").attr(\"bgcolor\",\"yellow\");' >
						<td style='width:20px;$cor' class='cls_td'><a href='http://10.10.0.100/processos/case_det.php?case=" . $wr['id_case'] . "'  target='_blank' style='$cor'>" . htmlentities($wr['id_case'])	. "</a></td>
						<td style='width:80px;$cor'>" . htmlentities($wr['p_cliente']) 	. "</td>
						<td style='width:80px;$cor'>" . htmlentities($wr['p_adverso']) 	. "</td>
						<td style='width:80px;$cor'>" . htmlentities($andamentos['kw_followups_'.$ultevew['ultevento'].'_title']) . "</td>
						<td style='width:80px;$cor'>" . htmlentities($ultevew['datestart'])	. "</td>
						<td style='width:80px;$cor'>" . htmlentities($wr['comarca']) 	. "</td>
						<td style='width:80px;$cor'>" . htmlentities($wr['state']) 		. "</td>
						<td style='width:20px;$corsrs'>" . $difdias . "</td>
					  </tr>";
				echo "<script type='text/javascript'>
					$(function(){   
					$('#trs_$a').dblclick(function(){
						$(\"#trs_$a\").attr(\"bgcolor\",\"#ffffff\");
					});
				});</script>";

			}
		}
}

$table .= "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:9pt; font-weight:bold; font-family:arial'>
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
			</tr>";
$table .= "</table>";
$table .= "<tr><td>";
$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; font-size:7pt; font-family:arial;'>";
$table .= "<tr>
			<th style='color:blue; width:20px' class='comFiltro' >Pasta</td>
			<th style='color:blue; width:80px' class='comFiltro' >Banco</td>
			<th style='color:blue; width:80px' class='comFiltro' >Adverso</td>
			<th style='color:blue; width:80px' class='comFiltro' >Evento</td>
			<th style='color:blue; width:80px' class='comFiltro' >Data</td>
			<th style='color:blue; width:80px' class='comFiltro' >Comarca</td>
			<th style='color:blue; width:80px' class='comFiltro' >Estado</td>
			<th style='color:blue; width:20px' class='comFiltro' >SRS</td>
		  </tr>";
$table .= $htmlA;
$table .= "</table>";
$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; font-size:7pt; font-family:arial;'>";
$table .= "<tr><td colspan='12' style='color:blue'>Total de mandados: $a  </td></tr>";
$table .= "</table>";
$table .= "<br><br>";

echo $table;

?>