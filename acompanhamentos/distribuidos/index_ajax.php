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

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");

$qr = mysql_query(" SELECT *, date_format(f.date_start,'%d/%m/%Y') as dataeve, date_format(f.date_cad,'%d/%m/%Y') as datacad FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
	WHERE f.`type` = 'followups24' AND c.`status` = 'open' AND k.id_keyword = 71
	GROUP BY c.id_case
	ORDER BY f.date_cad DESC ");
	
$tl=0;
$a =0;
$contratos = "";
while($wr = mysql_fetch_array($qr)){
	
	$qdilig = mysql_query(" SELECT c.id_case FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case
		WHERE f.`type` in ('followups31','followups33','followups35','followups41','followups42','followups43','followups44','followups45','followups23','followups17') AND c.`status` = 'open' AND c.id_case = '".$wr['id_case']."' AND f.date_start > '".$wr['date_start']."'
		GROUP BY c.id_case
		ORDER BY f.date_cad DESC ");
	
	if(mysql_num_rows($qdilig)==0){
		
		$tipodeacao = preg_replace("/[^a-zA-Z0-9_]/i", "\\1", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
		$comarca = strtolower(preg_replace("/[^a-zA-Z0-9_]/i", "\\1", strtr($wr['comarca'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC ")));
		
		if($tipodeacao=='BUSCAEAPREENS_O' || $tipodeacao=='REINTEGRA_ODEPOSSE' || $tipodeacao==''){
		
		
		switch(htmlentities($wr['state'])){
			case "RN":
				$linktj = "<a href='http://esaj.tjrn.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dePesquisa=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
				$fntjal = htmlentities($wr['vara']);
				break;
			case "PE":
				$linktj = "<a href='http://srv01.tjpe.jus.br/consultaprocessualunificada/xhtml/consulta.xhtml?processo=".str_replace("-","",str_replace(".","",htmlentities($wr['processo'])))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
				$fntjal = htmlentities($wr['vara']);
				break;
			case "PI":
				$linktj = "<a href='http://www.tjpi.jus.br/themisconsulta/' target='_blank'>".htmlentities($wr['processo'])."</a>";
				$fntjal = htmlentities($wr['vara']);
				break;
			case "PB":
				$linktj = "<a href='https://pje.tjpb.jus.br/pje/ConsultaPublica/listView.seam' target='_blank'>".htmlentities($wr['processo'])."</a>";
				$fntjal = htmlentities($wr['vara']);
				$fntjal = "";
				break;
			case "AP":
				$linktj = "<a href='http://app.tjap.jus.br/tucujuris/publico/processo/index.xhtml?consNumeroUnicoJustica=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."&consNomeParte=&speed=true' target='_blank'>".htmlentities($wr['processo'])."</a>";
				$fntjal = htmlentities($wr['vara']);
				break;
			case "CE":
				$linktj = "<a href='http://esaj.tjce.jus.br/cpopg/open.do?paginaConsulta=1&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&dadosConsulta.tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dadosConsulta.valorConsulta=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
				$fntjal = htmlentities($wr['vara']);
				break;
			case "AL":
				$linktj = "<a href='http://www2.tjal.jus.br/cpopg/open.do?paginaConsulta=1&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&dadosConsulta.tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dadosConsulta.valorConsulta=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
				$fntjal = "<a href='http://www.tjal.jus.br/?pag=consultas/enderecosComarca&cidade=" . $cod_fnal[$comarca] . "' target='_blank' >" . htmlentities($wr['vara']) . "-". $comarca ."</a>";
				break;
		}
		
		
			$a++;
			$htmlA .= "<tr $trcolA style='height:30px; text-align:center;' >
					<td style='width:80px' class='cls_td'><a href='/processos/case_det.php?case=" . $wr['id_case'] . "'  target='_blank'>" . htmlentities($wr['id_case'])	. "</a></td>
					<td style='width:80px'>" . htmlentities($wr['p_cliente']) 	. "</td>
					<td style='width:80px'>" . htmlentities($wr['p_adverso']) 	. "</td>
					<td style='width:80px'>" . $linktj	. "</td>
					<td style='width:80px'>" . $fntjal		. "</td>
					<td style='width:80px'>" . htmlentities($wr['legal_reason']). "</td>
					<td style='width:80px'>DISTRIBU&Iacute;DOS</td>
					<td style='width:80px' title='Cadastrado em: " . $wr['datacad'] . "'>" . $wr['dataeve']. "</td>
					<td style='width:80px'>" . htmlentities($wr['comarca']) 	. "</td>
					<td style='width:80px'>" . htmlentities($wr['state']) 		. "</td>
				  </tr>";
		}
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
			<th style='color:blue; width:80px' class='comFiltro' >Comarca</td>
			<th style='color:blue; width:80px' class='comFiltro' >Estado</td>
		  </tr>";
$table .= $htmlA;
$table .= "<tr><td colspan='12' style='color:blue'>Total de mandados: $a  </td></tr>";
$table .= "</table></table><br><br>";

echo $table;

?>