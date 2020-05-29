
<html>
<head>
<meta http-equiv='content-type' content='text/html; charset=ISO-8859-1'>
<title>Ajuizar</title>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<script type='text/javascript' src="../js/jquery-1.9.1.js"></script>
<script type='text/javascript' src="../js/jquery-ui.js"></script>
<script type='text/javascript' src='jFilterXCel2003.js'></script>
<link rel="stylesheet" href="../css/style.css" />
<script type='text/javascript'>
$(document).ready(function(){	
	$("#return_html").html("<img src='../img/aguarde_g.gif' />");
	$.ajax({
		type: "POST",
		url:  "index_ajax.php",
		data: "flag=",
		success: function(retorno_ajax){
			$("#return_html").html(retorno_ajax);
			carregarFiltros('tbf1');
		}
	});
});
</script>
<div id='return_html'></div>
