<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<title>Acompanhamento dos Andamentos</title>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<script type='text/javascript' src="../js/jquery-1.9.1.js"></script>
<script type='text/javascript' src="../js/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/style.css" />
<input type="hidden" id="txt" value="0" />
<script type="text/javascript">
$(function(){   
	$("#button").click(function(){
		var dados = "P-01="+$("#﻿P-01").val();
		for (i=2; i<41; i++) { 	
			if(i<10){
				dados += "&P-0"+i+"="+$("#P-0"+i).val(); 
			}else{
				dados += "&P-"+i+"="+$("#P-"+i).val(); 
			}
		}
		//alert(dados);
		$.ajax({
			type: "POST",
			url:  "ajax.php",
			data: dados + "&flag=",
			success: function(retorno_ajax){
				alert(retorno_ajax);
				$("#return_html").html(retorno_ajax);
				 setTimeout(function(){ location.reload(); }, 3000);
			}
		});
	});
});
	//function cadastrar_dados(){
	//}
</script>
<table style='width:100%;text-align:center;font-size:38pt; color:#999;'>
	<tr><td align="center" colspan="2"><div id='aguarde' >ACOMPANHAMENTO DOS PARASITAS</div><br></td></tr>
	<?php
		$file = file("parasitas.txt");	
		$p1 = explode("_|_",$file[0]);
		$n=0;
		foreach($p1 as $r){
			if($r!=""){
				$p = explode("=",$r);
				if($p[1]=='FABIO TORRES' || $p[1]=='RODRIGO MIGUEL' || $p[1]=='HERBETE SILVA' || $p[1]=='FABIO BEZERRA' || $p[1]=='LUCIANO SILVA' ){
					$color='#000';
				}elseif($p[1]=='DEFEITO' || $p[1]=='PERDIDO'){
					$color="#ccc";
				}else{
					$color='blue';
					$n++;
				}
				if(strpos($p[1]," erro")==true){
					$color="red";
				}elseif(strpos($p[1]," EXISTE")==true){
					$color="#ccc";
				}
				echo "<tr><td align='right'>".$p[0]."=</td><td align='left'><input type='text' id='".$p[0]."' value='".$p[1]."' style='font-size:38pt; color:$color;'/></td></tr>";
				$color="";
			}
		}
	?>
	<tr><td align="left" colspan="2">Localizados: <?php echo $n; ?><br>
		<?php 
			if($_GET['cad']==1){
				?> 
				<button id="button" class="cadastrar_dados" style="font-size:38pt; color:#999; cursor:pointer">cadastrar</button>
				<?php 
			}
		?>
	</td></tr>
	<tr><td align="center" colspan="2"><div id='return_html'></div></td></tr>
</table>

