<?php
if(isset($_POST['ficheroExcel']))
{
$ficheroExcel = $_POST['ficheroExcel'];
}
else
{
$ficheroExcel = 'ficheroExcel';
}
header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=$ficheroExcel.xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $_POST['datos_a_enviar'];
?>