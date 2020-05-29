
<html>
<head>
<meta name="viewport" content="text/html, user-scalable=no" charset="ISO-8859-1">

<title>PLACAS</title>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<script type='text/javascript' src="../js/jquery-1.9.1.js"></script>
<script type='text/javascript' src="../js/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/style.css" />
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
	</script>
<form action='index.php' method='post'>
<?php 

if($_POST['enviar'] && $_POST['placa_n']!= '' || $_POST['placa_t'] != '' ) {
?>
	<script type='text/javascript'>
	$(document).ready(function(){
		var altura = $(window).height();
		$("html").css("height",altura);
		$("#return_html").html("<img src='../img/aguarde_g.gif' style='height:300px'/>");
		$.ajax({
			type: "POST",
			url:  "index_ajax.php",
			data: "flag=&placa_t=<?php echo $_POST['placa_t']; ?>&placa_n=<?php echo $_POST['placa_n']; ?> ",
			success: function(retorno_ajax){
				//$("#return_html").html(retorno_ajax);
				//alert(retorno_ajax);
				$("#placa_t").val("");
				$("#placa_n").val("");
				if(retorno_ajax=="zero"){
					$("#numeros").hide();
					$("#return_html").html("<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:50pt; font-family:arial; width:99%'><tr><td style='color:blue'>Total de casos: 0</td></tr></table>");
					setTimeout(function(){  
						$("#numeros").show();
					}, 5000);
				} else if(retorno_ajax=="erro"){
					$("#numeros").hide();
					$("#return_html").html("<table align='center' id='tbf1' class='tbf1' align='left' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:50pt; font-family:arial; width:99%'><tr><td style='color:red'>A consulta deve conter ao menos os 4 números da placa do veículo!</td></tr></table>");
					setTimeout(function(){  
						$("#numeros").show();
					}, 5000);
				}else{
					$("#numeros").hide();
					$("#return_html").html(retorno_ajax);
				}
				
				
			}
		});
	});
	</script>
<?php 
} elseif($_POST['lista'] || $_POST['lista']!= ''){
?>
	<script type='text/javascript'>
	$(document).ready(function(){	
	
		$("#return_html").html("<img src='../img/aguarde_g.gif'  />");
		$("#placa_t").val("");
		$("#placa_n").val("");
		$.ajax({
			type: "POST",
			url:  "veiculos.php",
			data: "flag=&placa_t=<?php echo $_POST['placa_t']; ?>&placa_n=<?php echo $_POST['placa_n']; ?> ",
			success: function(retorno_ajax){
				$("#return_html").html(retorno_ajax);
				carregarFiltros('tbf1');
			}
		});
	});

	</script>
<?php 
}
?>
</head>
	<div style='width:99%; height:30%; background:#fff'>
		<table align='center' valign="top" width='99%' height="100%" border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); border-collapse:collapse; color:blue; font-size:9pt; font-weight:bold; font-family:arial'>
			<tr height="10%">
				<td align='center' colspan='2'><p style='font-size:50pt'>LOCALIZAR PLACAS - GMAC</p></td>
			</tr>
			<tr height="10%">
				<td align='left'><span style='font-size:40pt'>Letras: </span></td>
				<td align='left'><span style='font-size:40pt'>Números:</span></td>
			</tr>
			<tr height="10%">
				<td align='left' style='font-size:60pt'>	
					<input type='text' 	 name='placa'  id='placa' value='<?php echo $_POST['placa']; ?>' style='font-size:60pt; width: 85%' />&nbsp;-
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
			<button type='submit'  	name='enviar'  value='1' style='font-size:60pt;width:46%;height:25%' >Enviar</button>
			<button type='button'  	name='back'    value='1' style='font-size:60pt;width:20%;height:25%' onclick="back_n();"><</button>
	</div>
	<div id='return_html'  style="width:99%;" ></div>
<br>
</form>
</html>