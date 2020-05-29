<table style="margin-top:80px" class="adminlist">
	<tr height="30">
		<td class="order" ><b>Código		 </b></td>
		<td class="order" ><b>Nome			 </b></td>
		<td class="order" ><b>Usuário		 </b></td>
		<td class="order" ><b>Nível			 </b></td>
		<td class="order" ><b>Último Acesso	 </b></td>
		<td class="order" ><b>Data Cadastro	 </b></td>
		<td class="order" ><b>E-mail		 </b></td>
		<td class="order" ><b>Status		 </b></td>
		<td class="order" ><b>Opções         </b></td>
	</tr>
<?php
	
$query = mysql_query("SELECT * from tp_usu_tb as u ORDER by u.id_usu") or die(mysql_error());
while ($arr = mysql_fetch_array($query))
{
	$acesso = $arr['acesso_usu']=="0000-00-00 00:00:00"?"":strftime("%d/%m/%Y %H:%M:%S", strtotime($arr['acesso_usu']));
	?>
	<tr >
		<td class="order"><?PHP echo $arr['id_usu'];		?>	</td>
		<td class="order"><?php echo $arr['nome_usu'];		?>	</td>
		<td class="order"><?php echo $arr['login_usu']; 	?>	</td>
		<td class="order"><?php echo $arr['nivel_usu'];		?>	</td>
		<td class="order"><?php echo $acesso;  ?>	</td>
		<td class="order"><?php echo strftime("%d/%m/%Y %H:%M:%S", strtotime($arr['data_cad'])); 	?>	</td>
		<td class="order"><?php echo $arr['email_usu']; 	?>	</td>
		<td class="order"><?php echo $arr['status_usu']; 	?>	</td>
		<td class="order"><?php echo fc_botoes_usu($arr['id_usu'],"block",$arr['login_usu']); ?></td>
	</tr>
	<?php
}
?>
</table>
<div id="dialog-edit-usu" title="Editar Usuário" style="display:none; text-align:left;">
	<?php 
	if($_GET['edit_status']=="")
	{
		$cad_msg = "Para manter a senha, deixe em branco.";
	}
	if($_GET['edit_status']=="1")
	{
		$cad_msg = '<font color="red">Alteração realizado com sucesso!</font>';
	}
	if($_GET['edit_status']=="3")
	{
		$cad_msg = '<font color="red">Repita a senha corretamente!</font>';
	}
	if($_GET['edit_status']=="5")
	{
		$cad_msg = '<font color="red">Nome e Usuário é obrigatório!</font>';
	}
	?>
	<p class="validateTips">Edite o Usuário Abaixo</p>
	<fieldset>
		<div>
			<table>
				<tr>
					<td><label>Nome:</label></td>
					<td><input type="text" class="cls_usu" name="nome_usu" id="nome_usu" value="" obrigatorio="1" title="Nome e Sobrenome"/></td>
				</tr>
				<tr>
					<td><label>Usuário:</label></td>
					<td><input type="text" class="cls_usu" name="login_usu" id="login_usu"  value="" obrigatorio="1" title="Usuário"/></td>
				</tr>
				<tr>
					<td><label>E-mail:</label></td>
					<td><input type="text" class="cls_usu" name="email_usu" id="email_usu" value="" obrigatorio="1" title="E-mail"/></td>
				</tr>
				<tr>
					<td><label>Nível:</label></td>
					<td>
						<select class="cls_usu" name="nivel_usu" id="nivel_usu" obrigatorio="1" title="Nivel">
							<option value="">  </option>                                       
							<option value="ADM"> Admin </option>
							<option value="GER"> Gerente</option>
							<option value="USU"> Usuário</option> 
						</select>
					</td>
				</tr>
				<tr>
					<td><label>Setor:</label></td>
					<td>
						<select class="cls_usu" name="setor_usu" id="setor_usu" obrigatorio="1" title="Setor">
							<option value="">  </option>                                       
						<?php 
						$qsetor = mysql_query("SELECT * FROM tp_setor_tb");
						while($wsetor = mysql_fetch_array($qsetor)){
							?>
							<option value="<?php echo $wsetor[0]; ?>"> <?php echo $wsetor[1]; ?></option>
							<?php 
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label>Status </label></td>
					<td>
						<select class="cls_usu" name="status_usu" id="status_usu" obrigatorio="1" title="Status" >
							<option value=""></option> 
							<option value="ATI">Ativo </option> 
							<option value="INA">Inativo</option> 
						</select>
					</td>
				</tr>
				<tr>
					<td><label>Senha </label></td>
					<td><input type="password" class="cls_usu" name="senha_usu1" id="senha_usu1" value="" /></td>
				</tr>
				<tr>
					<td><label>Repete a Senha</label></td>
					<td><input type="password" class="cls_usu" name="senha_usu2" id="senha_usu2" value="" /></td>
				</tr>
			</table>
			<input type="hidden" class="cls_usu" name="id_usu" id="id_usu" value="" />
		</div>
	</fieldset>
</div>