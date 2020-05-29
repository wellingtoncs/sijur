<?php
$qu = mysql_query("SELECT id_dados, nome_dados FROM tp_dados_tb ",$conexao1);
while($wl = mysql_fetch_array($qu))
{
	$arr_cp[$wl['id_dados']] = $wl['nome_dados'];
}
?>
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
$(document).ready(function(){
	$(".group .delete").click(function(){
		$(this).parents(".group").fadeOut('slow', function(){ $(this).remove();});
	});
});

</script>
<div align="center" class="include_arq">		
	<div align="left" id="accordion" style="width:880px;" >
		<?php	
		$tipo_tb   = $_POST['TIPOPET'] ? $_POST['TIPOPET'] : "''";
		$sel_text  = " SELECT * FROM tp_funda_tb as tf";
		$sel_text .= " JOIN tp_tipo_tb as tt on tt.tipo_id = tf.tipo_id ";
		$sel_text .= " JOIN tp_inputs_tb AS ti ON ti.tipo_id = tt.tipo_id";
		$sel_text .= " WHERE tt.tipo_id = " . $tipo_tb;
		$sel_text .= " GROUP BY tf.fund_id";
		$sel_text .= " ORDER BY tf.fund_order ASC";
	
		$que_text = mysql_query($sel_text,$conexao1);
		$n=0;
		while($wtext = mysql_fetch_array($que_text))
		{
			?>
			<input type="hidden" class="fund_id" value="<?php echo $wtext['fund_id']; ?>" >
			<div class="group">
				<h3><a href="#" style="cursor: move;" ><?php echo $wtext['fund_titulo']; ?></a><img src="css/images/closeButton.png" alt="delete" class="delete" /></h3>
				<div align="center">
					<textarea id="cls_text_<?php echo $n; ?>" name="cls_text_<?php echo $n; ?>" class="cls_text" style="width:690px;" >
						<?php
							$para_text = $wtext['fund_text'];								
								//Pegando o valor dos names do POST
								foreach($_POST as $obj => $val)
								{
									//Definindo o valor do name (se existir)
									
									//Definindo quanto a marcação '@CAMPO@' for maiúscula
									if(strpos($para_text, "@" . strtoupper($obj) . "@") != false)
									{
										$para_text = str_replace("@" . strtoupper($obj) . "@",($arr_cp[$val] ? $arr_cp[strtoupper($val)] : strtoupper($val)),$para_text);
									}
									//Definindo quanto a marcação '@Campo@' for a primeira letra maiúscula
									elseif(strpos($para_text, "@" . upwords(convertemin($obj)) . "@") != false)
									{											
										$para_text = str_replace("@" . upwords(convertemin($obj)) . "@",($arr_cp[$val] ? $arr_cp[upwords(convertemin($val))] : upwords(convertemin($val))),$para_text);
									}
									//Definindo quanto a marcação '@campo@' mesmo tamanho
									else
									{
										$para_text = str_replace("@$obj@",($arr_cp[$val] ? $arr_cp[$val] : $val),$para_text);
									}										
								}	
								
							echo str_replace(", ,",",",str_replace(", , ,",", ,",$para_text));
						?>
					</textarea>
				</div>
			</div>
			<?php
			$n++;
		}
		?>
	</div>
	<div align="center" id="bottomSpace" style="width:880px;"></div>
	<script language="javascript">
		
		$(function() {
			
			var config = {
				sharedSpaces :
				{
					top : 'topSpace',
					bottom : 'bottomSpace'
				},
				skin : 'v2',
				removePlugins : 'maximize,resize',
				extraPlugins : 'autogrow',
				removePlugins : 'resize',
				scayt_autoStartup : true,
				scayt_sLang : "pt_BR",
				language: 'pt_BR',
				contentsCss : 'css/texto.css',
				toolbar:
				[
					{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike' ] },
					{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
					{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print' ] },
					{ name: 'styles', items : [ 'Format','Font','FontSize' ] },
					{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
					{ name: 'editing', items : [ 'SpellChecker', 'Scayt', 'myplugin' ] }
				]
			};
			$('.cls_text').ckeditor(config);
		});	
		
	</script>
	<div align="center">
		<br/>
		<!--button type="button" onclick="javascript:history.back();" class="input-default" style="height:25px;">Voltar</button>
		<button type="button" onclick="gerar_texto();" 	class="input-default" style="height:25px;">Gerar</button-->
		<input type="submit" value="Unir Parágrafos" onclick="EnviarDados('form.php','3','');"  class="input-default" style="height: 30px; cursor:pointer;" />
	</div>
	<input type="hidden" name="tipo_id"   id="tipo_id"   value="<?php echo $tipo_tb; ?>" />
	<input type="hidden" name="name_text" id="name_text">
	<input type="hidden" name="edit_text" id="edit_text" value="<?php echo $n; ?>" />
	<input type='hidden' name='url_dir'   id='url_dir'   value='<?php echo $_POST['url_dir']; ?>' />
	<input type="hidden" name="nomecli"	  id="nomecli"   value="<?php echo $_POST['nomecli']; ?>" />
	<?php 
	$nomepet = $_POST['nomepet'];
	?>
	<input type="hidden" name="nomepet"	  id="nomepet"   value="<?php echo $_POST[$nomepet]; ?>" />
</div>
<!--Crinado Inputs dinâmicos-->
<div>
	<div id="dialog_inputs" title="Novo Input" style="display:none">
		<div style="height:140px">
			<center>
				<br/>
				<table>
					<tr height="30px">
						<td align="left" class="td_title"><b>Título  <br /><input type="text" id="IMPTITLE" style="width:220px"/></td>
					</tr>
					<tr>
						<td align="left" class="td_title"><b>NAME:   <br /><input type="text" id="IMPNAME"  style="width:220px" onkeypress="validaCaractaer(event)";/></td>
					</tr>
				</table> 
			</center>	
		</div>
	</div>
</div>