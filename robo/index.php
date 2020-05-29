<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=UTF-8'>
<meta http-equiv="refresh" content="60">
<script type='text/javascript' src='jquery-1.8.3.min.js'></script>
<?php

	$file_srs = file("http://10.10.0.212/status.php?robot=SRS");
	$file_leg = file("http://10.10.0.212/status.php?robot=LEG");
	
?>
<style>
.alpha{
	 filter:alpha(opacity=50);
     opacity: 0.5;
     -moz-opacity:0.5;
     -webkit-opacity:0.5;
}
</style>
</head>
<body>
<table style="width:100%;border-collapse:collapse; font-size:80px" align="center" border="1">
	<tr height="100px"><td align='center' colspan="5"><b>ROBÔ - EA</b></td></tr>
</table>
<br>
<table style="width:100%;border-collapse:collapse; font-size:60px" align="center" border="1">
	<tr height="50px">
		<td align='right' width="40%">SRS:</td>
		<td align='center' width="60%">
			<?php
			if(trim($file_srs[0])=="<img src='img/circle_red.png'>"){
				?>
				<span align="center"><img src='circle_red_m.png'></span>
				<?php
			}else{
				?>
				<span align="center"><img src='circle_green_m.png'></span>
				<?php
			}
			?>
		</td>
		</td>
	</tr>
	<tr height="50px">
		<td colspan="2" align='center'>&nbsp;</td>
	</tr>
	<tr height="50px">
		<td align='right' width="40%">LEGEM:</td>
		<td align='center' width="60%">
			<?php
			if(trim($file_leg[0])=="<img src='img/circle_red.png'>"){
				?>
				<span align="center"><img src='circle_red_m.png'></span>
				<?php
			}else{
				?>
				<span align="center"><img src='circle_green_m.png'></span>
				<?php
			}
			?>
		</td>
	</tr>
	<tr height="500px">
		<td align='center' colspan="2" width="60%">
			<script type="text/javascript">
			$(function(){
				$("#minhaDiv").hide();
				$("#bt_opem").click(function(){
					$("#minhaDiv").slideToggle();
					setTimeout(function(){
						if($("#minhaDiv").is(":visible")==true){ 
							$("#bt_opem").attr("src","bottom_A.png").anime();
						}else if($("#minhaDiv").is(":visible")==false){
							$("#bt_opem").attr("src","bottom.png").anime();
						}
					}, 500);
				});
				$(".star").click(function(){
					$(this).attr("src","loading_128.gif");
				});

			});
			function ativarobo(valor){
				
				$.ajax({
					type: "POST",
					url:  "ajax.php", 
					data: "flag="+valor,
					success: function(retorno_ajax){
						alert(retorno_ajax);
						location.reload(); 
					}
				});
			}
			</script>
			<div height="50px">&nbsp;</div>
			<div style="text-align:right; width:100%; height:120px;cursor:pointer;border:1px solid #999;">
				<img id="bt_opem" src="bottom.png" style="cursor:pointer">
			</div>
			<div id="minhaDiv" style="width:100%; height:auto; background:#ebebeb;" >
				<table style="width:100%;border-collapse:collapse; font-size:60px" align="center" border="1">
					<tr height="50px">
						<td align='right' width="40%">SRS:</td>
						<td align='center' width="60%">
							<?php
							if(trim($file_srs[0])=="<img src='img/circle_red.png'>"){
								if(trim($file_leg[0])!="<img src='img/circle_green.png'>"){
									?>
									<span align="center"><a href='#' onclick='ativarobo("SRS");'><img class='star' id='star_SRS' src='start_g.png'></a></span> 
									<?php
								}else{
									?>
									<img class="alpha" src="start_g.png">
									<?php
								}
							}else{
								?>
								<span align="center"><a href='#' onclick='ativarobo("OFF");'><img class='star' src='stop_g.png'></a></span>
								<?php
							}
							?>
						</td>
						</td>
					</tr>
					<tr height="50px">
						<td colspan="2" align='center'>&nbsp;</td>
					</tr>
					<tr height="50px">
						<td align='right' width="40%">LEGEM:</td>
						<td align='center' width="60%">
							<?php
							if(trim($file_leg[0])=="<img src='img/circle_red.png'>"){
								if(trim($file_srs[0])!="<img src='img/circle_green.png'>"){
									?>
									<span align="center"><a href='#' onclick='ativarobo("LEG");'><img class='star' id='star_LEG' src='start_g.png'></a></span> 
									<?php
								}else{
									?>
									<img class="alpha" src="start_g.png">
									<?php
								}
							}else{
								?>
								<span align="center"><a href='#' onclick='ativarobo("OFF");'><img class='star' src='stop_g.png' ></a></span>
								<?php
							}
							?>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
</body>
</html>