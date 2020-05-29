
<?php
if($_POST['hid_enviar']==5){
?>
	<div class="content_body">
		<div class="cpanel-left">
			<div class="cpanel">
				<?php fc_select_div("tp_tipo_tb",'1',"tipo_id","tipo_nome","2156","E",$conexao1); ?>
			</div>
		</div>
		<div class="cpanel-right">
			<div class="cpanel">
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" id="prg" onclick="novo_tipo()">
							<img src="css/images/header/icon-48-article-add.png" alt=""  /><span>Novo Modelo</span>
						</a>
					</div>
				</div>
				<?php 
				if($_SESSION['usuarioNivel']=="ADM"){
					?>
					<div class="icon-wrapper">
						<div class="icon">
							<a href="#" onclick="EnviarDados('form.php','8','')">
								<img src="css/images/header/icon-48-user.png" alt=""  /><span>Usuários</span>
							</a>
						</div>
					</div>
					<div class="icon-wrapper">
						<div class="icon">
							<a href="#" onclick="EnviarDados('form.php','9','')">
								<img src="css/images/header/icon-48-move.png" alt=""  /><span>Setores</span>
							</a>
						</div>
					</div>
					<?php 
				}
				?>
			</div>
		</div>
		<div class="cpanel-right-sub">
			<div class="cpanel">
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" id="frm" onclick="mark_edit(7,0)">
							<img src="css/images/header/icon-48-themes.png" alt=""  /><span>Formulário</span>
						</a>
					</div>
				</div>	
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" id="prg" onclick="mark_edit(6,0)">
							<img src="css/images/header/icon-48-article-edit.png" alt=""  /><span>Parágrafos</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" id="prg" onclick="mark_edit('',1)">
							<img src="css/images/header/icon-48-deny.png" alt=""  /><span>Excluir</span>
						</a>
					</div>
				</div>		
			</div>
		</div>	
	</div>
<?php
}
?>
<script>
function novo_tipo(){
	$( "#dialog_tipo" ).dialog({
		modal: true,
		autoOpen: true,
		close: function() {
			
		},
		buttons: {
			Salvar: function() {
				$.ajax({
				   type: "POST",
				   url:  "inc/ajax_parag.php",
				   data: "flag=T" + 
						 "&tipotitle=" + escape($("#TIPOTITLE").val()) +
						 "&tiposerve=" + escape($("#TIPOSERVE").val()) +
						 "&tipobanco=" + escape($("#TIPOBANCO").val()) +
						 "&tipousuar=" + escape($("#TIPOUSUAR").val()) +
						 "&tiposenha=" + escape($("#TIPOSENHA").val()) +
						 "&tipotable=" + escape($("#TIPOTABLE").val()) +
						 "&tipochave=" + escape($("#TIPOCHAVE").val()) +
						 "&tipoquery=" + escape($("#TIPOQUERY").val()) +
						 "&tipowhere=" + escape($("#TIPOWHERE").val()) +
						 "&tiposetor=" + escape($("#TIPOSETOR").val()) 
						 ,
				   success: function(retorno_ajax){
						if(retorno_ajax=1){
							$( "#dialog_tipo" ).dialog( "close" );
							msgbox("<br> Modelo criado com sucesso !", {
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('form.php','5','');
								}
							});
						}
					}
				});
			},
			Sair: function() {
				$( this ).dialog( "close" );
			}
		}
	
		
	});
}
</script>

<div id="dialog_tipo" title="Novo Modelo" style="display:none">
	<div style="height:320px">
		<center>
			<br/>
			<table>
				<tr height="30px">
					<td align="left" class="td_title"><b>Título do Modelo<br /><input type="text" id="TIPOTITLE" style="width:220px"/></td>
				</tr>
				<tr height="30px">
					<td align="left" class="td_title"><b>Servidor<br /><input type="text" id="TIPOSERVE" style="width:220px"/></td>
				</tr>
				<tr height="30px">
					<td align="left" class="td_title"><b>Banco de Dados<br /><input type="text" id="TIPOBANCO" style="width:220px"/></td>
				</tr>
				<tr height="30px">
					<td align="left" class="td_title"><b>Usuário <br /><input type="text" id="TIPOUSUAR" style="width:220px"/></td>
				</tr>
				<tr height="30px">
					<td align="left" class="td_title"><b>Senha<br /><input type="text" id="TIPOSENHA" style="width:220px"/></td>
				</tr>
				<tr height="30px">
					<td align="left" class="td_title"><b>Tabela<br /><input type="text" id="TIPOTABLE" style="width:220px"/></td>
				</tr>
				<tr height="30px">
					<td align="left" class="td_title"><b>Chave<br /><input type="text" id="TIPOCHAVE" style="width:220px"/></td>
				</tr>
				<div style="display:none">
					<tr height="30px" style="display:none">
						<td align="left" class="td_title"><b>Query<br /><input type="text" id="TIPOQUERY" style="width:220px"/></td>
					</tr>
					<tr height="30px" style="display:none">
						<td align="left" class="td_title"><b>Where<br /><input type="text" id="TIPOWHERE" style="width:220px"/></td>
					</tr>
				</div>
				<tr height="30px">
					<td align="left" class="td_title"><b>Setor<br />
						<select name="TIPOSETOR" id="TIPOSETOR" title="Setor" style="width:222px; height:21px; background: #e6e6e6" >
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
			</table> 
		</center>	
	</div>
</div>