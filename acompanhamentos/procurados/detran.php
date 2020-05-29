<?php 

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");


$limite = $_POST['limite']?$_POST['limite']:0;

if($_POST['estado']=='PE' || $_POST['estado']=='PB'){
	
	$qr = mysql_query(" SELECT 
		CASE  
		WHEN c.notes LIKE '%CHEVROLE%' THEN 'gm'  
		WHEN c.notes LIKE '%GM%' THEN 'gm'
		WHEN c.notes LIKE '%VOLKS%' THEN 'volks'
		WHEN c.notes LIKE '%TOYOTA%' THEN 'toyota'
		WHEN c.notes LIKE '%HONDA%' THEN 'honda'
		WHEN c.notes LIKE '%FIAT%' THEN 'fiat'
		WHEN c.notes LIKE '%VOLVO%' THEN 'volvo'
		ELSE ''
		END as 'marca',
		CASE  
		WHEN c.notes LIKE '%CELTA%' THEN 'celta'  
		WHEN c.notes LIKE '%CORSA%' THEN 'corsa'  
		WHEN c.notes LIKE '%ONIX%' THEN 'onix'   
		WHEN c.notes LIKE '%AGILE%' THEN 'agile' 
		WHEN c.notes LIKE '%CLASSIC%' THEN 'classic'
		WHEN c.notes LIKE '%MERIVA%' THEN 'meriva'
		WHEN c.notes LIKE '%COBALT%' THEN 'cobalt'
		WHEN c.notes LIKE '%SPIN%' THEN 'spin' 
		WHEN c.notes LIKE '%VECTRA%' THEN 'vectra'
		WHEN c.notes LIKE '%PRISMA%' THEN 'prisma'
		WHEN c.notes LIKE '%CRUZE%' THEN 'cruze'
		WHEN c.notes LIKE '%MONTANA%' THEN 'montana'
		WHEN c.notes LIKE '%S10%' THEN 's10'
		WHEN c.notes LIKE '%CAMARO%' THEN 'camaro'
		WHEN c.notes LIKE '%TRACKER%' THEN 'tracker'
		WHEN c.notes LIKE '%VOLT%' THEN 'volt'
		WHEN c.notes LIKE '%MALIBU%' THEN 'malibu'
		WHEN c.notes LIKE '%OMEGA%' THEN 'omega'
		ELSE ''  
		END as 'modelo',
		CASE  
		WHEN c.notes LIKE '%PRETO%' THEN 'preto'  
		WHEN c.notes LIKE '%PRETA%' THEN 'preto'
		WHEN c.notes LIKE '%PRATA%' THEN 'prata'
		WHEN c.notes LIKE '%BRANC%' THEN 'branco'
		WHEN c.notes LIKE '%CINZA%' THEN 'cinza'
		WHEN c.notes LIKE '%AZUL%' THEN 'azul'
		WHEN c.notes LIKE '%VERDE%' THEN 'verde'
		WHEN c.notes LIKE '%VERMELH%' THEN 'vermelho'
		ELSE ''  
		END as 'cor',
		CAST(SUBSTR(replace(replace(replace(replace(replace(replace(replace(replace(c.notes,' ',''),':',''),'/',''),'-',''),'\r',''),'\t',''),'\n',''),'•',''),LOCATE('ANO',replace(replace(replace(replace(replace(replace(replace(replace(c.notes,' ',''),':',''),'/',''),'-',''),'\r',''),'\t',''),'\n',''),'•',''))+3,4) AS UNSIGNED) as 'ano',
		SUBSTR(replace(replace(replace(replace(replace(replace(replace(replace(c.notes,' ',''),':',''),'/',''),'-',''),'\r',''),'\t',''),'\n',''),'•',''),LOCATE('PLACA',replace(replace(replace(replace(replace(replace(replace(replace(c.notes,' ',''),':',''),'/',''),'-',''),'\r',''),'\t',''),'\n',''),'•',''))+5,7) as 'placa',
		c.comarca as 'cidade', 
		c.state as 'estado', 
		c.notes 
		FROM lcm_case AS c JOIN lcm_followup AS f on f.id_case=c.id_case
		WHERE c.notes <> '' 
		and c.`status` = 'open'
		AND c.id_case not in (SELECT f.id_case FROM lcm_followup AS f WHERE f.`type` = 'followups45' )
		and year(c.date_creation) = '2015'
		and (c.legal_reason like '%BUSCA%' OR c.legal_reason like '%REINTEGRA%' ) 
		and c.state = '".$_POST['estado']."' 
		AND replace((SELECT replace(SUBSTR(cp.notes,LOCATE('Placa',cp.notes)+6,((LOCATE('Renavam',cp.notes))-(LOCATE('Placa',cp.notes)+7))),'-','') FROM lcm_case AS cp WHERE LOCATE('Placa',cp.notes) <> '' AND cp.id_case=c.id_case),' ','') <> '' 
		and CHARACTER_LENGTH(SUBSTR(replace(replace(replace(replace(replace(replace(replace(replace(c.notes,' ',''),':',''),'/',''),'-',''),'\r',''),'\t',''),'\n',''),'•',''),LOCATE('PLACA',replace(replace(replace(replace(replace(replace(replace(replace(c.notes,' ',''),':',''),'/',''),'-',''),'\r',''),'\t',''),'\n',''),'•',''))+5,7))=7 
		GROUP BY c.id_case limit ".$limite.",1 ",$conexao1);
	$wr = mysql_fetch_array($qr);
	$placa = $wr['placas'];
	
}else{
	echo "<tr><td style='text-align:center'>Nenhum estado selecionado!</td></tr>";
	exit;
}
echo "<tr><td style='font-size:60px; text-align:center'>PROCURA-SE!!!</td></tr>";
echo "<tr><td style='text-align:center'><img src='img/gm_" . $wr['modelo'] . "_" . $wr['cor'] . ".jpg' height='680px'/></td></tr>";
echo "<tr><td style='font-size:30px;text-align:center'>". $wr['notes']."</td></tr>";
echo "<tr><td style='font-size:100px; font-family: mandatory; text-align:center;' >". substr($wr['placa'], 0,3)."-".substr($wr['placa'], -4)."</td></tr>";

sleep(2);

?>
