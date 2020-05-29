<table align="center" class="sub_title_tb_cfg">
	<tr>
		<td align="left">Editando os parágrafos de:&nbsp;&nbsp;<?php echo $tw[0]; ?></td>
		<td align="right"><?php echo $cntdo; ?></td>
	</tr>
</table>

<?php
$qInp = mysql_query("SELECT * FROM tp_inputs_tb where tipo_id = '" . $_POST['TIPOPET'] . "' ",$conexao1);
$nu = 0;
$campos = "";
while($w = mysql_fetch_array($qInp))
{
	$campos .= $nu>0 ? "|_|" : "";
	$campos .= $w['input_title'];
	$campos .= "_|_";
	$campos .= "@campo" . $w['id_input'] . "@";
	$nu++;
}

?>
<script language="javascript">

var str_retorno_ajax = "<?php echo $campos; ?>";

$(function() {
	$( "#accordion" )
		.accordion({
			autoHeight: false,
			navigation: true,
			header: "> div > h3"
		})
	$('input:text').setMask();
});	

$(document).ready(function(){	
	$(".group .delete").click(function(){
		//$(this).parents(".group").animate({ opacity: 'hide' }, "slow");
		$(this).parents(".group").fadeOut('slow', function(){ $(this).remove();});
		//$j("#item-"+id).fadeOut('slow', function(){ $j(this).remove(); 
	});
});

function msgbox(msg, bts){
var $dialog = $('<div></div>')
	.html(msg)
	.dialog({
		modal: true,
		autoOpen: true,
		buttons: bts,
		title: 'Alerta'
	});	
}

function del_parag(valor){
	msgbox("<br><table align='center'><tr><td>Deseja realmente deletar esse tópico?</td></tr></table>", {
		Sim: function() {
			$( this ).dialog( "close" );
			$.ajax({
			   type: "POST",
			   url:  "inc/ajax_parag.php",
			   data: "flag=D" + 
					 "&idvalor=" + valor,
					 
			   success: function(retorno_ajax){
					if(retorno_ajax =='OK'){
						msgbox("<br><table align='center'><tr><td> Input deletado com sucesso !</td></tr></table>", {
							Fechar: function() {
								$( this ).dialog( "close" );
								EnviarDados('form.php','6',$('#TIPOPET').val());
							}
						});
					}
				}
			});
		},	
		"Não": function() {
			$( this ).dialog( "close" );
		}
	});
}

function save_parag(id,valor1,valor2){

	msgbox("<br><table align='center'><tr><td>Deseja realmente salvar esse tópico e parágrafos?</td></tr></table>", {
		Sim: function() {
			$( this ).dialog( "close" );
			$.ajax({
			   type: "POST",
			   url:  "inc/ajax_parag.php",
			   data: "flag=" + valor2 + 
					 "&fund_id=" 	+ id + 
					 "&fund_text=" 	+ escape(valor1),
					 
			   success: function(retorno_ajax){
					if(retorno_ajax == 'OK'){
						msgbox("<br><table align='center'><tr><td> Texto salvo com sucesso !</td></tr></table>", {
							Fechar: function() {
								$( this ).dialog( "close" );
								//EnviarDados('form.php','6',$('#TIPOPET').val());
							}
						});
					}
				}
			});
		},	
		"Não": function() {
			$( this ).dialog( "close" );
		}
	});
	
}


