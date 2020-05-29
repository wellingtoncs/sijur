<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=UTF-8'>
<title>Consulta de Ve&iacute;culos Apreendidos no DETRAN/PE</title>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<script type='text/javascript' src="../js/jquery-1.9.1.js"></script>
<script type='text/javascript' src="../js/jquery-ui.js"></script>
<script type='text/javascript' src='../jFilterXCel2003.js'></script>
<link rel="stylesheet" href="../css/style.css" />
<input type="hidden" id="txt" value="0" />
<?php 

$estado = $_GET['estado']?$_GET['estado']:'PE';

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");
$qr = mysql_query(" SELECT 
count(c.id_case)
FROM lcm_case AS c
WHERE c.notes <> '' 
AND c.`status` = 'open'
AND year(c.date_creation) = '2015'
AND c.id_case not in (SELECT f.id_case FROM lcm_followup AS f WHERE f.`type` = 'followups45' )
AND (c.legal_reason like '%BUSCA%' OR c.legal_reason like '%REINTEGRA%' ) 
AND replace((SELECT replace(SUBSTR(cp.notes,LOCATE('Placa',cp.notes)+6,((LOCATE('Renavam',cp.notes))-(LOCATE('Placa',cp.notes)+7))),'-','') FROM lcm_case AS cp WHERE LOCATE('Placa',cp.notes) <> '' AND cp.id_case=c.id_case),' ','') <> '' 
AND CHARACTER_LENGTH(SUBSTR(replace(replace(replace(replace(replace(replace(replace(replace(c.notes,' ',''),':',''),'/',''),'-',''),'\r',''),'\t',''),'\n',''),'•',''),LOCATE('PLACA',replace(replace(replace(replace(replace(replace(replace(replace(c.notes,' ',''),':',''),'/',''),'-',''),'\r',''),'\t',''),'\n',''),'•',''))+5,7))=7 
AND c.state = '". $estado ."' ",$conexao1);
$wr = mysql_fetch_array($qr);
echo "<input type='hidden' id='imp_qtd' value='".$wr[0]."'>";
echo "<input type='hidden' id='estado' value='".$estado."'>";
?>
<script type='text/javascript'>
var counter = 0;
var qtd = $("#imp_qtd").val();
var est = $("#estado").val();
var stt = true;
var vtr = 0;
var psr = 0;
var avc = 0;

$(document).ready(function(){	
	getData();
});

function contador(valor){
	$("#contador").html(valor);
}

function getData() {
	if(stt==true){		
		$.ajax({
			type: "POST",
			url:  "detran.php",
			data: "flag=1&estado="+est+"&limite="+counter,
			success: function(retorno_ajax){
				$("#return_html").html(retorno_ajax);
				parseInt(counter++);
				if (counter < qtd){
					contador(counter);
					getData();
				}
				if(counter==qtd){
					window.location.reload();
				}
				var x = document.body.scrollWidth;
				var y = document.body.scrollHeight;
				window.scrollTo(x, y);
			}
		});
	}else{
		return false;
	}
}
function slides(valor){
	contador(counter);
	$("#return_html").attr("id","return_html_2");
	stt = false;
	if(valor=="voltar"){
		counter = parseInt(counter)-1;
		$("#return_html_2").attr("id","return_html");
		$("#return_html").html("<tr><td style='text-align:center'><img src='img/progress.gif' width='80px' /></td></tr>");
		$.ajax({
			type: "POST",
			url:  "detran.php",
			data: "flag=1&estado="+est+"&limite="+counter,
			success: function(retorno_ajax){
				$("#return_html").html(retorno_ajax);
			}
		});	
		$("#pausar").hide();
		$("#seguir").show();
	}else if(valor=="pausar"){
		$("#pausar").hide();
		$("#seguir").show();
	}else if(valor=="avanca"){
		counter = counter+1;
		$("#return_html_2").attr("id","return_html");
		$("#return_html").html("<tr><td style='text-align:center'><img src='img/progress.gif' width='80px' /></td></tr>");
		$.ajax({
			type: "POST",
			url:  "detran.php",
			data: "flag=1&estado="+est+"&limite="+counter,
			success: function(retorno_ajax){
				$("#return_html").html(retorno_ajax);
			}
		});
		$("#pausar").hide();
		$("#seguir").show();
	}else if(valor=="seguir"){
		$("#return_html_2").attr("id","return_html");
		stt = true;
		$("#pausar").show();
		$("#seguir").hide();
		getData();
	}
	contador(counter);
}
</script>
<body>
<table align='center' id='return_html' width='100%' height='600px' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; font-size:10pt; font-family:arial;'></table>
<table align='center' width='100%' border='0' cellspacing='1' cellpadding='1'>
	<tr>
		<td align="center">
			<button id="voltar" onclick="slides('voltar')" style="font-size:60px;"><<</button>
			<button id="pausar" onclick="slides('pausar')" style="font-size:60px;">||</button>
			<button id="seguir" onclick="slides('seguir')" style="font-size:60px;display:none"> > </button>
			<button id="avanca" onclick="slides('avanca')" style="font-size:60px;">>></button>
		</td>
	</tr>
	<tr>
		<td><div id="contador"></div></td>
	</tr>
</table>

</body>
</html>

