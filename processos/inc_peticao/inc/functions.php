<?php
function formata_data_extenso($strDate)
{
	$arrMonthsOfYear = array(1 => 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
	$intDayOfMonth = date("d");
	$intMonthOfYear = date("n");
	$intYear = date("Y");
	return $intDayOfMonth . ' de ' . $arrMonthsOfYear[$intMonthOfYear] . ' de ' . $intYear. '.';
}

function fc_select($p_tb,$p_id,$val_id,$val_nome,$usu,$conex,$p_setor="")
{
	$q = mysql_query("SELECT $val_id , $val_nome FROM " . $p_tb. " where tipo_usu = " . $usu . " " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " GROUP BY " . $val_nome . " ORDER BY " . $val_nome. " ",$conex);
	echo "<option></option>";
	
	while($w = mysql_fetch_array($q))
	{
		echo "<option value='" . $w[$val_id] . "' " . ($w[$val_id] == "$p_id" ? "selected" : "") . ">" . $w[$val_nome] . "</option>";
	}
}

function fc_select_dados($id_input,$conex,$p_setor="")
{
	$q = mysql_query("SELECT id_dados, nome_dados FROM tp_dados_tb where id_input = '$id_input' " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " ORDER BY nome_dados asc ",$conex);
	echo "<option></option>";
	
	while($w = mysql_fetch_array($q))
	{
		echo "<option value='" . $w['id_dados'] . "' " . ($w[$val_id] == "$p_id" ? "selected" : "") . ">" . $w['nome_dados'] . "</option>";
	}
}

?>
