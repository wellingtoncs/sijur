<?php

$file = file_get_contents("consulta_".date('ymd').".txt");
$ro1 = explode("_|_",$file);
if(!in_array($_POST['flag'],$ro1)){
	$file = $file."_|_".$_POST['flag'];
}
$fp   = fopen("consulta_".date('ymd').".txt", "w");
$escreve = fwrite($fp, $file);
fclose($fp);
echo 1;

?>