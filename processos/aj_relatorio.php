<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.cliente/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="inc/jquery.js"></script>
<script language="javascript">
	function fc_cad_dados()
	{		
		dados = "&id_dados=" + escape(document.form.id_dados.value);
				
	   $.ajax({
			   type: "POST",
			   url:  "aj_externo.php",
			   data: 'flag=dad' + dados,
			   success: function(retorno_ajax) {					
					if(retorno_ajax !='')
					{
						window.location = "Content-type: application/vnd.ms-excel; name='excel'";
						window.location = "Content-Disposition: filename=arquivo.xls";
						$("#result").html(retorno_ajax);
					}
			  //limpar dados
			   }
		});
	}
	$(document).ready(function()
	{
		$(".botonExcel").click(function(event) 
		{
			$("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
			$("#FormularioExportacion").submit();
		});
	});
</script>
<td width="20px">
<table name="result" id="result"></table>
	<form action="aj_externo.php" method="post" target="_blank" id="FormularioExportacion" style="margin:0px;">
		<img src="images/export/export_to_excel.png" class="botonExcel" style="cursor:pointer;" alt="Exportar" />
		<input type="hidden" name="id_dados"  value="<?php echo "select * from lcm_case limit 10" ?>">
		<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
		<input type="hidden" name="flag"  value="dad">
	</form>
</td>
<form action="" method="POST" name="form" target="" id="form" onsubmit="" style="margin:0px;" >
	<table border="0" id="Exportar_a_Excel">
		<tr>
			<td>
				<input type="button" name="Atualizar Dados" id="atualizar"  value="Atualizar Dados" onclick="fc_cad_dados();" />
			</td>
		</tr>
	</table>
</form>