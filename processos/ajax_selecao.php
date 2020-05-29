<?php

include('inc/inc.php');

if($_POST['flag']=='S1'){
	
	if($_POST['jusid']=="1" || $_POST['jusid']=="2" || $_POST['jusid']=="3" || $_POST['jusid']=="4" ){
		echo "<select class='select_vara_1' name='select_vara_1' onchange='competencias(this.value,2);' style='width:95%'>";
		$q = mysql_query("SELECT * FROM lcm_list_regiao WHERE jus_id='".$_POST['jusid']."'");
		$n=0;
		while ($w = mysql_fetch_array($q)){
			$n++;
			if($n==1){
				echo "<option value='0' >Selecione a ".$w[3]."</option>";
			}
			echo "<option value='" . $w[0] . "' >" . $w[2] . "</option>";
		}
		echo "</select>";
	}
}elseif($_POST['flag']=='S2'){
	if($_POST['jusid']==2){
		$vrjz = 'Juizado';
		$nr = 'o';
	}else{
		$vrjz = 'Vara';
		$nr = 'a';
	}
	if($_POST['jusid']!=5 && $_POST['jusid']!=6){
		echo "<select class='select_vara_2' name='select_vara_2' onchange='competencias(".$_POST['jusid'].",3);' style='width:25%'>";
		echo "<option value='0' >".$vrjz."</option>";
		echo "<option value='' ></option>";
		for($i=1; $i<=50;$i++){
			echo "<option value='".$i."' >".htmlentities($i.$nr)."</option>";
		}
		echo "</select>";
	}
}elseif($_POST['flag']=='S3'){
	echo "<select class='select_vara_2' name='select_vara_3' onchange='competencias();' style='width:70%'>";
	$q = mysql_query("SELECT * FROM lcm_list_vara WHERE reg_id='".$_POST['jusid']."'");
	echo "<option value='0' >Complemente</option>";
	while ($w = mysql_fetch_array($q)){
		echo "<option value='" . $w[0] . "' >" . $w[2] . "</option>";
	}
	echo "</select>";
}
?>