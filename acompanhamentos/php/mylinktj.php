<?php 

switch(htmlentities($wr['state'])){
	case "RN":
		$linktj = "<form name='consultarProcessoForm' method='GET' action='http://esaj.tjrn.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dePesquisa=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' id='formConsulta' target='_blank'>
						<input type='hidden'  name='paginaConsulta' value='1'>
						<input type='hidden'  name='conversationId' value='' />
						<input type='hidden'  name='dePesquisa' value='".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' formatType='TEXT' formato='25' />
						<input type='hidden'  name='cbPesquisa' value='NUMPROC' />
						<input type='hidden'  name='localPesquisa.cdLocal' value='-1' />
						<input type='hidden'  name='tipoNuProcesso' value='SAJ' />
						<button type='submit' onclick='send_ajax(".$wr['id_case'].")' name='pbEnviar' value='Pesquisar' style='border:0; cursor:pointer; background: transparent; font-size:7pt; color:blue' ><u>".$wr['processo']."</u></button>
					</form>";
					
		//$linktj = "<a href='http://esaj.tjrn.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dePesquisa=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
		$fntjal = htmlentities($wr['vara']);
		break;
	case "PE":
		$linktj = " <a href='http://srv01.tjpe.jus.br/consultaprocessualunificada/xhtml/consulta.xhtml?processo=".str_replace("-","",str_replace(".","",htmlentities($wr['processo'])))."' class='my_clip_button tjpe_1' data-clipboard-target='d_clip_button_$a' target='_blank' onclick='send_ajax(".$wr['id_case'].")' >".htmlentities($wr['processo'])."</a>
					<a href='https://pje.tjpe.jus.br/1g/ConsultaPublica/listView.seam' onclick='copyTextToClipboard(\"".$wr['processo']."\"); send_ajax(".$wr['id_case']."); solta_ctrl();' class='my_clip_button tjpe_2' data-clipboard-target='d_clip_button_$a' target='_blank' style='display:none'>".htmlentities($wr['processo'])."</a>";
		$fntjal = htmlentities($wr['vara']);
		break;
	case "PI":
			$linktj = "<form class='form-horizontal' action='http://www.tjpi.jus.br/themisconsulta/consulta/numero' method='post' target='_blank'>
				<input type='hidden' name='consulta.numeroUnico' value='".$wr['processo']."'>
				<input type='checkbox' name='consulta.isNumeroLegado' value='true' style='display:none'>
				<input type='hidden' name='consulta.comarca' value=''>
				<input type='hidden' name='consulta.numeroLegado' >
				<button type='submit' onclick='send_ajax(".$wr['id_case'].")' style='border:0; cursor:pointer; font-size:7pt; background: transparent; color:blue' ><u>".$wr['processo']."</u></button>
			</form>";
		//$linktj = "<a href='http://www.tjpi.jus.br/themisconsulta/' target='_blank'>".htmlentities($wr['processo'])."</a>";
		$fntjal = htmlentities($wr['vara']);
		break;
	case "PB":
		if(htmlentities(tiraAcento($wr['comarca']))!="JOAO_PESSOA"){
			$linktj = "<a href='https://app.tjpb.jus.br/consultaprocessual2/views/consultarPorParte.jsf' target='_blank' onclick='copyTextToClipboard(\"".$wr['processo']."\"); send_ajax(".$wr['id_case'].")'>".htmlentities($wr['processo'])."</a>";
		}else{
			$linktj = "<a href='https://pje.tjpb.jus.br/pje/ConsultaPublica/listView.seam' target='_blank' onclick='copyTextToClipboard(\"".$wr['processo']."\"); send_ajax(".$wr['id_case'].")'>".htmlentities($wr['processo'])."</a>";
		}
		$fntjal = htmlentities($wr['vara']);
		$fntjal = "";
		break;
	case "AP":
		$linktj = "<a href='http://app.tjap.jus.br/tucujuris/publico/processo/index.xhtml?consNumeroUnicoJustica=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."&consNomeParte=&speed=true' target='_blank' onclick='send_ajax(".$wr['id_case'].")'>".htmlentities($wr['processo'])."</a>";
		$fntjal = htmlentities($wr['vara']);
		break;
	case "CE":
		if(htmlentities(tiraAcento($wr['comarca']))!="FORTALEZA" && htmlentities(tiraAcento($wr['comarca']))!="MARACANAU"){
			$linktj = "<a href='http://www4.tjce.jus.br/sproc2/paginas/sprocprincipal.asp' target='_blank' onclick='copyTextToClipboard(\"".$wr['processo']."\"); send_ajax(".$wr['id_case'].");'>".htmlentities($wr['processo'])."</a>";
		}else{
			$linktj = "	<form name='consultarProcessoForm' method='GET' action='http://esaj.tjce.jus.br/cpopg/search.do' id='formConsulta' target='_blank'>
							<input type='hidden'  name='dadosConsulta.valorConsulta' value='".$wr['processo']."' formatType='TEXT' formato='25' />
							<input type='hidden'  name='cbPesquisa' value='NUMPROC' />
							<input type='hidden'  name='dadosConsulta.localPesquisa.cdLocal' value='-1' />
							<input type='hidden'  name='dadosConsulta.tipoNuProcesso' value='SAJ' />
							<button type='submit' onclick='send_ajax(".$wr['id_case'].")' name='pbEnviar' value='Pesquisar' style='border:0; cursor:pointer; font-size:7pt; background: transparent; color:blue' ><u>".$wr['processo']."</u></button>
						</form>";
		}
		//$linktj = "<a href='http://esaj.tjce.jus.br/cpopg/open.do?paginaConsulta=1&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=NUMPROC&dadosConsulta.tipoNuProcesso=SAJ&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dePesquisaNuUnificado=&dadosConsulta.valorConsulta=".trim(htmlentities(str_replace("-","",str_replace(".","",$wr['processo']))))."' target='_blank'>".htmlentities($wr['processo'])."</a>";
		$fntjal = htmlentities($wr['vara']);
		break;
	case "AL":
		$linktj = "<form name='consultarProcessoForm' method='GET' action='http://www2.tjal.jus.br/cpopg/search.do' id='formConsulta' target='_blank'>
						<input type='hidden'  name='dadosConsulta.valorConsulta' value='".$wr['processo']."' formatType='TEXT' formato='25' />
						<input type='hidden'  name='cbPesquisa' value='NUMPROC' />
						<input type='hidden'  name='dadosConsulta.localPesquisa.cdLocal' value='-1' />
						<input type='hidden'  name='dadosConsulta.tipoNuProcesso' value='SAJ' />
						<button type='submit' onclick='send_ajax(".$wr['id_case'].")' name='pbEnviar' value='Pesquisar' style='border:0; cursor:pointer; font-size:7pt; background: transparent; color:blue' ><u>".$wr['processo']."</u></button>
					</form>";
		$fntjal = "<a href='http://www.tjal.jus.br/?pag=consultas/enderecosComarca&cidade=" . $cod_fnal[strtolower($comarca)] . "' target='_blank' >" . htmlentities($wr['vara']) . "-". $comarca ."</a>";
		break;
}

$linksijur = "<a class='sijr_1' href='http://eduardoalbuquerque.no-ip.biz:8080/processos/case_det.php?case=" . $wr['id_case'] . "'  target='_blank'>" . htmlentities($wr['id_case'])	. "</a>
			  <a class='sijr_2' href='http://eduardoalbuquerque.no-ip.biz:8080/processos/edit_fu.php?case=" . $wr['id_case'] . "' target='_blank' onclick='solta_ctrl();' style='display:none' >" . htmlentities($wr['id_case']). "</a>";

?>