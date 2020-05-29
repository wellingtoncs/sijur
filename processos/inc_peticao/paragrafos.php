<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php

include("inc/conectar.php");
include("inc/functions.php");

$qu = mysql_query("SELECT id_dados, nome_dados FROM tp_dados_tb ",$conexao1);
while($wl = mysql_fetch_array($qu))
{
	$arr_cp[$wl['id_dados']] = $wl['nome_dados'];
}

?>
<meta 	http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<html>
<head>
	<title>Gerador de Petições</title>
	<link type="text/css" rel="stylesheet" href="css/base/jquery.ui.all.css">
	<link type="text/css" rel="stylesheet" href="css/demos.css">
	<link type="text/css" rel="stylesheet" href="css/css.css">
	<script type="text/javascript" src="js/jquery-1.7.2.js">			</script>
	<script type="text/javascript" src="js/jquery.ui.core.js">			</script>
	<script type="text/javascript" src="js/jquery.ui.widget.js">		</script>
	<script type="text/javascript" src="js/jquery.ui.mouse.js">			</script>
	<script type="text/javascript" src="js/jquery.ui.sortable.js">		</script>
	<script type="text/javascript" src="js/jquery.ui.accordion.js">		</script>
	<script type="text/javascript" src="js/jquery.ui.position.js">		</script>
	<script type="text/javascript" src="js/jquery-ui.js">				</script>
	<script type="text/javascript" src="js/jquery.ui.dialog.js">		</script>
   	<script type="text/javascript" src="ckeditor/ckeditor.js">			</script>
   	<script type="text/javascript" src="ckeditor/adapters/jquery.js">	</script>
	<script language="javascript">	
	//Demo
	$(function() {
		$( "#accordion" )
			.accordion({
				autoHeight: false,
				navigation: true,
				header: "> div > h3"
			})
			.sortable({
				axis: "y",
				handle: "h3",
				stop: function( event, ui ) {
					ui.item.children( "h3" ).triggerHandler( "focusout" );
				}
			});
	});
	$(document).ready(function(){
		$(".group .delete").click(function(){
			$(this).parents(".group").fadeOut('slow', function(){ $(this).remove();});
		});
	});
	function UnirTexto(){
		document.form.submit();
	}
	</script>
</head>
<body style="overflow-y: scroll;">
<div align="center" class="demo" style="margin-top:85px;">
	<form id="form" name="form" action="editor.php"  method="POST" >
		
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
					<h3><a href="#" style="cursor: move;" ><?php echo $wtext['fund_titulo']; ?></a><img src="closeButton.png" alt="delete" class="delete" /> </h3>
					<div align="center">
						<textarea class="inpuText" id='cls_text_<?php echo $n; ?>' name='cls_text_<?php echo $n; ?>' class='cls_text' style="width:690px;" >
							<?php
								
								$para_text = $wtext['fund_text'];								
									//Pegando o valor dos names do POST
									foreach($_POST as $obj => $val)
									{
										//Definindo o valor do name (se existir)
										if(strpos($para_text, "@" .$obj . "@") !== false)
										{
											$para_text = str_replace("@$obj@",($arr_cp[$val] ? $arr_cp[$val] : $val),$para_text);
										}
									}	
									
								echo $para_text;
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
					removePlugins : 'maximize,resize',
					extraPlugins : 'autogrow',
					removePlugins : 'resize',
					scayt_autoStartup : true,
					scayt_sLang : "pt_BR",
					language: 'pt_BR',
					contentsCss : 'css/texto.css',
					toolbar:
					[
						{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
						{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
						{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print' ] },
						{ name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
						{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
						{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
						{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
						{ name: 'colors', items : [ 'TextColor','BGColor','showtime' ] }
					]
				};
				$('.inpuText').ckeditor(config);
			});	
			
		</script>
		<table align="center" class="topo2" cellpadding="2" cellspacing="4">
			<tr>
				<td class="topo2" align="right"><center><div id="topSpace" style="width: 880px;"></div></center></td>
			</tr>
		</table>
		<div align="center">
			<br/>
			<button type="button" onclick="javascript:history.back();" class="input-default" style="height:25px;">Voltar</button>
			<button type="button" onclick="gerar_texto();" 	class="input-default" style="height:25px;">Gerar</button>
			<button type="button" onclick="UnirTexto();"  	class="input-default" style="height:25px;">Unir</button>
		</div>
		<input type="hidden" id="tipo_id" value="<?php echo $tipo_tb; ?>" >
		<input type="hidden" name="name_text" id="name_text" >
		<input type="hidden" name="edit_text" value="<?php echo $n; ?>" >
		<input type='hidden' name='url_dir' id='url_dir' value='<?php echo $_POST['url_dir']; ?>' />
		<input type="hidden" name="nomecli"	id="nomecli" value="<?php echo $_POST['nomecli']; ?>">
		
	</form>
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
</body>
</html>
