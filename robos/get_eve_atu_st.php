<?php

function fc_get_evento_legem($id, $id_followup, $dt_hr)
{
	global $con_robo;
	
	$rs = mysql_query("select l.descricao, l.codigo " .
					  "from tb_rob_eventos_sisjur s inner join tb_rob_eventos_legem l " .
					  "on s.id_legem = l.id_legem " .
					  "where s.codigo = '$id' ", $con_robo);
	$status = 5;
	$evento = '';
		
	if ($ln = mysql_fetch_assoc($rs))
	{
		if ($ln['codigo'] > 0)
		{
			$status = 0;
			$evento = $ln['descricao'];
		}
		else
		{
			$status = 3;
		}
	}			
		
	if ($status > 0)
	{
		fc_at_status($id_followup, $dt_hr, $status);
	}
	
	return $evento;
}	

function fc_get_evento_srs($id, $id_followup, $dt_hr)
{
	global $con_robo;
	
	$rs = mysql_query("select r.descricao, r.codigo, s.descricao as descr_srs " .
					  "from tb_rob_eventos_sisjur s inner join tb_rob_eventos_srs r " .
					  "on s.id_srs = r.id_srs " .
					  "where s.codigo = '$id' ", $con_robo);
	$status = 5;
	$evento = '';
		
	if ($ln = mysql_fetch_assoc($rs))
	{			
		if (strpos($ln['descricao'], 'FOLLOW UP') !== false)
		{
			$status = 0;
			$evento = 'FOLLOWUP|' . $ln['descr_srs'];
		}
		elseif (!empty($ln['codigo']))
		{
			$status = 0;
			$evento = $ln['codigo'] . '|' . $ln['descr_srs'];	
		}
		else
		{
			$status = 3;
		}
	}			
		
	if ($status > 0)
	{
		fc_at_status($id_followup, $dt_hr, $status);
	}
	
	return $evento;
}

function fc_at_status($id, $dt_hr, $status)
{
	global $con_sisjur, $con_robo, $robo;
	
	if (mysql_query("update lcm_followup set robo_ins = $status, robo_cad = '$dt_hr' " .
					"where id_followup = $id", $con_sisjur))
	{
		$rs = mysql_query("select * from tb_rob_relatorio where id_followup = $id", $con_robo);
		
		$fld_dt = "dh_st_$status"; 
		
		if ($ln = mysql_fetch_assoc($rs))
		{
			mysql_query("update tb_rob_relatorio set $fld_dt = '$dt_hr', st = $status " .
						"where id_followup = $id", $con_robo);
		}
		else
		{
			mysql_query("insert into tb_rob_relatorio (id_followup, $fld_dt, tipo, st) values " .
						"($id, '$dt_hr', '$robo', $status)", $con_robo);
		}
		
		mysql_query("update tb_rob_sit set last_time = " . microtime(true) . " where robo = '$robo'", $con_robo);
	}
}
?>