<?php

include('inc/inc.php');
include_lcm('inc_filters');
include_lcm('inc_impex');

$find_adverso_string = trim(_request('find_adverso_string'));

if (!empty($_REQUEST['export']) && ($GLOBALS['author_session']['status'] == 'admin')) {
	export('adverso', $_REQUEST['exp_format'], $find_adverso_string);
	exit;
}
 
 
 function date_diff($data1,$data2)
{
	$data = $data1;
	$ano_banco = substr($data,0,4);
	$mes_banco = substr($data,5,2);
	$dia_banco = substr($data,8,2);
	$data = $dia_banco."-".$mes_banco."-".$ano_banco;
	$data_atual = $data2;
	$ano_atual = substr($data_atual,0,4);
	$mes_atual = substr($data_atual,5,2);
	$dia_atual = substr($data_atual,8,2);
	$data_atual = $dia_atual."-".$mes_atual."-".$ano_atual;
	$data = mktime(0,0,0,$mes_banco,$dia_banco,$ano_banco);
	$data_atual = mktime(0,0,0,$mes_atual,$dia_atual,$ano_atual);
	$dias = ($data_atual - $data)/86400;
	$dias = ceil($dias);
	return $dias;
}

lcm_page_start(_T('title_andamentos_list'), '', '', 'adversos_intro');
//FT Ocultando a informação de ajuda
//lcm_bubble('adverso_list');
//show_find_box('adverso', $find_adverso_string, '', (string)($GLOBALS['author_session']['status'] == 'admin') );


echo '<table border="0" align="center" class="tbl_usr_dtl" width="99%">' . "\n";
	echo "<tr>\n";
		echo '<th class="heading" nowrap="nowrap">Qtd</th>';
		echo '<th class="heading" nowrap="nowrap">&Uacute;ltimo Andamento</th>';
		echo '<th class="heading" nowrap="nowrap">Tempo</th>';
	echo "</tr>\n";
	
	$qst  = "SELECT * FROM lcm_stopped";
	$res = lcm_query($qst);
	while($wst = mysql_fetch_array($res))
	{
		$arr_std[] = array($wst[0],$wst[1]);
	}	

	//FT--- andamentos antigos -----
	$q = "SELECT f.id_case, f.`type`, f.date_start
	FROM lcm_followup AS f
	JOIN lcm_case as c on c.id_case = f.id_case
	WHERE f.date_start = (SELECT MAX(fu.date_start) FROM lcm_followup AS fu WHERE fu.id_case = f.id_case)";
	

	//$q = "SELECT id_case FROM lcm_case WHERE status = 'open' ";
	$result = lcm_query($q);
	if (lcm_num_rows($result) > 0)
	{
		$i=1;
		while ($row=lcm_fetch_array($result)) 
		{			
			foreach($arr_std as $std_n)
			{
				if($row[1]==$std_n[0])
				{
					if(date_diff($row[2],date('Y-m-d')) >= 30)
					{
						$qtd[$std_n[0]][] = array($row[0],$row[2]);
					}
				}
			}
		}
	}
	foreach($arr_std as $std_n)
	{
		$i++;
		echo "<tr>\n";
			echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>" . count($qtd[$std_n[0]]) . "</td>\n";
			echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>" . ($std_n[0] ? "<a href=\"listcases.php?pasta_f=&parte_f=&status_f=open&case_owner=&processo_f=&comar_f=&state_f=&case_period=all&cliente_f=&type_f=" . $std_n[0] . "&last_f=on&condicao_f=&vara_f=&acao_f=&stopday_f=" . $std_n[1] . "&submit=submit\">" . _T('kw_followups_' . $std_n[0] . '_title') . "</a>" : _T('kw_followups_' . $std_n[0] . '_title')) . "</td>\n";
			//echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>" . _T('kw_followups_' . $std_n[0] . '_title') . "</td>\n";
			echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>+ de <b>" . $std_n[1] . "</b> dias parado.</td>\n";
		echo "</tr>\n";		
	
	}
	echo "</table>\n";
//FT--------------------------------------------------

?>
<br /><br />
<?php
lcm_page_end();
?>
