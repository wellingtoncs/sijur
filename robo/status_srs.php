<form id="myForm" method="post" action="status_srs.php">
<meta http-equiv='content-type' content='text/html; charset=UTF-8'>
<script type='text/javascript' src='jquery-1.8.3.min.js'></script>
<script type='text/javascript'>
	function ativarobo(valor){
		$("#inrobo").val(valor);
		$("#myForm").submit();
	}
	//function reiniciarobo(){
	//	$( "#inrobo" ).val(valor);
	//	$( "#target" ).submit();
	//}
		$(function(){			
			
		});
	</script>
<?php

if($_POST['inrobo']!=""){
	$ativ_file = file("http://10.10.0.212/ativar.php?robot=".$_POST['inrobo']);
}
$file_srs = file("http://10.10.0.212/status.php?robot=SRS");
$file_leg = file("http://10.10.0.212/status.php?robot=LEG");

?>
<table style="width:100%;border-collapse:collapse" align="center" border="1">
	<tr height="50px"><td align='center' colspan="5"><b>ROBÔ</b></td></tr>
</table>
<br>
<table style="width:100%;border-collapse:collapse" align="center" border="1">
	<tr height="50px">
		<td align='center' width="10%">SRS:</td>
		<td align='center' width="40%">
			<?php
			if(trim($file_srs[0])=="<img src='img/circle_red.png'>"){
				?>
				<span align="center"><img src='circle_red.png'></span>
				<span align="center"><a href='#' onclick='ativarobo("SRS");'><img id='star_srs' src='start.png'></a></span> 
				<?php
			}else{
				?>
				<span align="center"><img src='circle_green.png'></span>
				<span align="center"><a href='#' onclick='ativarobo("OFF");'><img src='stop.png'></a></span>
				<script>$(function(){  $("#star_leg").hide(); });</script>
				<?php
			}
			?>
		</td>
		<td align='center' width="1%"></td>
		<td align='center' width="10%">LEGEM:</td>
		<td align='center' width="40%">
			<?php
			if(trim($file_leg[0])=="<img src='img/circle_red.png'>"){
				?>
				<span align="center"><img src='circle_red.png'></span>
				<span align="center"><a href='#' onclick='ativarobo("LEG");'><img id='star_leg' src='start.png'></a></span> 
				<?php
			}else{
				?>
				<span align="center"><img src='circle_green.png'></span>
				<span align="center"><a href='#' onclick='ativarobo("OFF");'><img src='stop.png'></a></span>
				<script>$(function(){  $("#star_srs").hide(); });</script>
			<?php
			}
			?>
		</td>
	</tr>
</table>
<br>
<table style="width:100%;border-collapse:collapse" align="center" border="1">
	<tr height="50px">
		<td align='center' colspan="2">Reiniciar Máquina: </td>
		<td align='center' colspan="2">
			<span><a href='#' onclick='reiniciarobo();'><img src="pc_on.png"></a></span>
		</td>
	</tr>
</table>
<input  name="inrobo" type="hidden" value="" id="inrobo"/>
</form>