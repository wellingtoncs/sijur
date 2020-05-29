
<html>
<head>
<meta name="viewport" content="text/html, user-scalable=no" charset="ISO-8859-1">
<title>PLACAS</title>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<script type='text/javascript' src="../js/jquery-1.9.1.js"></script>
<script type='text/javascript' src="../js/jquery-ui.js"></script>
<script type='text/javascript' src="dados.js"></script>
<link rel="stylesheet" href="../css/style.css" />
<audio id="audio">
   <source src="alert.mp3" type="audio/mp3" />
</audio>
<script>
	

	function insert_n(valor){
		//alert(valor.value);
		var num = "";
		var num2 = "";
		num = $("#placa_n").val();
		num2 = valor.value;
		$("#placa_n").val(num+num2);
	}
	function focus_t(){
		$("#placa_n").attr("type","number");
	}
	function back_n(){
		var str = $("#placa_n").val();
		if(str!=""){			
			var len = parseInt(str.length)-1;
			var res = str.substr(0, len);
			$("#placa_n").val(res);
		}
	}
	function voltar_n(){
		$("#return_html").html("");
		$("#numeros").show();
	}
	function locplaca(valor_t,valor_n){
		$("#placa_t").val("");
		$("#placa_n").val("");
		if(valor_n.length==4){
			var htmlA = "";
			var a=0;
			for (i in dados) {
				var resul = (dados[i][0]).indexOf(valor_n);
				if(resul>0){
					audio = document.getElementById('audio');
					audio.play();
					a++;
					var nn = 1;
					htmlA +=  "<div><span style='float:left'>"+a+"º </span><span style='float:right;margin-right:2%'><i>Pesquisado por: <u>"+valor_n+"</u></i> -> Placa:<b>"+dados[i][0]+"</b></span></div>"
								+"<table align='center' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:#000; font-size:36pt; font-family:arial;' >"
									+"<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Localizado o Veículo: 	</td><td style='width:65%; text-align:left'>"+dados[i][2]+"</td></tr>"
									+"<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Pasta SIJUR: 				</td><td style='width:65%; text-align:left'><a href='/processos/case_det.php?case="+dados[i][1]+"' target='_blank'>"+dados[i][1]+"</a></td></tr>"
									+"<tr style='text-align:center;'><td style='text-align:right; width:35%;'>Status: 	  				</td><td style='width:65%; text-align:left'>"+dados[i][3]+" <a href='#' id='adet_"+a+"' style='float:right' onclick='mais_det("+dados[i][1]+","+a+");'>Mais detalhes </a></td></tr>"
								+"</table><div id='mdet_"+a+"'></div><br>";
				}else{
					var mm = 0;
				}
			}
			if(nn==1){
				$("#numeros").hide();
				var tableA = "<table align='center' align='left' width='99%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:#000; font-size:36pt; font-family:arial;'>"
								+"<tr><td><button style='float:right; font-size:60pt;height:25%' onclick='voltar_n()' >Voltar</button></td></tr>"
								+"<tr><td>"
								+ htmlA
								+"</td></tr>" 
								+"<tr><td colspan='2' style='color:blue; font-size:36pt'>Total de casos: "+a+"  </td></tr>"
								+"</table>";
				$("#return_html").html(tableA);
				
			} else if(mm==0){
				$("#numeros").hide();
				$("#return_html").html("<table align='center' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:50pt; font-family:arial; width:99%'><tr><td><button style='float:right; font-size:60pt;height:25%' onclick='voltar_n()' >Voltar</button></td></tr><tr><td style='color:blue'>Nenhum resultado encontrado! Para a consulta: "+valor_t+valor_n+" </td></tr></table>");
				setTimeout(function(){   
					$("#numeros").show();
					$("#return_html").html("");
				}, 5000);
			}else{
				$("#numeros").hide();
				$("#return_html").html("<table align='center' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:50pt; font-family:arial; width:99%'><tr><td><button style='float:right; font-size:60pt;height:25%' onclick='voltar_n()' >Voltar</button></td></tr><tr><td style='color:red'>A consulta deve conter ao menos os 4 números da placa do veículo!</td></tr></table>");
				setTimeout(function(){  
					$("#numeros").show();
					$("#return_html").html("");
				}, 5000);
			}
		} else{
			$("#numeros").hide();
				$("#return_html").html("<table align='center' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:50pt; font-family:arial; width:99%'><tr><td><button style='float:right; font-size:60pt;height:25%' onclick='voltar_n()' >Voltar</button></td></tr><tr><td style='color:red'>A consulta deve conter ao menos os 4 números da placa do veículo!</td></tr></table>");
				setTimeout(function(){  
				$("#numeros").show();
				$("#return_html").html("");
			}, 5000);
		}
	}
	
	function atualizar(){
		$("#numeros").hide();
		$("#return_html").html("<img src='../img/aguarde_g.gif' style='height:150px'/>");
		$.ajax({
			type: "POST",
			url:  "ajax_new.php",
			data: "flag=att",
			success: function(retorno_ajax){
				$("#return_html").html("<table align='center' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:50pt; font-family:arial; width:99%'><tr><td><button style='float:right; font-size:60pt;height:25%' onclick='voltar_n()' >Voltar</button></td></tr><tr><td style='color:red'>"+retorno_ajax+"</td></tr></table>");
				setTimeout(function(){ 
					$("#numeros").show();
				}, 5000);
			}
		});
	}
	function mais_det(idcase,valor){
		$("#mdet_"+valor).html("<img src='../img/aguarde_g.gif' style='height:130px'/>");
		$("#adet_"+valor).hide();
		$.ajax({
			type: "POST",
			url:  "ajax_det.php",
			data: "flag=&idcase="+idcase,
			success: function(retorno_ajax){
				$("#placa_t").val("");
				$("#placa_n").val("");
				$("#numeros").hide();
				$("#mdet_"+valor).html(retorno_ajax);
			}
		});
	}
	</script>
