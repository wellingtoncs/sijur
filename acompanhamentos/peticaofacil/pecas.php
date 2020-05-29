<script language="javascript">	
//Demo
$(function() {
	$( "#accordion" )
	.accordion({
		autoHeight: false,
		navigation: true,
		header: "> div > h3"
	})
});

function ajax_pecas(valor1,valor2){
	$.ajax({
	   type: "POST",
	   url:  "inc/ajax_pecas.php",
	   data: "flag=H&tipo_id=" + valor1 + "&limit="+valor2,
	   success: function(retorno_ajax){
			$("#html_pecas_"+valor1).html(retorno_ajax);
		}
	});
}
</script>
<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<div class="icon-wrapper">
				<div align="left" id="accordion" style="width:880px;" >
				<?php
				$id_setor="";
				if($usu_nivel!="ADM"){
					$id_setor = "where id_setor = '$usu_setor'";
				}
				$qTipo = mysql_query("SELECT * from tp_tipo_tb as t $id_setor ORDER by t.tipo_id ") or die(mysql_error());					
				while ($arTipo = mysql_fetch_array($qTipo))
				{
					?>
					<div class="group">
						<h3><a href="#" style="cursor: move;" onclick="ajax_pecas('<?php echo $arTipo['tipo_id']; ?>','0')" ><?php echo $arTipo['tipo_nome']; ?></a></h3>
						<div align="center" id="html_pecas_<?php echo $arTipo['tipo_id']; ?>"></div>
					</div>
					<?php
				}
				?>
					<input type="hidden" name="is_pecas" id="is_pecas" value="1"  />
					<input type="hidden" name="id_pecas" id="id_pecas" value=""   />
					<input type="hidden" name="tipo_id"  id="tipo_id"  value=""   />
					<input type="hidden" name="nomepet"  id="nomepet"  value=""   />
					<input type="hidden" name="nomecli"  id="nomecli"  value=""   />
				</div>
			</div>
		</div>
	</div>
</div>
<div id="dialog-edit-setor" title="Editar Setor" style="display:none; text-align:left;">
	<p class="validateTips">Edite o Usuário Abaixo</p>
	<fieldset>
		<div>
			<table>
				<tr>
					<td><label>Nome do Setor:</label></td>
					<td><input type="text" class="cls_setor" name="nome_setor" id="nome_setor" value="" obrigatorio="1" title="Nome do Setor"/></td>
				</tr>
			</table>
			<input type="hidden" class="cls_setor" name="id_setor" id="id_setor" value="" />
		</div>
	</fieldset>
</div>