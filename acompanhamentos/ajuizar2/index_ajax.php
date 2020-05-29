<?php

include('../php/functions.php');

//distribuidos_com_agendamento
$arquivo2 = 'combete.csv';
$fd = fopen($arquivo2, "r");
$arr_agend[] = array();
while(($dados2 = fgetcsv($fd, 0, ";")) !== FALSE){
	$quant_campos2 = count($dados2);
	for($n = 0; $n < $quant_campos2; $n++){
		$arr_agend[] = trim($dados2[$n]);
	}
}
fclose($fd);

$htmlA = "";
$htmlB = "";
$table = "";
$table .= "<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<title>Acompanhamento dos Processos</title>
<script type='text/javascript' src='../js/jquery-1.8.3.min.js'></script>
<script type='text/javascript' src='../js/jFilterXCel2003.js'></script>
<script type='text/javascript' src='../js/functions.js'></script>
<link rel='stylesheet' href='../css/style.css'>";

$table .= "</head><body>";

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");

$qr = mysql_query(" SELECT *,(5 - (datediff(curdate(),if(f.date_cad<>null, f.date_cad, f.date_start )))) as 'difdias', if(f.date_cad<>null, f.date_cad, f.date_start ) as 'datacad' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case	JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
					WHERE f.`type` = 'followups11' AND c.`status` != 'closed' AND k.id_keyword = 71
					GROUP BY c.id_case
					ORDER BY f.date_start DESC " );

//consultas tjs
if(!file_exists("consulta_".date('ymd').".txt")){
	$fp   = fopen("consulta_".date('ymd').".txt", "w");
	fclose($fp);	
}
$file = file_get_contents("consulta_".date('ymd').".txt");
$arr_c=array();
$c1 = explode("_|_",$file);
foreach($c1 as $c){
	if($c!=""){
		$arr_c[] = $c;
	}
}

$tl=0;
$a =0;
$contratos = "";
while($wr = mysql_fetch_array($qr)){
	
	$qr2 = mysql_query("	SELECT *, (select max(ff.id_followup) from lcm_followup as ff where ff.id_case='" . $wr['id_case'] . "' and ff.`type`='followups77') as 'andamentos'
							FROM lcm_case AS c 
							JOIN lcm_followup AS f ON f.id_case = c.id_case 
							JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
							WHERE c.id_case = " . $wr['id_case'] . "
							and f.`type` in ('followups77','followups81','followups11','followups68','followups91','followups92') 
							GROUP BY c.id_case ORDER BY f.date_start DESC ");
	$wr2 = mysql_fetch_array($qr2);
	
	$qandam = mysql_query(" SELECT *, 
							date_format(f.date_start,'%d/%m/%Y') as data_atual, 
							date_format(f.date_cad,'%d/%m/%Y') as data_cad,
							datediff(curdate(),f.date_cad) as 'diff_dias'
							FROM lcm_followup AS f where f.id_followup = '" . $wr2['andamentos'] . "' ");
	$wdilig = mysql_fetch_array($qandam);
	
	$qdilig = mysql_query(" SELECT c.id_case 
							FROM lcm_case AS c
							JOIN lcm_followup AS f ON f.id_case = c.id_case
							WHERE f.`type` in ('followups23','followups24','followups44','followups17','followups10','followups67') AND c.`status` in ('open','suspended') AND c.id_case = '".$wr['id_case']."' AND f.date_start > '".$wr['date_start']."'
							GROUP BY c.id_case ORDER BY f.date_start DESC ");
	
	//pega o cpf do cliente do processo
	$QCasecpf = mysql_query("SELECT replace(replace(a.cpfcnpj,'.',''),'-','') as 'cpfcnpj', a.id_adverso FROM lcm_case_adverso_cliente AS ac JOIN lcm_adverso AS a ON a.id_adverso=ac.id_adverso WHERE ac.id_case ='". $wr['id_case'] ."' ",$conexao1);
	$WCasecpf = mysql_fetch_array($QCasecpf);
	
	
	$QCliente = mysql_query("SELECT cl.name
							FROM lcm_case_adverso_cliente AS cac
							JOIN lcm_cliente AS cl ON cl.id_cliente=cac.id_cliente
							WHERE cac.id_case = '".$wr['id_case']."' AND cac.id_cliente != 0 ",$conexao1);

	$WCliente = mysql_fetch_array($QCliente);
	
	if(mysql_num_rows($qdilig)==0){
		
		//verificando documentos
		$qdocum = mysql_query(" SELECT 
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%NOTIFICACAO POSITIVA%' and ca.id_case=c.id_case limit 1) AS 'not_pos',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%NOTIFICACAO NEGATIVA%' and ca.id_case=c.id_case limit 1) AS 'not_neg',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%EXTRATO%' and ca.id_case=c.id_case limit 1) AS 'ext_deb',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%KIT%' and ca.id_case=c.id_case limit 1) AS 'kit_com',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%CEDULA%' and ca.id_case=c.id_case limit 1) AS 'ced_cre',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%VEICULO%' and ca.id_case=c.id_case limit 1) AS 'doc_vei',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%GUIA%' and ca.id_case=c.id_case limit 1) AS 'gui_cus',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%PAGAS%' and ca.id_case=c.id_case limit 1) AS 'cus_pag',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%PETICAO INICIAL%' and ca.id_case=c.id_case limit 1) AS 'pet_ini',
								(SELECT concat(ca.id_attachment, '_|_',ca.description) from  lcm_case_attachment as ca where ca.description like '%SUBSTABELECIMENTO%' and ca.id_case=c.id_case limit 1) AS 'pro_sub'
								FROM lcm_case AS c WHERE c.id_case = '" . $wr['id_case'] . "' and c.p_cliente in ('BANCO J. SAFRA S/A','BANCO SAFRA S/A','BANCO GMAC S/A') " );
								
		$wdocum = mysql_fetch_array($qdocum);
		
		
		$ulteveq = mysql_query("SELECT max(f.`type`) as 'ultevento',  date_format(f.date_start,'%d/%m/%Y') as 'datestart', f.sumbilled FROM lcm_followup as f where f.id_case = ".$wr['id_case']." and f.`type`in ('followups11','followups77','followups81','followups68','followups91','followups92')");
		$ultevew = mysql_fetch_array($ulteveq);


		$dessus = mysql_query("SELECT max(f.`type`) as 'ultevento',  date_format(f.date_start,'%d/%m/%Y') as 'datestart', f.sumbilled FROM lcm_followup as f where f.id_case = ".$wr['id_case']." and f.`type`in ('followups23','followups44','suspension')");
		$dessus = mysql_fetch_array($dessus);
		
		$tipodeacao = ereg_replace("[^a-zA-Z0-9_]", "", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
	
		if($tipodeacao=='BUSCAEAPREENS_O' || $tipodeacao=='REINTEGRA_ODEPOSSE' || $tipodeacao==''){
			
			//Criar link dos Tjs
			include('../php/mylinktj.php');

			//if(diasemana($wr['datacad'])==6) {
			//	$somadia = 2;
			//}elseif(diasemana($wr['datacad'])==7){
			//	$somadia = 2;
			//}else{
			//	$somadia = 2;
			//}
			
			$Qctt = mysql_query("   SELECT ct.value, ct.extra FROM lcm_contact AS ct WHERE ct.id_of_person='".$WCasecpf['id_adverso']."'
									and ct.type_contact=7 LIMIT 1 ",$conexao1);
			$Wctt = mysql_fetch_array($Qctt);
			
			$somadia = 2;
			$difdias = $wr['difdias'] + $somadia;
			$difdias>0?($corsrs = "background:green;color:#fff;border:1px solid #006400").($cor='color:blue'):($difdias==0?($corsrs="background:yellow;color:green;border:1px solid #DAA520").($cor='color:blue'):($corsrs="background:red;color:#fff;border:1px solid #B22222").($cor='color:red'));
			
			$a++;
			
			$pro_sub = explode("_|_",$wdocum['pro_sub']);
			$pet_ini = explode("_|_",$wdocum['pet_ini']);
			$kit_com = explode("_|_",$wdocum['kit_com']);
			$gui_cus = explode("_|_",$wdocum['gui_cus']);
			$cus_pag = explode("_|_",$wdocum['cus_pag']);
			$doc_vei = explode("_|_",$wdocum['doc_vei']);
			$ced_cre = explode("_|_",$wdocum['ced_cre']);
			$ext_deb = explode("_|_",$wdocum['ext_deb']);
			$not_neg = explode("_|_",$wdocum['not_neg']);
			$not_pos = explode("_|_",$wdocum['not_pos']);

			if(in_array($wr['p_adverso'],$arr_agend)){
				$procustas = "COM BETE!";
				$ccolor = "color:#996600";	
			}else{
				$procustas = "NORMAL!";
				$ccolor = $cor;					
			}
			/////////////////////////////////////////////
			if(in_array($wr['id_case'],$arr_c)){
				$trcolA="bgcolor='#FFFFFF'";				
			}else{
				$trcolA="bgcolor='#D6DCE5'";
			}
			
			$htmlA .= "<tr $trcolA style='height:30px; text-align:center;' id='trs_$a' onclick='javascrip:$(\"#trs_$a\").attr(\"bgcolor\",\"yellow\");' >
							<td class='cls_onblur cls_td' style='width:80px' >" . $linksijur . "</a></td>
							<td class='cls_onblur' style='width:80px; ".($WCliente['name']?$cor:"color:red")."'>" . ($WCliente['name']?htmlentities($WCliente['name']):"- CADASTRAR -") . "</td>
							<td class='cls_onblur' style='width:80px; $ccolor'>" . $procustas 	. "</td>
							<td class='cls_onblur' style='width:80px;$cor'>" . htmlentities($wr['p_adverso']) 	. "</td>
							<td class='cls_onblur' style='width:80px;$cor'>" . htmlentities($andamentos['kw_followups_'.$ultevew['ultevento'].'_title']) . "</td>
							<td class='cls_onblur' style='width:80px;$cor'>" . ($dessus['ultevento']?htmlentities($andamentos['kw_followups_'.$dessus['ultevento'].'_title']):"NORMAL") . "</td>
							<td class='cls_onblur' style='width:80px;$cor'>" . htmlentities($ultevew['datestart'])	. "</td>
							<td class='cls_onblur' style='width:80px;$cor'>" . htmlentities($wr['comarca']) 	. "</td>
							<td class='cls_onblur' style='width:80px;$cor'>" . htmlentities($wr['state']) 		. "</td>
							<td class='cls_onblur' style='width:80px;$cor'>" . htmlentities(trim(number_format($wdilig['sumbilled'],2,',','.'))) . "</td>
							<td class='cls_onblur' style='width:10px;$cor;font-size:16px'>" . 
						
						($pro_sub[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$pro_sub[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$pro_sub[1].'">&#9416;</a> ' : '') . 
						($pet_ini[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$pet_ini[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$pet_ini[1].'">&#9406;</a> ' : '') . 
						($kit_com[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$kit_com[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$kit_com[1].'">&#9408;</a> ' : '') . 
						($gui_cus[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$gui_cus[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$gui_cus[1].'">&#9404;</a> ' : '') . 
						($cus_pag[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$cus_pag[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$cus_pag[1].'">&#9413;</a> ' : '') . 
						($doc_vei[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$doc_vei[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$doc_vei[1].'">&#9419;</a> ' : '') . 
						($ced_cre[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$ced_cre[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$ced_cre[1].'">&#9400;</a> ' : '') . 
						($ext_deb[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$ext_deb[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$ext_deb[1].'">&#9402;</a> ' : '') . 
						($not_neg[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$not_neg[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$not_neg[1].'">&#9437;</a> ' : '') . 
						($not_pos[1]!="" ? '<a href="http://10.10.0.100:8080/processos/view_file.php?type=case&file_id='.$not_pos[0].'" style="text-decoration: none;'.$cor.';" target="_blank" title="'.$not_pos[1].'">&#9411;</a> ' : '') . 						
						
					"</td>
					 <td style='width:20px;$corsrs'>" . $difdias . "</td>
					 <td style='width:80px;$cor;text-align:left'>" . ($Wctt['value']!=''?htmlentities($Wctt['value']):'<a href="http://10.10.0.100:8080/processos/edit_adverso.php?adverso='.$WCasecpf['id_adverso'].'" target="_blank">- Sem dados - '). "</a></td> 
					 <td style='width:80px;$cor;text-align:left'>" . ($Wctt['extra']!=''?htmlentities($Wctt['extra']):'- Sem dados - ') . "</td>
				  </tr>";

				$pro_sub="";
				$pet_ini="";
				$kit_com="";
				$gui_cus="";
				$cus_pag="";
				$doc_vei="";
				$ced_cre="";
				$ext_deb="";
				$not_neg="";
				$not_pos="";
			echo "<script type='text/javascript'>
					$(function(){   
				$('#trs_$a').dblclick(function(){
					$(\"#trs_$a\").attr(\"bgcolor\",\"#ffffff\");
				});
			});</script>";
			$tot_custas += $wdilig['sumbilled'];
		}
	}
}

$table .= "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#8497B0' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:9pt; font-weight:bold; font-family:arial'>
			<tr>
				<td align='center'>ACOMPANHAMENTO DO QUE ESTÁ AGUARDANDO A DISTRIBUIÇÃO NO SIJUR - $a 
					<div id='id_sel' style='font-size:9pt;font-weight:normal'><i>Total selecionado: $a</i></div> 
				</td>
			</tr>";
$table .= "</table>";
$table .= "<tr><td>";
$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#8497B0' style='border-collapse:collapse; font-size:7pt; font-family:arial;'>";
$table .= "<tr>
			<th style='color:blue; width:20px' class='comFiltro' >Pasta</td>
			<th style='color:blue; width:80px' class='comFiltro' >Banco</td>
			<th style='color:blue; width:80px' class='comFiltro' >CUSTAS</td>
			<th style='color:blue; width:80px' class='comFiltro' >Adverso</td>
			<th style='color:blue; width:80px' class='comFiltro' >Evento</td>
			<th style='color:blue; width:80px' class='comFiltro' >Status</td>
			<th style='color:blue; width:80px' class='comFiltro' >Data</td>
			<th style='color:blue; width:80px' class='comFiltro' >Comarca</td>
			<th style='color:blue; width:80px' class='comFiltro' >Estado</td>
			<th style='color:blue; width:80px' class='comFiltro' >Valor</td>
			<th style='color:blue; width:20px' class='comFiltro' >DOCS</td>
			<th style='color:blue; width:20px' class='comFiltro' >SRS</td>
			<th style='font-color:#cccccc;' class='comFiltro' >Endereço</td>
			<th style='font-color:#cccccc;' class='comFiltro' >Cordenadas</td>
		  </tr>";
$table .= $htmlA;
$table .= "</table>";
$table .= "<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#8497B0' style='border-collapse:collapse; font-size:7pt; font-family:arial;'>";
$table .= "<tr>
				<td colspan='11' style='color:blue'>Total de ajuizamento: $a  </td>
				<td colspan='1' style='width:120px'>&nbsp;</td>
			</tr>";
$table .= "</table>";
$table .= "<br><br>";

echo $table;

echo "Total de custas à Pagar: ". number_format($tot_custas,2,',','.');

include("../php/exportar.php");

?>