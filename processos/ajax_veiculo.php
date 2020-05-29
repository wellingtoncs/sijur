<?php

include('inc/inc.php');

if($_POST['flag']=='S1'){
	
	echo "<select class='select_notes_1' name='select_notes_1' onchange='veiculos(this.value,2);' style='width:95%'>";
	$q = mysql_query("SELECT * FROM lcm_list_modelo WHERE veic_id='".$_POST['veicid']."' order by nome_modelo ");
	$n=0;
	while ($w = mysql_fetch_array($q)){
		$n++;
		if($n==1){
			echo "<option value='0' >Selecione a ".$w[3]."</option>";
		}
		echo "<option value='" . $w[0] . "' >" . $w[2] . "</option>";
	}
	echo "</select>";
		
}elseif($_POST['flag']=='S2'){
	if($_POST['veicid']!=5 && $_POST['veicid']!=6){
		echo "<select class='select_notes_2' name='select_notes_2' onchange='veiculos(".$_POST['veicid'].",3);' style='width:25%'>";
		//echo "<option value='0' >".$vrjz."</option>";
		echo "<option value='0' >Ano</option>";
		echo "<option value='' ></option>";
		for($i=1990; $i<=2020;$i++){
			echo "<option value='".$i."' >".htmlentities($i.$nr)."</option>";
		}
		echo "</select>";
	}
}elseif($_POST['flag']=='S3'){
	echo "<select class='select_notes_3' name='select_notes_3' onchange='veiculos(".$_POST['veicid'].",4);' style='width:70%'>";
	$q = mysql_query("SELECT * FROM lcm_list_cor ");
	echo "<option value='0' >Cor</option>";
	while ($w = mysql_fetch_array($q)){
		echo "<option value='" . $w[1] . "' >" . $w[1] . "</option>";
	}
	echo "</select>";
	
}elseif($_POST['flag']=='S4'){
	echo "<input type='text' class='select_notes_4' name='select_notes_4' onfocus='$(this).val(\"\");' onblur='veiculos(".$_POST['veicid'].",5);' style='width:30%' value='Chassi' />";
}elseif($_POST['flag']=='S5'){
	echo "<input type='text' class='select_notes_5' name='select_notes_5' onfocus='$(this).val(\"\");' onblur='veiculos(".$_POST['veicid'].",6);' style='width:30%' value='Placa' />";
}elseif($_POST['flag']=='S6'){
	echo "<input type='text' class='select_notes_6' name='select_notes_6' onfocus='$(this).val(\"\");' onblur='veiculos();' style='width:30%' value='Renavam' />";
}
?>