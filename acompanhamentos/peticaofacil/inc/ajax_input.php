<?php
header("Content-Type: text/html; charset=ISO-8859-1",true);

include("seguranca.php");
protegePagina();

if($_POST['flag']=="I")
{	
	$inptitle = $_POST['inptitle'] 	? strtoupper($_POST['inptitle']) : "";
	$tipopet  = $_POST['tipopet']	? $_POST['tipopet']  : "";
	$db_col	  = $_POST['db_col']	? $_POST['db_col']   : "";
	$inputcol = $_POST['inputcol']	? $_POST['inputcol'] : "";
	$inpcheck = $_POST['inpcheck']	? $_POST['inpcheck'] : "";
	$inputReq = $_POST['inputReq']	? $_POST['inputReq'] : "";
	$inptFocus= $_POST['inptFocus']	? $_POST['inptFocus'] : "";
	$tbBase   = $_POST['tbBase']	? $_POST['tbBase'] 	 : "";
	
	if($_POST['inputcol']==1){$twidth=265;}elseif($_POST['inputcol']==2){$twidth=560;}elseif($_POST['inputcol']==3){$twidth=860;}
	
	$Qconf = mysql_query("SELECT id_input FROM tp_inputs_tb WHERE input_title = '" . $inptitle . "' AND tipo_id = '" . $tipopet ."' AND listsel = 'N' ",$conexao1);
	if(mysql_num_rows($Qconf)>0)
	{
		echo 2;
		exit;
	}
	
	if($_POST['dadSel']=="TIPOINP")
	{
		$qIns  = "INSERT INTO tp_inputs_tb SET ";
		$qIns .= "tipo_id 	  = $tipopet, ";
 	    $qIns .= $inptitle   != "" ? "input_title = '$inptitle', " : "";
	    $qIns .= $db_col     != "" ? "input_val   = '$db_col', "   : "";
	    $qIns .= $inpcheck   != "" ? "input_alt   = '$inpcheck', " : "";
	    $qIns .= $inputcol   != "" ? "input_cols  = '$inputcol', " : "";
	    $qIns .= $inptFocus  != "" ? "input_func  = '$inptFocus', " : "";
	    $qIns .= "input_width = $twidth, ";
	    $qIns .= "input_req   = $inputReq, ";
	    $qIns .= "input_order = (select if(max(t.input_order),max(t.input_order)+1,1) from tp_inputs_tb as t where t.tipo_id = '$tipopet' AND t.listsel = 'N') ";
		$query = mysql_query($qIns,$conexao1);
		echo 1;
	}
	elseif($_POST['dadSel']=="TIPOSEL")
	{
		$qIns  = "INSERT INTO tp_inputs_tb SET ";
		$qIns .= "tipo_id 	  = $tipopet, ";
		$qIns .= $inptitle   != "" ? "input_title = '$inptitle', " : "";
		$qIns .= "input_tipo  = 'SELECT', ";
		$qIns .= $tbBase 	 != "" ? "input_db 	= '$tbBase', " : "";
		$qIns .= "input_cols  = 1, ";
		$qIns .= $db_col 	 != "" ? "input_val 	= '$db_col', " : "";
		$qIns .= "input_order = (select if(max(t.input_order),max(t.input_order)+1,1) from tp_inputs_tb as t where t.tipo_id = '$tipopet' AND t.listsel = 'N') ";
		$query = mysql_query($qIns,$conexao1);
		
		$mxInp = mysql_query("SELECT MAX(id_input) FROM tp_inputs_tb WHERE listsel = 'N' limit 1 ",$conexao1);
		$mxWil = mysql_fetch_array($mxInp);
		
		$dadI  = explode("_|_",$_POST['dadI']);
		
		foreach($dadI as $dd)
		{
			if($dd!="")
			{
				$q = mysql_query("INSERT INTO tp_dados_tb SET id_input =  " . $mxWil[0] . ", nome_dados = '" . $dd . "', return_1	= '', id_setor = 1, listsel = 'N' ",$conexao1);
			}
		}
		echo 1;	
	}
	elseif($_POST['dadSel']=="TIPOTIT")
	{
		$qIns  = " INSERT INTO tp_inputs_tb SET";
		$qIns .= " tipo_id = " . $tipopet . ",";
		$qIns .= " input_title = '" . $inptitle . "',";
		$qIns .= " input_tipo = 'TITLE',";
		$qIns .= " input_cols=3,";
		$qIns .= " input_width=860,";
		$qIns .= " input_order = (select if(max(t.input_order),max(t.input_order)+1,1) from tp_inputs_tb as t where t.tipo_id = '$tipopet' AND t.listsel = 'N') ";
		mysql_query($qIns,$conexao1);
		echo 1;
	}
}
if($_POST['flag']=="E" && $_POST['campoId']!='')
{
	
	$inptitle = $_POST['inptitle'] 	? strtoupper($_POST['inptitle']) : "";
	$tipopet  = $_POST['tipopet']	? $_POST['tipopet']  : "";
	$db_col	  = $_POST['db_col']	? $_POST['db_col']  : "";
	$inputcol = $_POST['inputcol']	? $_POST['inputcol']  : "";
	$inpcheck = $_POST['inpcheck']	? $_POST['inpcheck'] : "";
	$inputReq = $_POST['inputReq']	? $_POST['inputReq'] : "";
	$inptFocus= $_POST['inptFocus']	? $_POST['inptFocus'] : "";
	$tbBase   = $_POST['tbBase']	? $_POST['tbBase'] : "";
	$campoId  = $_POST['campoId']	? $_POST['campoId'] : "";
	
	if($_POST['inputcol']==1){$twidth=265;}elseif($_POST['inputcol']==2){$twidth=560;}elseif($_POST['inputcol']==3){$twidth=860;}
	
	if($_POST['dadSel']=="TIPOINP")
	{
		$query = mysql_query("UPDATE tp_inputs_tb SET 
							  tipo_id 	  = $tipopet, 
							  input_title = '$inptitle', 
							  input_val   = '$db_col',
							  input_alt   = '$inpcheck', 
							  input_cols  = '$inputcol',
							  input_focus = '$inptFocus',
							  input_width = $twidth,
							  input_req   = $inputReq
							  WHERE id_input = $campoId 
							  AND listsel = 'N'
							  ",$conexao1);
		echo 1;
	}
	elseif($_POST['dadSel']=="TIPOSEL")
	{
		
		$query = mysql_query("UPDATE tp_inputs_tb SET 
								tipo_id 	= $tipopet,
								input_title = '$inptitle',
								input_tipo 	= 'SELECT',
								input_db 	= '$tbBase',
								input_cols	= 1, 
								input_val 	= '$db_col' 
								WHERE id_input = $campoId 
								AND listsel = 'N'
								",$conexao1);
								
		//deleta os possíveis antigos dados da seleção anterior
		$mxInp = mysql_query("DELETE FROM tp_dados_tb WHERE id_input = $campoId AND listsel = 'N' ",$conexao1);
		$mxWil = mysql_fetch_array($mxInp);
		
		$dadI  = explode("_|_",$_POST['dadI']);
		
		foreach($dadI as $dd)
		{
			if($dd!="")
			{
				$q = mysql_query("INSERT INTO tp_dados_tb SET id_input =  $campoId, nome_dados = '" . $dd . "', return_1	= '', id_setor = 1, listsel = 'N'",$conexao1);
			}
		}
		echo 1;	
	}
	elseif($_POST['dadSel']=="TIPOTIT")
	{
		$query = mysql_query("UPDATE tp_inputs_tb SET tipo_id = " . $tipopet . ", input_title = '" . $inptitle . "', input_tipo = 'TITLE', input_cols=3, input_width=860 WHERE id_input = $campoId AND listsel = 'N' ",$conexao1);
		echo 1;
	}
}
elseif($_POST['flag']=="D")
{
	$query = mysql_query("DELETE FROM `tp_inputs_tb` WHERE `id_input`= " . $_POST['idvalor'] . " AND listsel = 'N' LIMIT 1 ",$conexao1);
	$query = mysql_query("DELETE FROM `tp_dados_tb` WHERE `id_input`= " . $_POST['idvalor'] . " AND listsel = 'N' ",$conexao1);
	echo 1;
}
elseif($_POST['flag']=="L")
{
	if($_POST['dadSel']=="TIPOSEL")
	{
		$qIns  = "INSERT INTO tp_inputs_tb SET ";
		$qIns .= "tipo_id 	  = 1, ";
		$qIns .= $inptitle   != "" ? "input_title = '$inptitle', " : "";
		$qIns .= "input_tipo  = 'SELECT', ";
		$qIns .= $tbBase 	 != "" ? "input_db 	= '$tbBase', " : "";
		$qIns .= "input_cols  = 1, ";
		$qIns .= $db_col 	 != "" ? "input_val 	= '$db_col', " : "";
		$qIns .= "input_order = (select if(max(t.input_order),max(t.input_order)+1,1) from tp_inputs_tb as t where t.tipo_id = $tipopet AND t.listsel = 'Y') ";
		$query = mysql_query($qIns,$conexao1);
		
		$mxInp = mysql_query("SELECT MAX(id_input) FROM tp_inputs_tb AND listsel = 'N' limit 1 ",$conexao1);
		$mxWil = mysql_fetch_array($mxInp);
		
		$dadI  = explode("_|_",$_POST['dadI']);
		
		foreach($dadI as $dd)
		{
			if($dd!="")
			{
				$q = mysql_query("INSERT INTO tp_dados_tb SET id_input =  " . $mxWil[0] . ", nome_dados = '" . $dd . "', return_1	= '', id_setor = 1, listsel = 'Y' ",$conexao1);
			}
		}
		echo 1;	
	}
}
?>
