<div class="content_body">
	<div class="cpanel-center">
		<div class="cpanel">
			<div class="icon-wrapper">
				<table class="adminlist" width="60%" align="center">
					<tr height="30">
						<td class="order" ><b>C�digo		 </b></td>
						<td class="order" ><b>Nome			 </b></td>
						<td class="order" ><b>Data Cadastro	 </b></td>
						<td class="order" ><b>Op��es         </b></td>
					</tr>
					<?php
						
					$query = mysql_query("SELECT * from tp_setor_tb as s ORDER by s.id_setor") or die(mysql_error());
					while ($arr = mysql_fetch_array($query))
					{
						?>
						<tr >
							<td class="order"><?PHP echo $arr['id_setor'];	 ?></td>
							<td class="order"><?php echo $arr['nome_setor']; ?></td>
							<td class="order"><?php echo $arr['data_cad']; 	 ?></td>
							<td class="order"><?php echo fc_botoes_setor($arr['id_setor'],"block",$arr['nome_setor']); ?></td>
						</tr>
						<?php
					}
				?>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="dialog-edit-setor" title="Editar Setor" style="display:none; text-align:left;">
	<p class="validateTips">Edite o Usu�rio Abaixo</p>
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