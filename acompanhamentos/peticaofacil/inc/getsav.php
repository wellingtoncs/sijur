<?php
	
	include("../inc/functions.php");
	include("../inc/seguranca.php");
	protegePagina();
	
	$tipo_id = $_POST['tipo_id'];
	$nomtipo = fc_select_name('tipo_id',$tipo_id,'tipo_nome','tp_tipo_tb',$conexao1);
	$nomtipo = limita_caracteres($nomtipo,20,false);
	 
	$nomecli = preg_replace("[^a-zA-Z0-9_]", "", strtr($_POST['nomepet'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
	$nomtipo = preg_replace("[^a-zA-Z0-9_]", "", strtr($nomtipo, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ= ", "aaaaeeiooouucAAAAEEIOOOUUC-_"));
	$nompeca = $nomtipo."-".$nomecli;
	
			
	$usu_nivel = $_SESSION['usuarioNivel'];
	$usu_idusu = $_SESSION['usuarioID'];
	
	if($_POST['flag']==1){
		
		$query_doc = mysql_query("INSERT INTO tp_pecas_tb SET 
		tipo_id='"	 .$tipo_id."', 
		id_usu='"	 .$usu_idusu."', 
		nome_pecas='".$nomtipo."', 
		nome_cli='"	 .$nomecli."', 
		cod_pecas='" .str_replace("_|_","&",$_POST['name_text'])."', 
		data_cad='"	 .date('Y-m-d H:i:s')."' ");
			
		$q_peca = mysql_query("SELECT MAX(t.id_pecas) AS 'id_pecas' FROM tp_pecas_tb AS t",$conexao1);
		$w_peca = mysql_fetch_array($q_peca);
		echo $w_peca['id_pecas'];
		
	}elseif($_POST['flag']==2){
		$query_doc = mysql_query("UPDATE tp_pecas_tb SET 
		tipo_id='"	 .$tipo_id."', 
		id_usu='"	 .$usu_idusu."', 
		nome_pecas='".$nomtipo."', 
		nome_cli='"	 .$nomecli."', 
		cod_pecas='" .str_replace("_|_","&",$_POST['name_text'])."', 
		data_cad='"	 .date('Y-m-d H:i:s')."'
		where id_pecas = '".$_POST['id_pecas']."' ");
		
		echo $_POST['id_pecas'];
	}
?>