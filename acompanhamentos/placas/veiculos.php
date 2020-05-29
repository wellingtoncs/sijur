<?php

$table = "";
$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor.");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados .");

$qr = mysql_query(" SELECT *, max(f.id_followup) as 'andamentos' FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
					WHERE c.`status` = 'open' AND k.id_keyword = 71 AND c.processo != '' AND f.`type`!='other' GROUP BY c.id_case ORDER BY max(f.date_cad) asc ",$conexao1);
	
$a   = 0;

while($wr = mysql_fetch_array($qr)){
	
	$qdilig = mysql_query(" SELECT *, date_format(f.date_start,'%d/%m/%Y') as data_atual, date_format(f.date_cad,'%d/%m/%Y') as data_cad,
		datediff(curdate(),f.date_cad) as 'diff_dias' FROM lcm_followup AS f where f.id_followup = " . $wr['andamentos'] . " AND f.`type`!='other'");
	$wdilig = mysql_fetch_array($qdilig);
	
	$qusu = mysql_query("SELECT * FROM lcm_author as a where a.id_author = '".$wdilig['id_author']."' ");
	$wusu = mysql_fetch_array($qusu);
		
	$tipodeacao = preg_replace("/[^a-zA-Z0-9_]/", "", strtr($wr['legal_reason'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
	
	if($tipodeacao=='BUSCAEAPREENS_O' || $tipodeacao=='REINTEGRA_ODEPOSSE' || $tipodeacao==''){
		
		$trcolA = "color:blue";
		$marca = strtoupper($wr['notes']);
		if($marca!=""){
		$a++;
			
			$htmlA .= "<tr style='height:30px; text-align:center; $trcolA' id='trs_$a' onclick='javascrip:$(\"#trs_$a\").attr(\"bgcolor\",\"yellow\");' >
						<td style='width:100%;text-align:left'>" . htmlentities($marca) 				. "</td>
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
$table .= "</table>";
$table .= "<table align='center' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:19pt; font-family:arial;' id='tbf1' class='tbf1'>";
$table .= "<tr>
			<th style='color:blue; width:100%;' class='comFiltro'>Veículo</td>
			<th style='color:blue; width:100%;' class='comFiltro'>Processo</td>
			<th style='color:blue; width:100%;' class='comFiltro'>Comarca</td>
			<th style='color:blue; width:100%;' class='comFiltro'>Estado</td>
		  </tr>";
$table .= $htmlA;
$table .= "<tr><td colspan='12' style='color:blue'>Total de mandados: $a  </td></tr>";
$table .= "</table><br><br>";

echo $table;

?>