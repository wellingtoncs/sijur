<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php

include("inc/conectar.php");
include("inc/functions.php");

$qInp = mysql_query("SELECT * FROM tp_inputs_tb where tipo_id = '1' ",$conexao1);
$nu = 0;
$campos = "";
while($w = mysql_fetch_array($qInp))
{
	$campos .= $nu>0 ? "|_|" : "";
	$campos .= $w['input_title'];
	$campos .= "_|_";
	$campos .= "@CAMPO" . $w['id_input'] . "@";
	$nu++;
}

?>
<meta 	http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<html>
<head>
	<title>Gerador de Petições</title>
	<link type="text/css" rel="stylesheet" href="css/base/jquery.ui.all.css" >
	<link type="text/css" rel="stylesheet" href="css/demos.css" >
	<link type="text/css" rel="stylesheet" href="css/css.css" >
	<script type="text/javascript" src="js/jquery-1.7.2.js">		   	</script>
	<script type="text/javascript" src="js/jquery.ui.core.js">		   	</script>
	<script type="text/javascript" src="js/jquery.ui.widget.js">	   	</script>
	<script type="text/javascript" src="js/jquery.ui.mouse.js">		   	</script>
	<script type="text/javascript" src="js/jquery.ui.sortable.js">	   	</script>
	<script type="text/javascript" src="js/jquery.ui.accordion.js">	   	</script>
	<script type="text/javascript" src="js/jquery.ui.autocomplete.js">	</script>
	<script type="text/javascript" src="js/jquery.ui.button.js">		</script>
	<script type="text/javascript" src="js/jquery.ui.position.js">		</script>
	<script type="text/javascript" src="js/jquery-ui.js">				</script>
	<script type="text/javascript" src="js/jquery.ui.dialog.js">		</script>
	<script type="text/javascript" src="js/jquery.meio.mask.js">	 	</script>
   	<script type="text/javascript" src="ckeditor/ckeditor.js">			</script>
   	<script type="text/javascript" src="ckeditor/adapters/jquery.js">	</script>
	<script>
	
	var str_retorno_ajax = "<?php echo $campos; ?>";
	
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

			$("#TIPOPET").combobox();
			$('input:text').setMask();
	});	
	
	//Autocomplete
	(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var input,
					self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "",
					wrapper = $( "<span>" )
						.addClass( "ui-combobox" )
						.insertAfter( select );

				input = $( "<input>" )
					.appendTo( wrapper )
					.val( value )
					.addClass( "ui-state-default" )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
							//submeter ao selecionar
							$("#act_parag").val('0');
							document.form_fund.action="funds.php";	
							document.form_fund.submit();							
						},
						change: function( event, ui ) {
							//select.focus();
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								} 
							}
						}
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );

				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};

				$( "<a class='ui-button2'>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Todos os ítens" )
					.appendTo( wrapper )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "ui-corner-right ui-button-icon" )
					.click(function() {
						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}

						// work around a bug (likely same cause as #5265)
						$( this ).blur();

						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},

			destroy: function() {
				this.wrapper.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );
	
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
									document.form_fund.action="funds.php";
									document.form_fund.submit();
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
	
	function save_parag(id,valor){
		msgbox("<br><table align='center'><tr><td>Deseja realmente salvar esse tópico e parágrafos?</td></tr></table>", {
			Sim: function() {
				$( this ).dialog( "close" );
				$.ajax({
				   type: "POST",
				   url:  "inc/ajax_parag.php",
				   data: "flag=S" 		+ 
						 "&fund_id=" 	+ id + 
						 "&fund_text=" 	+ escape(valor),
						 
				   success: function(retorno_ajax){
						if(retorno_ajax == 'OK'){
							msgbox("<br><table align='center'><tr><td> Texto salvo com sucesso !</td></tr></table>", {
								Fechar: function() {
									$( this ).dialog( "close" );
									document.form_fund.action="funds.php";
									document.form_fund.submit();
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
										document.form_fund.action="funds.php";	
										document.form_fund.submit();
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
							 "&tipotitle=" + escape($("#TIPOTITLE").val()),
							 
					   success: function(retorno_ajax){
							if(retorno_ajax =1){
								$( "#dialog_tipo" ).dialog( "close" );
								msgbox("<br> Modelo criado com sucesso !", {
									Fechar: function(){
										$( this ).dialog( "close" );
										document.form_fund.action="funds.php";	
										document.form_fund.submit();
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
</head>
<body style="overflow-y: scroll;">
<div align="center" class="demo">
	<form id="form" name="form_fund" action="funds.php"  method="POST" >
		<div id="demo0" align="center" >
			<div class="tabela_form"  style="width:800px;margin-top:85px;">
				<div style="text-align:left; height:15px; margin:5px 5px 5px 15px "><label>PAINEL DE CONTROLE PARA MODELOS DE PETIÇÕES </label></div>
				<div align="center" border="1">
					<label>MODELO DE PETIÇÃO:		</label>
					<select type="text" id="TIPOPET" name="TIPOPET" class="input-default" ><?php echo fc_select("tp_tipo_tb",$_POST['TIPOPET'],"tipo_id","tipo_nome",$cod_usu,$conexao1); ?></select>
					<div align="right" style="float:right; margin-right:5px;">
						<button type="button" class="input-default" onclick="novo_tipo();" style="height:25px;">Novo Modelo</button>
					</div>
					<div>&nbsp;</div>
				</div>
			</div>
		</div>
		<div align="left" id="accordion" style="width:800px;" >
	
			<?php	
			$tipo_tb = $_POST['TIPOPET'] ? $_POST['TIPOPET'] : "''";
			$sel_text  = " SELECT * FROM tp_funda_tb as tf";
			$sel_text .= " JOIN tp_tipo_tb as tt on tt.tipo_id = tf.tipo_id ";
			$sel_text .= " WHERE tt.tipo_id = " . $tipo_tb . " ORDER BY tf.fund_order ASC";
			$que_text = mysql_query($sel_text,$conexao1);
			
			$obj_text = explode("_|_",$_POST['obj_text']);
			$n=0;
			while($wtext = mysql_fetch_array($que_text))
			{
				?>
				<input type='hidden' name='#dados<?php echo $n; ?>' id='#dados<?php echo $n; ?>' value='' />
				<div class="group">
					<h3>
						<a href="#" style="cursor: move;" ><?php echo $wtext['fund_titulo']; ?></a>
					</h3>
					<div align="center">
					<div align="right" style="padding:0 65px 5px 0;" >
						<button type="button" value="<?php echo $wtext['fund_id']; ?>" class="input-default" onclick="save_parag(<?php echo $wtext['fund_id']; ?>,$('#input<?php echo $n; ?>').val()) " style="height:25px">Salvar</button>&nbsp;
						<button type="button" value="<?php echo $wtext['fund_id']; ?>" class="input-default" onclick="del_parag(<?php echo $wtext['fund_id']; ?>) " style="height:25px">Excluir</button>&nbsp;
					</div>
						<textarea class="inpuText" id="input<?php echo $n; ?>" name="input<?php echo $n; ?>"><?php echo urldecode($wtext['fund_text']); ?></textarea>
					</div>
				</div>
				<?php
				$n++;
			}
			?>
		</div>
		<div align="center">
			<br/>
			<div align="center" id="bottomSpace" style="width:790px;"></div>
			<br/>
			<button type="button" class="input-default" onclick="novo_parag();" style="height:25px;">Novo Tópico</button>
			<br/>
		</div>
		<br><br>
		<input type="hidden" id="tipo_id" value="<?php echo $tipo_tb; ?>" >
		<input type="hidden" name="name_text" id="name_text" >
		<input type="hidden" name="act_parag" id="act_parag" value="<?php echo $_POST['act_parag'] ? $_POST['act_parag'] : 0; ?>" >
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
						{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','Smiley' ] },
						{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
						{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
						{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
						//{ name: 'colors', items : [ 'TextColor','BGColor','showtime','insertTab' ] }
					]
				};      
				
				$('.inpuText').ckeditor(config);   
				
				$( "#accordion" ).accordion({
					active: <?php echo $_POST['act_parag'] ? $_POST['act_parag'] : 0; ?> 
				});
			});	
		</script>
	</form>
</div>

<table align="center" class="topo2" cellpadding="2" cellspacing="4">
	<tr>
		<td class="topo2" align="right"><center><div id="topSpace" style="width: 800px;"></div></center></td>
	</tr>
</table>

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
<div id="dialog_tipo" title="Novo Modelo" style="display:none">
	<div style="height:80px">
		<center>
			<br/>
			<table>
				<tr height="30px">
					<td align="left" class="td_title"><b>Título do Modelo <br /><input type="text" id="TIPOTITLE" style="width:220px"/></td>
				</tr>
			</table> 
		</center>	
	</div>
</div>
</body>
</html>