</head>
<?php 
	$data_up = file_get_contents("data.txt");
	if($data_up==date("d/m/Y")){
		$data_up = $data_up . " (Hoje!)";
	} else {
		$att = " - <button style='font-size:30pt' onclick='atualizar()'> atualizar</button>";
	}
?>
	<div style='width:99%; height:30%; background:#fff'>
		<table align='center' valign="top" width='99%' height="100%" border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:9pt; font-weight:bold; font-family:arial'>
			<tr height="10%">
				<td align='center' colspan='2'><p style='font-size:40pt'>LOCALIZAR PLACAS - GMAC</p></td>
			</tr>
			<tr height="5%">
				<td align='center' colspan='2'><p style='font-size:30pt'>Atualizado em: <?php echo $data_up; echo $att; ?>  </p></td>
			</tr>
			<tr height="10%">
				<td align='left'><span style='font-size:40pt'>Letras: </span></td>
				<td align='left'><span style='font-size:40pt'>Números:</span></td>
			</tr>
			<tr height="10%">
				<td align='left' style='font-size:60pt'>	
					<input type='text' 	 name='placa_t'  id='placa_t' value='<?php echo $_POST['placa_t']; ?>' style='font-size:60pt; width: 85%' />&nbsp;-
				</td>
				<td align='left'>	
					<input type='text' name='placa_n'  id='placa_n' value='<?php echo $_POST['placa_n']; ?>' style='font-size:60pt; width: 90%' onfocus="focus_t();" />
				</td>
			</tr>
		</table>
	</div>
	<div id="numeros" style="width:99%; height:70%;">
		<button type='button'  	name='button1' value='1' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >1</button>
		<button type='button'  	name='button2' value='2' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >2</button>
		<button type='button'  	name='button0' value='3' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >3</button><br>
		<button type='button'  	name='button1' value='4' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >4</button>
		<button type='button'  	name='button2' value='5' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >5</button>
		<button type='button'  	name='button0' value='6' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >6</button><br>
		<button type='button'  	name='button1' value='7' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >7</button>
		<button type='button'  	name='button2' value='8' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >8</button>
		<button type='button'  	name='button0' value='9' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >9</button><br>
		<button type='button'  	name='button0' value='0' style='font-size:60pt;width:33%;height:25%' onclick="insert_n(this);" >0</button>
		<button type='button'  	name='enviar'  value='1' style='font-size:60pt;width:46%;height:25%' onclick="locplaca($('#placa_t').val(),$('#placa_n').val());" >Enviar</button>
		<button type='button'  	name='back'    value='1' style='font-size:60pt;width:20%;height:25%' onclick="back_n();"><</button>
	</div>
	<div id='return_html' style="width:99%;" ></div>
<br>
</html>