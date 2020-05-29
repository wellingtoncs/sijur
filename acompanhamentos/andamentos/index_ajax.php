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
$('#mapa').click(function(){
	$('#mapa').html('<img src=\'../img/aguarde_g.gif\' style=\'width:100px\' />');
	var arr_cont='';
	var nn=0;
	$( '.cls_td' ).each(function( index ) {
		if($(this).is(':Visible')){	
			if(nn>0){
				arr_cont += '_|_';
			}
			arr_cont += $(this).html();
			nn++;
		}
	});
	window.open('http://www.direito2010.com.br/gmapa/cria_jason.php?contratos='+arr_cont, 'Pagina', 'STATUS=NO, TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=NO, TOP=10, LEFT=10, WIDTH=810, HEIGHT=570');
	
	$('#mapa').html('Mapa dos mandados ');
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
.semFiltro{
	font-size:12px;
	text-align:left;
}
</style>
</head>
<body>";

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");

$conexao2 = mysql_connect("localhost", "fabio", "torres@#",TRUE) or die("MySQL: Não foi possível conectar-se ao servidor.");
mysql_select_db("contratos_db", $conexao2) or die("MySQL: Não foi possível conectar-se ao banco de dados.");

$qr = mysql_query(" SELECT *, max(f.id_followup) as 'andamentos',
					(SELECT ff.system_name FROM lcm_followup as ff where ff.id_followup=MAX(f.id_followup)) as 'meusrs'
					FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
					WHERE c.`status` = 'open' AND k.id_keyword = 71 AND c.processo != '' AND f.`type` not in ('other','followups62','followups83','followups84')  
					AND c.p_cliente REGEXP 'BANCO GMAC|SAFRA' 
					GROUP BY c.id_case	ORDER BY max(f.date_cad) asc ",$conexao1);
	
$com = "";
$obs = "";
$tl  = 0;
$m   = 0;
$a   = 0;
$nl = mysql_num_rows($qr);
$esc = 0;
while($wr = mysql_fetch_array($qr)){
	
	$qdilig = mysql_query(" SELECT *, 
		date_format(f.date_start,'%d/%m/%Y') as data_atual, 
		date_format(f.date_cad,'%d/%m/%Y') as data_cad,
		datediff(curdate(),f.date_cad) as 'diff_dias'
		FROM lcm_followup AS f where f.id_followup = " . $wr['andamentos'] . " AND f.`type`!='other' ",$conexao1);
	$wdilig = mysql_fetch_array($qdilig);

	$qusu = mysql_query("SELECT * FROM lcm_author as a where a.id_author = '".$wdilig['id_author']."' ",$conexao1);
	$wusu = mysql_fetch_array($qusu);
	
	$srsq = mysql_query("SELECT kc.value FROM lcm_keyword_case as kc where kc.id_case = '".$wr['id_case']."' and kc.value != '' and kc.id_keyword = 147 limit 1 ",$conexao1);
	$srsw = mysql_fetch_array($srsq);
	//pega o cpf do cliente do processo
	$QCasecpf = mysql_query("SELECT replace(replace(a.cpfcnpj,'.',''),'-','') as 'cpfcnpj' FROM lcm_case_adverso_cliente AS ac JOIN lcm_adverso AS a ON a.id_adverso=ac.id_adverso WHERE ac.id_case ='". $wr['id_case'] ."' ",$conexao1);
	$WCasecpf = mysql_fetch_array($QCasecpf);
	
	$QContcli = mysql_query(" SELECT cac.id_case 
							  FROM lcm_cont_adverso_cliente AS cac
							  JOIN lcm_adverso AS ca ON ca.id_adverso=cac.id_adverso
							  JOIN lcm_cont_followup AS f ON f.id_case=cac.id_case
							  WHERE REPLACE( REPLACE(ca.cpfcnpj,'.',''),'-','') = '". $WCasecpf['cpfcnpj'] ."'
							  and f.`type` in ('followups12','followups15','followups23','followups67','followups25','followups29','followups51','followups30')
							  limit 1 ",$conexao2);
							  
	$WContcli = mysql_num_rows($QContcli);
	
	$QContAdv = mysql_query(" SELECT cac.id_case 
							  FROM lcm_cont_adverso_cliente AS cac
							  JOIN lcm_adverso AS ca ON ca.id_adverso=cac.id_adverso
							  JOIN lcm_cont_followup AS f ON f.id_case=cac.id_case
							  WHERE REPLACE( REPLACE(ca.cpfcnpj,'.',''),'-','') = '". $WCasecpf['cpfcnpj'] ."'
							  and f.`type` = 'followups53' limit 1 ",$conexao2);
	
	$WContAdv = mysql_num_rows($QContAdv);
	
	//retira os que tem veículos apreendidos
	$Qapre = mysql_query(" SELECT * FROM lcm_followup AS f WHERE f.`type` in ('followups45') AND f.id_case = '". $wr['id_case'] ."' limit 1 ",$conexao1);
	if(mysql_num_rows($Qapre)==0){
		//verifica  o que tem veículo localizado
		$Qloca = mysql_query("SELECT f.id_case FROM lcm_followup AS f where f.id_case = ". $wr['id_case'] ." and f.description like '%LO LOCALIZADO %' ",$conexao1);
		$Wloca = mysql_num_rows($Qloca);		
	}else { $Wloca=0; }
	
	//verifica o que tem apenso a revisional
	$Qadve = mysql_query(" SELECT * FROM lcm_followup AS f WHERE f.`type` in ('followups17') AND f.id_case = '". $wr['id_case'] ."' limit 1 ",$conexao1);
	$Wadve = mysql_num_rows($Qadve);
	
	if($Wadve>0 || $WContAdv>0){
		$temadv = "SIM";
	} else {
		$temadv = "NÃO";
	}
	
	$tipodeacao = tiraAcento($wr['legal_reason']);
		
	
	if($tipodeacao=='BUSCA_E_APREENSAO' || $tipodeacao=='REINTEGRACAO_DE_POSSE' || $tipodeacao=='DEPOSITO' || $tipodeacao==''){
			
			if($wdilig['diff_dias']<25 && $wdilig['diff_dias']!=''){
				$trcolA = "color:blue";
				$cor = "A";
			}else{
				$trcolA = "color:red";
				$cor = "V";
				$m++;
			}
			
			switch(htmlentities($wr['state'])){
				
				case "AL":
					$linktj = "<a href='http://www2.tjal.jus.br/cpopg/open.do?paginaConsulta=1&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&dadosConsulta.tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dadosConsulta.valorConsulta=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				case "AP":
					$linktj = "<a href='http://app.tjap.jus.br/tucujuris/publico/processo/index.xhtml?consNumeroUnicoJustica=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."&consNomeParte=&speed=true' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				case "BA":
					$linktj = "<a href='http://esaj.tjba.jus.br/cpopg/open.do' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				case "CE":
					$linktj = "<a href='http://esaj.tjce.jus.br/cpopg/open.do?paginaConsulta=1&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&dadosConsulta.tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dadosConsulta.valorConsulta=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				case "RN":
					$linktj = "<a href='http://esaj.tjrn.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dePesquisa=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				case "PE":
					$linktj = "<a href='http://srv01.tjpe.jus.br/consultaprocessualunificada/xhtml/consulta.xhtml?processo=".str_replace("-","",str_replace(".","",htmlentities($wr['processo'])))."' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				case "PI":
					$linktj = "<a href='http://www.tjpi.jus.br/themisconsulta/' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				case "PB":
					$linktj = "<a href='https://pje.tjpb.jus.br/pje/ConsultaPublica/listView.seam' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				case "PA":
					$linktj = "<a href='http://wsconsultas.tjpa.jus.br/consultaprocessoportal/consulta/principal?detalhada=true".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank' style='$trcolA'>".htmlentities($wr['processo'])."</a>";
					break;
				default:
					$linktj = htmlentities($wr['processo']);
					break;
			}
			//verificar se tem SRS
			//if($wr['system_name']!='SRS'){
			//	$trcolA = "color:red";
			//	$cor = "V";
			//	$m++;
			//}else{
			//	$trcolA = "color:blue";
			//	$cor = "A";
			//}
			//PRIMEIRAS LETRAS
			$usu = substr($wusu['name_first'],0,1) . substr($wusu['name_last'],0,1);
			
			$a++;
						
			$htmlA .= "<tr style='height:30px; text-align:center; $trcolA' id='trs_$a' onclick='javascrip:$(\"#trs_$a\").attr(\"bgcolor\",\"yellow\");' >
					<td style='width:80px'><a href='/processos/case_det.php?case=" . $wr['id_case'] . "'  target='_blank' style='$trcolA'>" . htmlentities($wr['id_case']) 	. "</a></td>
					<td style='width:80px' class='cls_td'>" . htmlentities($srsw['value']) 		. "</td>
					<td style='width:80px'>" . htmlentities($wr['p_adverso']) 		. "</td>
					<td style='width:80px'>" . htmlentities($wr['p_cliente'])		. "</td>
					<td style='width:80px'>" . $linktj								. "</td>
					<td style='width:80px'>" . htmlentities($wr['vara']) 			. "</td>
					<td style='width:80px'>" . htmlentities($wr['legal_reason'])	. "</td>
					<td style='width:80px'>" . htmlentities($andamentos['kw_followups_'.$wdilig['type'].'_title']) . "</td>
					<td style='width:80px'>" . htmlentities($wdilig['data_atual'])	. "</td>
					<td style='width:80px'>" . htmlentities($wr['comarca']) 		. "</td>
					<td style='width:80px'>" . htmlentities($wr['state']) 			. "</td>
					<td style='width:80px'>" . htmlentities($wdilig['data_cad']) 	. "</td>
					<td style='width:80px' title='".htmlentities(strtoupper($wusu['name_first']." ".$wusu['name_middle']." ".$wusu['name_last']))."'>$usu</td>
					<td style='width:80px'>" . $simnao[$Wloca] 						. "</td>
					<td style='width:80px'>" . $simnao[$WContcli] 					. "</td>
					<td style='width:80px'>" . $temadv								. "</td>
					<td style='width:80px'>" . $cor . " - " . $wr['meusrs'] 		. "</td>
				  </tr>";
				echo "<script type='text/javascript'>
					$(function(){   
					$('#trs_$a').dblclick(function(){
						$(\"#trs_$a\").attr(\"bgcolor\",\"#ffffff\");
					});
				});</script>";
		
	}
	
	// Progress
	$esc++;
	$perc = ($esc / $nl) * 100;
	$fp = fopen($_POST['pgrs'], "w");
	$escreve = fwrite($fp, $perc);
	fclose($fp);	
}

$table .= "<table align='center' width='990px' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:9pt; font-weight:bold; font-family:arial'>
			<tr>
				<td align='center'>ACOMPANHAMENTO DOS ANDAMENTOS NO SIJUR - $a / RESTANDO ATUALIZAR: $m
					<!--span style='float:left;cursor:pointer' id='sttrb'>
						<span id='spn_rb'></span> 
					</span--> 
					<br>
					<span style='float:left'><a href='#' id='mapa'>Mapa dos mandados</a></span>
					<!--span style='float:right'>
						<a href='index_excel.php' >Base em Excel</a>
					</span-->
					<div id='id_sel' style='font-size:9pt;font-weight:normal'><i>Total selecionado: $a</i></div> 
				</td>
			</tr>
			<tr>
			<td>";
$table .= "<table align='center' id='tbf1' class='tbf1' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:7pt; font-family:arial; width:990px'>";
$table .= "<tr>
			<th style='color:blue;' class='semFiltro' >Pasta</td>
			<th style='color:blue;' class='semFiltro' >Contrato</td>
			<th style='color:blue;' class='semFiltro' >Adverso</td>
			<th style='color:blue;' class='comFiltro' >Banco</td>
			<th style='color:blue;' class='semFiltro' >Processo</td>
			<th style='color:blue;' class='comFiltro' >Vara</td>
			<th style='color:blue;' class='comFiltro' >Ação</td>
			<th style='color:blue;' class='comFiltro' >Evento</td>
			<th style='color:blue;' class='comFiltro' >Data</td>
			<th style='color:blue;' class='comFiltro' >Comarca</td>
			<th style='color:blue;' class='comFiltro' >Estado</td>
			<th style='color:blue;' class='comFiltro' >Atualizado</td>
			<th style='color:blue;' class='comFiltro' >Usu</td>
			<th style='color:blue;' class='comFiltro' >Loc?</td>
			<th style='color:blue;' class='comFiltro' >Ctt?</td>
			<th style='color:blue;' class='comFiltro' >Adv?</td>
			<th style='color:blue;' class='comFiltro' >Cor</td>
		  </tr>";
$table .= $htmlA;
$table .= "</table></table>";
$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:7pt; font-family:arial; width:990px'>";
$table .= "<tr><td colspan='12' style='color:blue'>Total de mandados: $a  </td></tr>";
$table .= "</table><br><br>";

echo "<script type='text/javascript'>
		$(function(){   
			$('#sttrb').click(function(){
				sttsrs();
			});
			
			function sttsrs(){
				
				$.ajax({
					type: 'POST',
					url:  'status_srs.php',
					data: 'flag=',
					success: function(retorno_ajax){
						//alert(retorno_ajax);
						$('#spn_rb').html(retorno_ajax);
					}
				});
			}
			sttsrs();
		}); 
	</script>";
	
echo $table;

sleep (1);
//deleta o arquivo criado
$dir = "./";
$dh = opendir($dir);
while (false !== ($filename = readdir($dh))) {
	if (substr($filename,-4) == ".txt") {
		unlink($filename);
	}
}

?>