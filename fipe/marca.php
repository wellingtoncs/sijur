<?php 
$mydiv = $_POST['mydiv'];
$tipo1 = $_POST['tipo1'];
$curl = curl_init("https://fipeapi.appspot.com/api/1/$tipo1/marcas.json");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$json = curl_exec($curl);
curl_close($curl);

$encoded = json_decode($json);
echo "<select id='sel_fipe' onchange='consultafipe(\"".$mydiv."\",\"modelo\",\"".$tipo1."\",this.value,\"\",\"\")' >";
echo "<option value=''>Selecione a Marca</option>";
foreach ($encoded as $e){
	echo "<option value='".$e->{'id'}."' >".$e->{'name'}."</option>";
}
echo "</select>";

?>