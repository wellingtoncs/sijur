<?php

$file = file("parasitas.txt");
$n=0;
$perc="";
$p1 = explode("_|_",$file[0]);
foreach($p1 as $r){
	if($r!=""){
		$p = explode("=",$r);
		if($n>0){
			$perc .= "_|_";
		}
		$perc .= $p[0]."=". $_POST[$p[0]];
	}
	$n++;
}

$fp   = fopen("parasitas.txt", "w");
$escreve = fwrite($fp, $perc);
fclose($fp);

echo "Cadastraro com Sucesso!"
?>