function novo_parag(){
	$( "#dialog_parag" ).dialog({
		modal: true,
		autoOpen: true,
		close: function() {
			
		},
		buttons: {
			Salvar: function() {
				$.ajax({
				   type: "POST",
				   url:  "inc/ajax_parag.php",
				   data: "flag=I" + 
						 "&toptitle=" + escape($("#TOPTITLE").val()) +
						 "&tipo_id=" + escape($("#tipo_id").val()),
						 
				   success: function(retorno_ajax){
						if(retorno_ajax =1){
							$( "#dialog_parag" ).dialog( "close" );
							msgbox("<br> Tópico criado com sucesso !", {
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('form.php','6',$('#TIPOPET').val());
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
<div align="center" class="content_form_cfg">
	<div align="left" id="accordion" style="width:800px;" >
		<br>
		<?php
		$tipo_tb = $_POST['TIPOPET'] ? $_POST['TIPOPET'] : "''";
		$sel_text  = " SELECT * FROM tp_funda_tb as tf";
		$sel_text .= " JOIN tp_tipo_tb as tt on tt.tipo_id = tf.tipo_id ";
		$sel_text .= " WHERE tt.tipo_id = " . $tipo_tb . " ORDER BY tf.fund_order ASC";
		$que_text = mysql_query($sel_text,$conexao1);
		$obj_text = explode("_|_",$_POST['obj_text']);
		$n=0;
		$cod_rodap="";
		while($wtext = mysql_fetch_array($que_text))
		{
			if($n==0 && $_POST['TIPOPET']!="")
			{
				?>
				<br>
				<div class="group">
					<h3>
						<a href="#" style="cursor: move;" ><i>CABEÇALHO</i></a>
					</h3>
					<div align="center">
						<textarea class="cls_text" id="input_cabec" name="input_cabec"><?php echo $wtext['cod_cabec']; ?></textarea>
						<div align="right" style="padding:0 65px 5px 0;" >
							<button type="button" value="<?php echo $_POST['TIPOPET']; ?>" class="input-default" onclick="save_parag(<?php echo $_POST['TIPOPET']; ?>,$('#input_cabec').val(),'C')" style="height:25px">Salvar</button>
						</div>
					</div>
				</div>
				<?php
				$cod_rodap = $wtext['cod_rodap'];
			}
			?>
			<input type='hidden' name='#dados<?php echo $n; ?>' id='#dados<?php echo $n; ?>' value='' />
			<div class="group">
				<h3>
					<a href="#" style="cursor: move;" ><?php echo $wtext['fund_titulo']; ?></a>
				</h3>
				<div align="center">
					<textarea class="cls_text" id="input<?php echo $n; ?>" name="input<?php echo $n; ?>"><?php echo urldecode($wtext['fund_text']); ?></textarea>
					<div align="right" style="padding:0 65px 5px 0;" >
						<button type="button" value="<?php echo $wtext['fund_id']; ?>" class="input-default" onclick="save_parag(<?php echo $wtext['fund_id']; ?>,$('#input<?php echo $n; ?>').val(),'S') " style="height:25px">Salvar</button>&nbsp;
						<button type="button" value="<?php echo $wtext['fund_id']; ?>" class="input-default" onclick="del_parag(<?php echo $wtext['fund_id']; ?>) " style="height:25px">Excluir</button>&nbsp;
					</div>
				</div>
			</div>
			<?php
			$n++;
		}
		if($n!=0 && $_POST['TIPOPET']!="")
		{
			?>
			<div class="group">
				<h3>
					<a href="#" style="cursor: move;" ><i>RODAPÉ</i></a>
				</h3>
				<div align="center">
					<textarea class="cls_text" id="input_rodape" name="input_rodape"><?php echo $cod_rodap; ?></textarea>
					<div align="right" style="padding:0 65px 5px 0;" >
						<button type="button" value="<?php echo $_POST['TIPOPET']; ?>" class="input-default" onclick="save_parag(<?php echo $_POST['TIPOPET']; ?>,$('#input_rodape').val(),'R')" style="height:25px">Salvar</button>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<div align="center">
		<br/>
		<div align="center" id="bottomSpace" style="width:790px;"></div>
		<br/>
		<?php 
			echo $_POST['TIPOPET']!=""?"<button type='button' class='input-default' onclick='novo_parag();' style='height:25px;'>Novo Tópico</button>":"";
		?>
		<br/>
	</div>
	<br><br>
	
	<input type="hidden" id="tipo_id" value="<?php echo $tipo_tb; ?>" >
	<input type="hidden" name="name_text" id="name_text" >
	<input type="hidden" name="act_parag" id="act_parag" value="<?php echo $_POST['act_parag'] ? $_POST['act_parag'] : 0; ?>" >
	<script language="javascript">
		
		$(function() {
			
			var config = {
				language : 'pt-br',
				filebrowserBrowseUrl 		: 'ckeditor/ckfinder/ckfinder.html',
				filebrowserImageBrowseUrl 	: 'ckeditor/ckfinder/ckfinder.html?type=Images',
				filebrowserFlashBrowseUrl 	: 'ckeditor/ckfinder/ckfinder.html?type=Flash',
				filebrowserUploadUrl 		: 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',
				filebrowserImageUploadUrl 	: 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',
				filebrowserFlashUploadUrl 	: 'ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Flash',
				sharedSpaces :
				{
					top : 'topSpace',
					bottom : 'bottomSpace'
				},
				skin : 'v2',
				removePlugins : 'maximize,resize',
				extraPlugins : 'autogrow',
				extraPlugins : 'insertTab',
				removePlugins : 'resize',
				scayt_autoStartup : true,
				scayt_sLang : "pt_BR",
				//language: 'pt_BR',
				contentsCss : 'css/texto.css',
				
				toolbar:
				[
					{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
					//{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike' ] },
					{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
					//{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
					{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print' ] },
					{ name: 'tools', items : [ 'Smiley' ] },
					{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
					{ name: 'styles', items : [ 'Format','Font','FontSize' ] },
					{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
					{ name: 'editing', items : [ 'SpellChecker', 'Scayt' ] },
					{ name: 'colors', items : [ 'insertTab','Image','Table' ] }
					
				]
			};      
			
			$('.cls_text').ckeditor(config);   
			
			$( "#accordion" ).accordion({
				active: <?php echo $_POST['act_parag'] ? $_POST['act_parag'] : 0; ?> 
			});
		});	
	</script>
</div>
<div id="dialog_parag" title="Novo Tópico" style="display:none">
	<div style="height:80px">
		<center>
			<br/>
			<table>
				<tr height="30px">
					<td align="left" class="td_title"><b>Título do Tópico <br /><input type="text" id="TOPTITLE" style="width:220px"/></td>
				</tr>
			</table> 
		</center>	
	</div>
</div>