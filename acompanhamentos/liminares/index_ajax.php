<?php

include('../php/functions.php');

$estados = array("PE","AL","AP","RN","CE","PB","PI");
$fichas = array();

FOREACH($estados AS $ufs){
	$pasta = "E://Publico/_BANCO GMAC - BUSCA/FICHAS_DE_LOCALIZACAO/".$ufs;
	if(is_dir($pasta))
	{
		$diretorio = dir($pasta);
		while($arquivo = $diretorio->read())
		{
			$arq1 = STR_REPLACE(".doc","",$arquivo);
			$arq1 = STR_REPLACE(".rtf","",$arquivo);
			$arq1 = STR_REPLACE("FICHA","",$arq1);
			$arq1 = STR_REPLACE("FICHA DE","",$arq1);
			$arq1 = STR_REPLACE("DE LOCALIZAÇÃO","",$arq1);
			$arq1 = explode("-",STR_REPLACE("DE LOCALIZACAO","",$arq1));
			$fichas[] = $ufs."-".trim(str_replace(" ","_",preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($arq1[0])))));
		}
		$diretorio->close();
	}
}

$htmlA = "";
$htmlB = "";
$table = "";
$table .= "<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<title>Acompanhamento das liminares</title>
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
</style></head><body>";
$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor.");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados .");

$conexao2 = mysql_connect("localhost", "fabio", "torres@#",TRUE) or die("MySQL: Não foi possível conectar-se ao servidor.");
mysql_select_db("contratos_db", $conexao2) or die("MySQL: Não foi possível conectar-se ao banco de dados.");

