<?php 
$mydiv = $_POST['mydiv'];
$tipo1 = $_POST['tipo1'];
$tipo2 = $_POST['tipo2'];
$tipo3 = $_POST['tipo3'];
$tipo4 = $_POST['tipo4'];

$curl = curl_init("http://fipeapi.appspot.com/api/1/$tipo1/veiculo/$tipo2/$tipo3/$tipo4.json");

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$json = curl_exec($curl);
curl_close($curl);

$encoded = json_decode($json);

	echo "Marca: ".$encoded->{'marca'}."<br>";
	echo "Modelo: ".$encoded->{'name'}."<br>";
	echo "Ano Modelo: ".$encoded->{'ano_modelo'}."<br>";
	echo "Preço: ".$encoded->{'preco'}."<br>";
	echo "<input type='hidden' id='hd_$mydiv' value='".str_replace(",",".",str_replace(".","",str_replace("R$ ","",$encoded->{'preco'})))."' />";

?>