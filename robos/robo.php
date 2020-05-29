<?php
	
	/*
		Status
		
		0 = Nao realizado
		1 = Em andamento
		2 = S - Realizado com sucesso
		3 = S - "de-para" nao transferir
		4 = N - O robo nao conseguiu realizar o cadastro
		5 = N - Faltando fazer associacao do "de-para"
		6 = N - Falha na conexao
		7 = Feito manualmente
	*/
	
	$id_followup = 0;
	$arr_dado    = array();
	$dt_hr       = date('Y-m-d H:i:s');
	
	$con_sisjur = mysql_connect('localhost', 'robosis', 'recife123');
	mysql_select_db('processos_db', $con_sisjur);
	
	$con_robo = mysql_connect('10.10.0.140', 'root', 'legemsrs');
	mysql_select_db('robo', $con_robo);
		
	include_once "get_eve_atu_st.php";	
		
	//Atualiza status positivo ou negativo
	if (isset($_POST['resultado']) && !empty($_POST['resultado']) && !empty($_POST['txt_id_followup']))
	{
		$id_upd = $_POST['txt_id_followup'];
		$status = $_POST['resultado'] === 'P' ? 2 : 4;
		
		fc_at_status($id_upd, $dt_hr, $status);		
	}	

	//Atualiza status para 3 (falha na conexao)			
	$rs = mysql_query(fc_get_sql_princ(1, $robo), $con_sisjur);	
	
	if ($ln = mysql_fetch_assoc($rs))
	{
		fc_at_status($ln['id_followup'], $dt_hr, 6);
	}
	
	$rs = mysql_query(fc_get_sql_princ(0, $robo), $con_sisjur);

	if ($ln = mysql_fetch_assoc($rs))
	{	
		switch($robo)
		{
			case 'LEG':
				$evento = fc_get_evento_legem($ln['type'], $ln['id_followup'], $dt_hr);
				break;
				
			case 'SRS':
				$evento = fc_get_evento_srs($ln['type'], $ln['id_followup'], $dt_hr);
				break;
		}
		
		if (!empty($evento))
		{	
			$id_followup 				= $ln['id_followup'];
		
			switch($robo)
			{
				case 'LEG':
					$arr_dado['cod_int']        = trim($ln['value']); 
					$arr_dado['vara']           = strpos(strtoupper($ln['vara']), 'PROCON') !== false ? 'PROCON' : utf8_encode($ln['vara']);
					$arr_dado['data']           = date('d/m/Y');
					$arr_dado['atividade']      = trim($evento);
					$arr_dado['comentario']     = trim($ln['description']) == '' ? ' ' : utf8_encode(trim($ln['description']));		
					break;
					
				case 'SRS':
					if (strpos($evento, '|'))
					{
						$arr_eve    = explode('|', $evento);
						$evento	    = $arr_eve[0];
						$str_remark = $arr_eve[1];
					}
					else
					{
						$str_remark = '';	
					}
					
					$arr_dado['loan_no']        = trim($ln['value']);; 
					$arr_dado['evento']         = trim($evento);
					$arr_dado['remarks']        = trim($ln['description']) == '' ? ' ' : utf8_encode(trim($ln['description']));
					
					if (trim($arr_dado['remarks']) != '')
					{
						$arr_dado['remarks'] = (trim($str_remark) == '' ? ' ' : utf8_encode(trim($str_remark))) . ' - ' .
						                       $arr_dado['remarks'];
					}
					else
					{
						$arr_dado['remarks'] = trim($str_remark) == '' ? ' ' : utf8_encode(trim($str_remark));
					}
					break;
			}
			
			fc_at_status($id_followup, $dt_hr, 1);
		}
	}	
    
	function fc_get_sql_princ($tipo, $robo)
	{
		$id_kw = $robo == 'LEG' ? 52 : 147;
		
		$sql = "select distinct c.id_case, f.id_followup, f.`description`, f.`type`, kc.value, c.vara  " .
			   "from lcm_followup f inner join lcm_case c on f.id_case = c.id_case  " .
			   "join lcm_keyword_case as kc on kc.id_case=c.id_case " . 
		       "where robo_ins = $tipo and system_name = '$robo' and kc.id_keyword = $id_kw and " .
			   "c.`status` = 'open' and not kc.value is null order by f.date_cad ";
		
		return $sql;
	}
		
	$id_kw = $robo == 'LEG' ? 52 : 147;
	
	$arr_st = array();
	
	$rs = mysql_query("select f.robo_ins, count(distinct f.id_followup) as qtd " .
					  "from lcm_followup f inner join lcm_case c on f.id_case = c.id_case  " .
					  "join lcm_keyword_case as kc on kc.id_case=c.id_case " .
					  "where system_name = '$robo' and kc.id_keyword = $id_kw and " . 
					  "c.`status` = 'open' and not kc.value is null and f.robo_ins in (0, 1) " .	
					  "group by f.robo_ins order by 1", $con_sisjur);
	
	while ($ln = mysql_fetch_assoc($rs))
	{
		$arr_st[] = array('st' => $ln['robo_ins'], 'qtd' => $ln['qtd']);
	}

	$rs = mysql_query("select st, count(*) as qtd from tb_rob_relatorio " . 
	                  "where st > 1 and tipo = '$robo' group by st", $con_robo);
	
	while ($ln = mysql_fetch_assoc($rs))
	{
		$arr_st[] = array('st' => $ln['st'], 'qtd' => $ln['qtd']);
	}	
	
	
?>
