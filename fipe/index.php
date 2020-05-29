<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<title>Calculadora</title>
<script type='text/javascript' src="jquery-1.9.1.js"></script>
<script type='text/javascript' src="jquery-ui.js"></script>
<link rel="stylesheet" href="jquery-ui.css" />
<link rel="stylesheet" href="style.css" />
<script type='text/javascript'>
	
function consultafipe(mydiv,link,tipo1,tipo2,tipo3,tipo4){		
	$.ajax({
		type: "POST",
		url:  link+".php",
		data: "tipo1="+tipo1+"&tipo2="+tipo2+"&tipo3="+tipo3+"&tipo4="+tipo4+"&mydiv="+mydiv,
		success: function(retorno_ajax){
			$("#"+mydiv).html(retorno_ajax);
		}
	});
}
function refresh(){
	 location.reload();
}
function calcular(){
	var hd1 = parseFloat($("#hd_mydiv1").val());
	var hd2 = parseFloat($("#hd_mydiv2").val());
	var res = hd1>hd2?(hd1-hd2):(hd2-hd1);
	$("#rest").html("Diferença de Valor: R$ "+currencyFormatDE(res));
}
function currencyFormatDE (num) {
    return num
       .toFixed(2) // always two decimal digits
       .replace(".", ",") // replace decimal point character with ,
       .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.") + " " // use . as a separator
}
</script>
<table align="left" width="100%" border="1"> 
	<tr>
		<td>Planilha Para Entrega Amigavel</td>
	<td><input type="button" value="Atualizar" onclick="refresh()" /> <input type="button" value="Calcular" onclick="calcular()" /></td>
	</tr>
</table>
<table align="left" height="200" width="50%" border="1">
	<tr>
		<td valign="top">
			<div id='mydiv1'>
				<select id="sel_fipe" onchange="consultafipe('mydiv1','marca',this.value,'','','')" >
					<option value="">Selecione o tipo</option>
					<option value="carros">Carro</option>
					<option value="caminhoes">Caminhao</option>
					<option value="motos">Moto</option>
				</select>
			</div>
		</td>
	</tr>
	
</table>
</script>
<table align="left" height="200" width="50%" border="1">
	<tr>
		<td valign="top">
			<div id='mydiv2'>
				<select id="sel_fipe" onchange="consultafipe('mydiv2','marca',this.value,'','','')" >
					<option value="">Selecione o tipo</option>
					<option value="carros">Carro</option>
					<option value="caminhoes">Caminhão</option>
					<option value="motos">Moto</option>
				</select>
			</div>
		</td>
	</tr>
</table>
<table align="left" height="20" width="100%">
	<tr>
		<td valign="top" align="right"><div id="rest" ></div>
	</tr>
</table>		
