<?php 
$mydiv = $_POST['mydiv'];
$tipo1 = $_POST['tipo1'];
$tipo2 = $_POST['tipo2'];
$tipo3 = $_POST['tipo3'];

$curl = curl_init("http://fipeapi.appspot.com/api/1/$tipo1/veiculo/$tipo2/$tipo3.json");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$json = curl_exec($curl);
curl_close($curl);

$encoded = json_decode($json);
echo "<select id='sel_fipe' onchange='consultafipe(\"".$mydiv."\",\"valor\",\"".$tipo1."\",\"".$tipo2."\",\"".$tipo3."\",this.value)' >";
echo "<option value=''>Selecione o Ano</option>";
foreach ($encoded as $e){
	echo "<option value='".$e->{'id'}."' >".$e->{'name'}."</option>";
}
echo "</select>";

?>