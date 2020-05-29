<?php 
$mydiv = $_POST['mydiv'];
$tipo1 = $_POST['tipo1'];
$tipo2 = $_POST['tipo2'];

$curl = curl_init("https://fipeapi.appspot.com/api/1/$tipo1/veiculos/$tipo2.json");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$json = curl_exec($curl);
curl_close($curl);

$encoded = json_decode($json);
echo "<select id='sel_fipe' onchange='consultafipe(\"".$mydiv."\",\"ano\",\"".$tipo1."\",\"".$tipo2."\",this.value,\"\")' >";
echo "<option value=''>Selecione o Modelo</option>";
foreach ($encoded as $e){
	echo "<option value='".$e->{'id'}."' >".$e->{'name'}."</option>";
}
echo "</select>";

?>