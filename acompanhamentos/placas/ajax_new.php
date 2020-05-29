<?php 

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor.");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados .");


$qr = mysql_query(" SELECT c.id_case, c.notes, c.`status` FROM lcm_case AS c JOIN lcm_followup AS f ON f.id_case = c.id_case JOIN lcm_keyword_case AS k ON k.id_case=c.id_case
		WHERE c.notes like '%".$placa."%' AND c.`status` in ('open','suspended') AND k.id_keyword = 71 
		AND f.id_followup = (SELECT MAX(ff.id_followup) FROM lcm_followup AS ff WHERE ff.id_case = c.id_case ) GROUP BY c.id_case
		ORDER BY f.date_start DESC ",$conexao1);
	$dados = 'var dados = [];';
	
	$fase = array("closed"=>"Inativo","deleted"=>"Excluído","draft"=>"Rascunho","merged"=>"Outros","open"=>"Ativo","suspended"=>"Suspenso");
	$a=0;
	while($wr = mysql_fetch_array($qr)){		
		//pega a placa no 'notes'
		$notes = str_replace(":","",str_replace(" ","",strtoupper($wr['notes'])));
		$pos = strpos($notes,'PLACA');
		if(strpos($notes,'PLACA')==true){
			$placa_search = str_replace("PLACA","",substr($notes,$pos,12));
			if($placa_search!=""){
				$dados .= 'dados["'.$a.'"] = new Array("'.$placa_search.'","'.$wr['id_case'].'","'.trim($wr['notes']).'","'.$fase[$wr['status']].'");';
				//$dados .= $wr['id_case'] . "_|_" . $placa_search . "_|_" . $wr['notes'] . "_|_" . $fase[$wr['status']] ."|-|";
				$a++;
			}
		}
	}
	$dados = preg_replace('/\n/','', $dados);
	$dados = preg_replace('/\t/','', $dados);
	$dados = preg_replace('/\r/','', $dados);
	$pgrs = "dados.js";
	$fp = fopen($pgrs, "w");
	$escreve = fwrite($fp, $dados);
	fclose($fp);
	if($escreve){
		echo "Dados atualizados!";
			$data = "data.txt";
			$fd = fopen($data, "w");
			$indata = fwrite($fd, date("d/m/Y"));
			fclose($fd);
	}else{
		echo "Não foi possível carregar dados!";
	}
	//echo $dados;
?>