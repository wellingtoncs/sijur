
<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<title>Acompanhamento dos Andamentos</title>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<script type='text/javascript' src="../js/jquery-1.9.1.js"></script>
<script type='text/javascript' src="../js/jquery-ui.js"></script>
<!--script type='text/javascript' src='../jFilterXCel2003.js'></script-->
<link rel="stylesheet" href="../css/style.css" />
<input type="hidden" id="txt" value="0" />
<style>
#id_progress{
	border:1px solid red;
	text-align:center;
	
}
</style>
<?php
	$pgrs = date("YmdHis") . ".txt";
	$fp = fopen($pgrs, "w");
	$escreve = fwrite($fp, 0);
	fclose($fp);
?>
<script type='text/javascript'>
var tempo = new Number();
tempo = 1;
var pgrs = "<?php echo $pgrs; ?>";
function startCountdown(){
	if((tempo + 1) <= 100){
		var seg = parseInt($("#txt").html());
		$( "#progressbar" ).progressbar({ value: seg });
		setTimeout('startCountdown()',500);
		tempo++;
		$("#txt").load(pgrs);
		$("#return_html").html("<span style='font-size:18pt; color:#999;font-weight:bold;'>" + parseInt($("#txt").html()) + " % </span>");
	}
}
startCountdown();

$(document).ready(function(){
	
	$("#return_html").html("<img src='../img/aguarde_g.gif' />");
	$.ajax({
		type: "POST",
		url:  "index_ajax.php",
		data: "flag=&pgrs=" + pgrs,
		success: function(retorno_ajax){
			$("#progressbar").hide();
			$("#aguarde").hide();
			$("#return_html").html(retorno_ajax);
			carregarFiltros('tbf1');
		}
	});
});

</script>
<table style='width:100%;text-align:center'>
	<tr>
		<td align="center">
			<div id='aguarde' style='font-size:18pt; color:#999; '>ACOMPANHAMENTO DOS ANDAMENTOS NO SIJUR<br>Aguarde...</div>
		</td>
	</tr>
	<tr>
		<td align="center">
			<div style="width:500px;" id="progressbar" ></div>
		</td>
	</tr>
	<tr>
		<td align="center">
			<div id='return_html'></div>
		</td>
	</tr>
</table>

