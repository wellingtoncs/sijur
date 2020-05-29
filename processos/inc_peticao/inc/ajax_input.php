<?php
header("Content-Type: text/html; charset=ISO-8859-1",true);

include("conectar.php");


if($_POST['flag']=="I")
{
	
	$inptitle = $_POST['inptitle'] 	? strtoupper($_POST['inptitle']) : "''";
	$tipopet  = $_POST['tipopet']	? $_POST['tipopet']  : "''";
	$db_col	  = $_POST['db_col']	? $_POST['db_col']  : "''";
	$inputcol = $_POST['inputcol']	? $_POST['inputcol']  : "''";
	$inpcheck = $_POST['inpcheck']	? $_POST['inpcheck'] : "''";
	$inputReq = $_POST['inputReq']	? $_POST['inputReq'] : "''";
	$inptFunc = $_POST['inptFunc']	? $_POST['inptFunc'] : "''";
	$tbBase   = $_POST['tbBase']	? $_POST['tbBase'] : "''";
	if($_POST['inputcol']==1){$twidth=265;}elseif($_POST['inputcol']==2){$twidth=560;}elseif($_POST['inputcol']==3){$twidth=860;}
	
	$Qconf = mysql_query("SELECT id_input FROM tp_inputs_tb WHERE input_title = '" . $inptitle . "' ",$conexao1);
	if(mysql_num_rows($Qconf)>0)
	{
		echo 2;
		exit;
	}
	
	if($_POST['dadSel']=="TIPOINP")
	{
		$query = mysql_query("INSERT INTO tp_inputs_tb SET 
							  tipo_id 	  =  " . $tipopet  . ", 
							  input_title = '" . $inptitle . "', 
							  input_val   = '" . $db_col   . "',
							  input_alt   = '" . $inpcheck . "', 
							  input_cols  = '" . $inputcol . "',
							  input_func  = '" . $inptFunc . "',
							  input_width =  " . $twidth   . ",
							  input_req   =  " . $inputReq . "
							  
							  ",$conexao1);
		echo 1;
	}
	elseif($_POST['dadSel']=="TIPOSEL")
	{
		$query = mysql_query("INSERT INTO tp_inputs_tb SET 
								tipo_id 	= " . $tipopet . ",
								input_title = '" . $inptitle . "',
								input_tipo 	= 'SELECT',
								input_db 	= '" . $tbBase . "',
								input_cols	= 1, 
								input_val 	= '" . $db_col . "' 
								",$conexao1);
		
		$mxInp = mysql_query("SELECT MAX(id_input) FROM tp_inputs_tb limit 1 ",$conexao1);
		$mxWil = mysql_fetch_array($mxInp);
		
		$dadI  = explode("_|_",$_POST['dadI']);
		
		foreach($dadI as $dd)
		{
			if($dd!="")
			{
				$q = mysql_query("INSERT INTO tp_dados_tb SET id_input =  " . $mxWil[0] . ", nome_dados = '" . $dd . "', return_1	= '', id_setor = 1",$conexao1);
			}
		}
		echo 1;	
	}
	elseif($_POST['dadSel']=="TIPOTIT")
	{
		$query = mysql_query("INSERT INTO tp_inputs_tb SET tipo_id = " . $tipopet . ", input_title = '" . $inptitle . "', input_tipo = 'TITLE', input_cols=3, input_width=860 ",$conexao1);
		echo 1;
	}
}
elseif($_POST['flag']=="D")
{
	$query = mysql_query("DELETE FROM `tp_inputs_tb` WHERE `id_input`= " . $_POST['idvalor'] . " LIMIT 1 ",$conexao1);
	$query = mysql_query("DELETE FROM `tp_dados_tb` WHERE `id_input`= " . $_POST['idvalor'] . " ",$conexao1);
	echo 1;
}


?>
