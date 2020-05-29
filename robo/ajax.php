<?php

if($_POST['flag']!=""){
	$ativ_file = file("http://10.10.0.212/ativar.php?robot=".$_POST['flag']);
	echo $ativ_file[1];
}else{
	echo "Erro no servidor!";
}
?>