$qr = mysql_query(" SELECT * FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
	WHERE f.`type` = 'followups31' AND c.`status` = 'open' AND k.id_keyword = 71
	AND f.id_followup = (SELECT MAX(ff.id_followup) FROM lcm_followup AS ff WHERE ff.id_case = c.id_case AND ff.`type` = 'followups31')
	GROUP BY c.id_case
	ORDER BY f.date_start DESC ",$conexao1);
	
$tl=0;
$a =0;
$contratos = "";
while($wr = mysql_fetch_array($qr)){
	
	$qdilig = mysql_query(" SELECT c.id_case 
	FROM lcm_case AS c
	JOIN lcm_followup AS f ON f.id_case = c.id_case
	WHERE f.`type` in ('followups21','followups33','followups35','followups45','followups23') AND c.`status` = 'open' AND c.id_case = '".$wr['id_case']."' AND f.date_start > '".$wr['date_start']."'
	GROUP BY c.id_case
	ORDER BY f.date_start DESC ",$conexao1);
	
	if(mysql_num_rows($qdilig)==0){
		
		$tipodeacao = preg_replace("/[^a-zA-Z0-9_]/i", "\\1", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
		//pega o cpf do cliente do processo
		$QCasecpf = mysql_query("SELECT replace(replace(a.cpfcnpj,'.',''),'-','') as 'cpfcnpj' FROM lcm_case_adverso_cliente AS ac JOIN lcm_adverso AS a ON a.id_adverso=ac.id_adverso WHERE ac.id_case ='". $wr['id_case'] ."' ",$conexao1);
		$WCasecpf = mysql_fetch_array($QCasecpf);
		
		$QContcpf = mysql_query("SELECT cac.id_case FROM lcm_cont_adverso_cliente AS cac JOIN lcm_adverso AS ca ON ca.id_adverso=cac.id_adverso WHERE  replace(replace(ca.cpfcnpj,'.',''),'-','') = '". $WCasecpf['cpfcnpj'] ."' ",$conexao2);
		$WContcpf = mysql_fetch_array($QContcpf);
		//para os dados do vaículo		
		$Qbem = mysql_query("SELECT * from lcm_cont_bem as b where b.id_cont = '". $WCasecpf['cpfcnpj'] ."' limit 1 ",$conexao2);
		$Wbem = mysql_fetch_array($Qbem);
		
		$Qloc = mysql_query("SELECT f.description,datediff(curdate(),f.date_start) as 'diff_dias' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
								WHERE f.`type` = 'followups62' AND c.`status` = 'open' AND k.id_keyword = 71
								and c.id_case = '" . $wr['id_case'] . "'
								ORDER BY f.date_start DESC ",$conexao1);
		$Wloc = mysql_fetch_array($Qloc);
		
		//Criar link dos Tjs
		switch(htmlentities($wr['state'])){
			case "RN":
				$linktj = "<a href='http://esaj.tjrn.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dePesquisa=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
				break;
			case "PE":
				$linktj = "<a href='http://srv01.tjpe.jus.br/consultaprocessualunificada/xhtml/consulta.xhtml?processo=".str_replace("-","",str_replace(".","",htmlentities($wr['processo'])))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
				break;
			case "PI":
				$linktj = "<a href='http://www.tjpi.jus.br/themisconsulta/' target='_blank'>".htmlentities($wr['processo'])."</a>";
				break;
			case "PB":
				$linktj = "<a href='https://pje.tjpb.jus.br/pje/ConsultaPublica/listView.seam' target='_blank'>".htmlentities($wr['processo'])."</a>";
				break;
			case "AP":
				$linktj = "<a href='http://app.tjap.jus.br/tucujuris/publico/processo/index.xhtml?consNumeroUnicoJustica=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."&consNomeParte=&speed=true' target='_blank'>".htmlentities($wr['processo'])."</a>";
				break;
			case "CE":
				$linktj = "<a href='http://esaj.tjce.jus.br/cpopg/open.do?paginaConsulta=1&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&dadosConsulta.tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dadosConsulta.valorConsulta=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
				break;
			case "AL":
				$linktj = "<a href='http://www2.tjal.jus.br/cpopg/open.do?paginaConsulta=1&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&dadosConsulta.tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dadosConsulta.valorConsulta=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
				break;
		}
		
		if($tipodeacao=='BUSCAEAPREENS_O' || $tipodeacao=='REINTEGRA_ODEPOSSE' || $tipodeacao==''){
			
			if($Wloc['diff_dias']<16 && $Wloc['diff_dias']!=''){
				$trcolA = "color:blue";
			}else{
				$trcolA = "color:red";
				$m++;
			}
			
			$a++;
			$description = str_replace("LOCALIZADOR:","",STRTOUPPER($Wloc['description']));
			
			//verifica se a ficha foi criada
			$comarca = $wr['comarca'];
			$comarca = str_replace(" ","_",preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($comarca))));
			$adverso = $wr['p_adverso'];
			$adverso = str_replace(" ","_",preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($adverso))));
			
			$filename = "E://Publico/_BANCO GMAC - BUSCA/FICHAS_DE_LOCALIZACAO/".htmlentities($wr['state'])."/".$adverso."-FICHA_DE_LOCALIZACAO-".$comarca.".rtf";
			if (file_exists($filename)){
				$botton_ficha = "OK!";
			} else {
				if (in_array(htmlentities($wr['state']) . "-".$adverso, $fichas)) {
					$botton_ficha = "OK!";
				} else {
					$botton_ficha = "<button type='submit' name='ficha' value='$a' style='cursor:pointer; border:0px solid #ccc; font-size:9px;$trcolA'>Ficha</button>";
				}
			}

			$htmlA .= "<tr style='height:30px; text-align:center; $trcolA'>
				
					<td style='width:80px' class='cls_td'>" . ($WContcpf['id_case']!=''?"<a href='/contratos/case_det.php?case=" . $WContcpf['id_case'] . "'  target='_blank' style='$trcolA'>" . htmlentities($wr['p_adverso']) . "</a>":htmlentities($wr['p_adverso'])). "</td>
					<td style='width:80px'>" . htmlentities($wr['p_cliente'])	. "</td>
					<td style='width:80px'>" . $linktj	. "</td>
					<td style='width:80px'>" . htmlentities($wr['vara']) 		. "</td>
					<td style='width:80px'>" . htmlentities($wr['legal_reason']). "</td>
					<td style='width:80px'><a href='/processos/case_det.php?case=" . $wr['id_case'] . "'  target='_blank' style='$trcolA'>" . htmlentities('Liminar Deferida') . "</a></td>
					<td style='width:80px'>" . htmlentities($wr['date_start'])	. "</td>
					<td style='width:80px'>" . htmlentities($wr['comarca']) 	. "</td>
					<td style='width:80px'>" . htmlentities($wr['state']) 		. "</td>
					<td style='width:80px'>" . ($description==''?'SEM LOCALIZADOR':htmlentities($description)). "</td>
					<td style='width:80px'>$botton_ficha
				
					<!--a href='http://www.detran.pe.gov.br/index.php?option=com_search_placa&placa=".$Wbem['carrier']."' style='cursor:pointer; border:0px solid #ccc' target='_blank'>".$Wbem['carrier']." Detan</a-->";
						echo "<input type='hidden' name='banco_$a' value='" . htmlentities($wr['p_cliente']) . "'/>";
						echo "<input type='hidden' name='processo_$a' value='" . htmlentities($wr['processo']) . "'/>";
						echo "<input type='hidden' name='adverso_$a' value='" . htmlentities($wr['p_adverso']) . "'/>";
						echo "<input type='hidden' name='vara_$a' value='" . htmlentities($wr['vara']) . "'/>";
						echo "<input type='hidden' name='comarca_$a' value='" . htmlentities($wr['comarca']) . "'/>";
						echo "<input type='hidden' name='estado_$a' value='" . htmlentities($wr['state']) . "'/>";
						echo "<input type='hidden' name='cpfcnpj_$a' value='" . $WCasecpf['cpfcnpj'] . "'/>";
					echo "</td>";
				  echo "</tr>";
		}
	}
}

