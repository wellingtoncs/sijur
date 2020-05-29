<?php

$placa_n = trim($_POST['placa_n']);

if(strlen($placa_n)!=4){
	echo "erro";
	exit; 
}

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
</style></head><body>";

$placa = $_POST['placa_t'].$_POST['placa_n'];
$placa = str_replace(" ","",$placa);
$placa = trim($placa);



	$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor.");
	mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados .");

	$conexao2 = mysql_connect("localhost", "fabio", "torres@#",TRUE) or die("MySQL: Não foi possível conectar-se ao servidor.");
	mysql_select_db("contratos_db", $conexao2) or die("MySQL: Não foi possível conectar-se ao banco de dados.");

if($placa!=""){

	$qr = mysql_query(" SELECT *,max(f.id_followup) as 'andamentos' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
		WHERE c.notes like '%".$placa."%' AND c.`status` = 'open' AND k.id_keyword = 71 
		AND f.id_followup = (SELECT MAX(ff.id_followup) FROM lcm_followup AS ff WHERE ff.id_case = c.id_case )
		GROUP BY c.id_case
		ORDER BY f.date_start DESC ",$conexao1);
		
	$tl=0;
	$a =0;
	$contratos = "";
	while($wr = mysql_fetch_array($qr)){
		
		$qandam = mysql_query(" SELECT *,  
		date_format(f.date_start,'%d/%m/%Y') as data_atual, 
		date_format(f.date_cad,'%d/%m/%Y') as data_cad,
		datediff(curdate(),f.date_cad) as 'diff_dias'
		FROM lcm_followup AS f where f.id_followup = " . $wr['andamentos'] , $conexao1);
		$wandam = mysql_fetch_array($qandam);
		
		$qdilig = mysql_query(" SELECT c.id_case 
		FROM lcm_case AS c
		JOIN lcm_followup AS f ON f.id_case = c.id_case
		WHERE f.`type` in ('followups56','followups45','followups23') AND c.`status` = 'open' AND c.id_case = '".$wr['id_case']."' AND f.date_start > '".$wr['date_start']."'
		GROUP BY c.id_case
		ORDER BY f.date_start DESC ",$conexao1);
		
		if(mysql_num_rows($qdilig)==0){
			$tipodeacao = preg_replace("/[^a-zA-Z0-9_]/i", "\\1", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
			//pega o cpf do cliente do processo
			$QCasecpf = mysql_query("SELECT replace(replace(a.cpfcnpj,'.',''),'-','') as 'cpfcnpj' FROM lcm_case_adverso_cliente AS ac JOIN lcm_adverso AS a ON a.id_adverso=ac.id_adverso WHERE ac.id_case ='". $wr['id_case'] ."' ",$conexao1);
			$WCasecpf = mysql_fetch_array($QCasecpf);
			
			$QContcpf = mysql_query("SELECT cac.id_case FROM lcm_cont_adverso_cliente AS cac JOIN lcm_adverso AS ca ON ca.id_adverso=cac.id_adverso WHERE  replace(replace(ca.cpfcnpj,'.',''),'-','') = '". $WCasecpf['cpfcnpj'] ."' ",$conexao2);
			$WContcpf = mysql_fetch_array($QContcpf);
			
			//pega a placa no 'notes'
			$notes = str_replace(":","",str_replace(" ","",strtoupper($wr['notes'])));
			$pos = strpos($notes,'PLACA');
			$placa_search = str_replace("PLACA","",substr($notes,$pos,12));
			
			$Qloc = mysql_query("SELECT f.description,datediff(curdate(),f.date_start) as 'diff_dias' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
									WHERE f.`type` = 'followups62' AND c.`status` = 'open' AND k.id_keyword = 71
									and c.id_case = '" . $wr['id_case'] . "'
									ORDER BY f.date_start DESC ",$conexao1);
									
			$Wloc = mysql_fetch_array($Qloc);
			
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
					$trcolA = "color:#000";
					$m++;
				}
				
				
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
						$botton_ficha = "Sem Ficha";
					}
				}
				
				if(strpos(strtolower(" " .$placa_search),strtolower($placa))==true){
				
					$a++;
					$htmlA .= "<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:#000; font-size:36pt; font-family:arial;'>";
					$htmlA .= "<div><span style='float:left'>".$a."º </span><span style='float:right'><i>Pesquisado por: <u>".$placa."</u></i> -> Placa:<b>".$placa_search."</b></span></div>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Adverso: 		</td><td style='width:65%; text-align:left' class='cls_td'>" . ($WContcpf['id_case']!=''?"<a href='/contratos/case_det.php?case=" . $WContcpf['id_case'] . "' target='_blank' style='$trcolA'>" . htmlentities($wr['p_adverso']) . "</a>":htmlentities($wr['p_adverso'])). "</td>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Banco: 		</td><td style='width:65%; text-align:left'>" . htmlentities($wr['p_cliente'])	. "</td></tr>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Processo: 	</td><td style='width:65%; text-align:left'>" . $linktj . "</td></tr>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Vara: 		</td><td style='width:65%; text-align:left'>" . htmlentities($wr['vara']) 		. "</td></tr>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Ação: 		</td><td style='width:65%; text-align:left'>" . htmlentities($wr['legal_reason']). "</td></tr>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Evento: 		</td><td style='width:65%; text-align:left; class='cls_td'><a href='/processos/case_det.php?case=" . $wr['id_case'] . "' target='_blank'>".htmlentities($andamentos['kw_followups_'.$wandam['type'].'_title'])."</a></td></tr>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Comarca/UF: 		</td><td style='width:65%; text-align:left'>" . htmlentities($wr['comarca']) 	. "/" . htmlentities($wr['state']) 		. "</td></tr>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Localizador:  </td><td style='width:65%; text-align:left'>" . ($description==''?'SEM LOCALIZADOR':htmlentities($description)). "</td></tr>";
					//$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Ficha: 		</td><td style='width:65%; text-align:left'>$botton_ficha</td>";
					$htmlA .= "<tr style='text-align:center; width:35%; $trcolA'><td style='text-align:right'>Veículo: 		</td><td style='width:65%; font-size:26pt; text-align:left'>" . htmlentities($wr['notes'])	. "</td></tr>";
						
					echo "<!--a href='http://www.detran.pe.gov.br/index.php?option=com_search_placa&placa=".$Wbem['carrier']."' style='cursor:pointer; border:0px solid #ccc' target='_blank'>".$Wbem['carrier']." Detan</a-->";
					echo "<input type='hidden' name='banco_$a' value='" . htmlentities($wr['p_cliente']) . "'/>";
					echo "<input type='hidden' name='processo_$a' value='" . htmlentities($wr['processo']) . "'/>";
					echo "<input type='hidden' name='adverso_$a' value='" . htmlentities($wr['p_adverso']) . "'/>";
					echo "<input type='hidden' name='vara_$a' value='" . htmlentities($wr['vara']) . "'/>";
					echo "<input type='hidden' name='comarca_$a' value='" . htmlentities($wr['comarca']) . "'/>";
					echo "<input type='hidden' name='estado_$a' value='" . htmlentities($wr['state']) . "'/>";
					echo "<input type='hidden' name='cpfcnpj_$a' value='" . $WCasecpf['cpfcnpj'] . "'/>";
					echo "<input type='hidden' name='veiculo_$a' value='" . $wr['notes'] . "'/>";
					echo "<input type='hidden' name='idcont_$a' value='" . $WContcpf['id_case'] . "'/>";
					$htmlA .= "</table><br>";
				}
				$linktj="";
			}
		}
	}
	$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='99%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:#000; font-size:36pt; font-family:arial;'>";
	$table .= "<tr><td><a href='index.php' style='float:right; font-size:60pt;width:20%;height:25%' >Voltar</a></td></tr>";
	$table .= "<tr><td>";
	$table .= $htmlA;
	$table .= "</td></tr>";
	$table .= "<tr><td colspan='2' style='color:blue; font-size:36pt'>Total de casos: $a  </td></tr>";
	$table .= "</table>";
	if($a==0){
		echo "zero";
	}else{
		echo $table;
	}
	
}else{
	
	$table = "";
	
	$qr = mysql_query(" SELECT *, max(f.id_followup) as 'andamentos' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
						WHERE c.`status` = 'open' AND k.id_keyword = 71 AND c.processo != '' AND f.`type`!='other' GROUP BY c.id_case ORDER BY max(f.date_cad) asc ", $conexao1);
		
	$a   = 0;

	while($wr = mysql_fetch_array($qr)){
		
		$qdilig = mysql_query(" SELECT *, date_format(f.date_start,'%d/%m/%Y') as data_atual, date_format(f.date_cad,'%d/%m/%Y') as data_cad,
			datediff(curdate(),f.date_cad) as 'diff_dias' FROM lcm_followup AS f where f.id_followup = " . $wr['andamentos'] . " AND f.`type`!='other'");
		$wdilig = mysql_fetch_array($qdilig);
		
		$qusu = mysql_query("SELECT * FROM lcm_author as a where a.id_author = '".$wdilig['id_author']."' ");
		$wusu = mysql_fetch_array($qusu);
			
		$tipodeacao = ereg_replace("[^a-zA-Z0-9_]", "", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
		
		if($tipodeacao=='BUSCAEAPREENS_O' || $tipodeacao=='REINTEGRA_ODEPOSSE' || $tipodeacao==''){
			
			$trcolA = "color:blue";
			$marca = strtoupper($wr['notes']);
			if($marca!=""){
			$a++;
				
				$htmlA .= "<tr style='height:30px; text-align:center; $trcolA' id='trs_$a' onclick='javascrip:$(\"#trs_$a\").attr(\"bgcolor\",\"yellow\");' >
							<td style='width:100%'>" . htmlentities($marca) 				. "</td>
							<td style='width:100%'>" . htmlentities($wr['processo'])		. "</td>
							<td style='width:100%'>" . htmlentities($wr['comarca']) 		. "</td>
							<td style='width:100%'>" . htmlentities($wr['state']) 			. "</td>
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

	$table .= "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:19pt; font-weight:bold; font-family:arial'>
				<tr>
					<td align='center'>VEÍCULOS - GMAC - TOTAL: $a </td>
				</tr>
				<tr>
				<td>";
	$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:17pt; font-family:arial; width:100%'>";
	$table .= "<tr>
				<th style='color:blue; width:100%' >Veículo</td>
				<th style='color:blue; width:100%' >Processo</td>
				<th style='color:blue; width:100%' >Comarca</td>
				<th style='color:blue; width:100%' >Estado</td>
			  </tr>";
	$table .= $htmlA;
	$table .= "<tr><td colspan='12' style='color:blue'>Total de casos: $a  </td></tr>";
	$table .= "</table></table><br><br>";
	if($a==0){
		echo "zero";
	}else{
		echo $table;
	}
}

?>