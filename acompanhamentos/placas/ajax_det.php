<?php

$idcase = trim($_POST['idcase']);
$placa = trim($idcase);
include('../php/functions.php');

$htmlA = ""; 
	$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor.");
	mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados .");

if($idcase!=""){

	$qr = mysql_query(" SELECT *,max(f.id_followup) as 'andamentos' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
		WHERE c.id_case = '".$idcase."' 
		AND f.id_followup = (SELECT MAX(ff.id_followup) FROM lcm_followup AS ff WHERE ff.id_case = c.id_case )
		GROUP BY c.id_case
		ORDER BY f.date_start DESC ",$conexao1);
		
	$tl=0;
	$a =0;
	$contratos = "";
	while($wr = mysql_fetch_array($qr)){
		
		$qandam = mysql_query(" SELECT * 
								FROM lcm_followup AS f where f.id_followup = " . $wr['andamentos'] , $conexao1);
		$wandam = mysql_fetch_array($qandam);
		
		$tipodeacao = preg_replace("/[^a-zA-Z0-9_]/i", "\\1", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
		//pega o cpf do cliente do processo
		$QCasecpf = mysql_query("SELECT replace(replace(a.cpfcnpj,'.',''),'-','') as 'cpfcnpj' FROM lcm_case_adverso_cliente AS ac JOIN lcm_adverso AS a ON a.id_adverso=ac.id_adverso WHERE ac.id_case ='". $wr['id_case'] ."' ",$conexao1);
		$WCasecpf = mysql_fetch_array($QCasecpf);
		
		$QContcpf = mysql_query("SELECT cac.id_case FROM lcm_cont_adverso_cliente AS cac JOIN lcm_adverso AS ca ON ca.id_adverso=cac.id_adverso WHERE  replace(replace(ca.cpfcnpj,'.',''),'-','') = '". $WCasecpf['cpfcnpj'] ."' ",$conexao2);
		$WContcpf = mysql_fetch_array($QContcpf);
		
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
		
		$description = str_replace("LOCALIZADOR:","",STRTOUPPER($Wloc['description']));
		$htmlA .= "<table align='center' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:#000; font-size:36pt; font-family:arial;'>";
			$htmlA .= "<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Adverso: 	</td><td style='width:65%; text-align:left;'>" . htmlentities($wr['p_adverso']) 	 . "</td></tr>";
			$htmlA .= "<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Banco: 		</td><td style='width:65%; text-align:left;'>" . htmlentities($wr['p_cliente'])		 . "</td></tr>";
			$htmlA .= "<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Processo: 	</td><td style='width:65%; text-align:left;'>" . $linktj 							 . "</td></tr>";
			$htmlA .= "<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Vara: 		</td><td style='width:65%; text-align:left;'>" . htmlentities($wr['vara']) 			 . "</td></tr>";
			$htmlA .= "<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Ação: 		</td><td style='width:65%; text-align:left;'>" . htmlentities($wr['legal_reason'])	 . "</td></tr>";
			$htmlA .= "<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Evento: 		</td><td style='width:65%; text-align:left;'><a href='/processos/case_det.php?case=" . $wr['id_case'] . "' target='_blank'>".htmlentities($andamentos['kw_followups_'.$wandam['type'].'_title'])."</a></td></tr>";
			$htmlA .= "<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Comarca/UF: 	</td><td style='width:65%; text-align:left;'>" . htmlentities($wr['comarca']) 		 . "/" . htmlentities($wr['state']) . "</td></tr>";
			$htmlA .= "<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Localizador: </td><td style='width:65%; text-align:left;'>" . ($description==''?'SEM LOCALIZADOR':htmlentities($description)). "</td></tr>";
		$htmlA .= "</table>";
	}	
}else{
	$htmlA .= "<tr style='text-align:center; width:99%; color:#000'><td style='text-align:center' colspan='2'>Erro!!!</td></tr>";
}

echo $htmlA;

?>