$table .= "<table align='center' width='990px' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:9pt; font-weight:bold; font-family:arial'>
			<tr>
				<td align='center'>PLANILHA DE LIMINARES EXPEDIDAS NO SIJUR - $a 
					<span style='float:right'>
					<!--a href='#' onclick='window.open(\"../emails/index.php\", \"Pagina\", \"STATUS=NO, TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=NO, TOP=10, LEFT=10, WIDTH=810, HEIGHT=570\");'>Emails das Filiais</a-->
					</span>
					<br>
					<!--span style='float:right'><a href='index_excel.php' >Base em Excel</a></span-->
					<div id='id_sel' style='font-size:9pt;font-weight:normal'><i>Total selecionado: $a</i></div> 
				</td>
			</tr>
			<tr>
				<td>";
$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='990px' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:#000; font-size:7pt; font-family:arial; width:990px'>";
$table .= "<tr>
			<th style='color:blue; width:80px' class='comFiltro' >Adverso</td>
			<th style='color:blue; width:80px' class='comFiltro' >Banco</td>
			<th style='color:blue; width:80px' class='comFiltro' >Processo</td>
			<th style='color:blue; width:80px' class='comFiltro' >Vara</td>
			<th style='color:blue; width:80px' class='comFiltro' >Ação</td>
			<th style='color:blue; width:80px' class='comFiltro' >Evento</td>
			<th style='color:blue; width:80px' class='comFiltro' >Data</td>
			<th style='color:blue; width:80px' class='comFiltro' >Comarca</td>
			<th style='color:blue; width:80px' class='comFiltro' >Estado</td>
			<th style='color:blue; width:80px' class='comFiltro' >Localizador</td>
			<th style='color:blue; width:80px' >Ficha</td>
		  </tr>";
$table .= $htmlA;
$table .= "<tr><td colspan='12' style='color:blue'>Total de mandados: $a  </td></tr>";
$table .= "</table>";
echo "</table><br><br>";

echo $table;

?>