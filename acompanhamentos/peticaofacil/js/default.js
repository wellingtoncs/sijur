var rand = 0;
var config = {
	sharedSpaces :
	{
		top : 'topSpace',
		bottom : 'bottomSpace'
	},
	skin:'v2',
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
		{ name: 'colors', items : [ 'TextColor','BGColor','showtime', 'myplugin' ] }
	]
};
//Demo
	$(function() {
		
		$('input:text').setMask();
		//Autocomplete
		$("#BANCO").combobox({ source: "handler.ashx" });
		$("#FILIAL").combobox({ source: "handler.ashx" });
		$("#TCONTRATO").combobox({ source: "handler.ashx" });
		$("#TIPOACAO").combobox({ source: "handler.ashx" });
		$("#ADVOGADO").combobox({ source: "handler.ashx" });
		$("#PUBADV").combobox({ source: "handler.ashx" });
		//$("#TIPOPET").combobox();
		$("#sel_chave").combobox();
		
		//$('select').each... Para ativar as funções dos selects
		$('select').each(function(index,object) {
			$(object).load();
		});
		//$('select').each... Para ativar as funções dos selects
		$('input:text').each(function(index,object) {
			if($(this).attr("carregar")!=0){
				$(object).focus();
			}
			$(object).load();
		});
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
							//Saindo do campo para atualizar
							$(this).blur();
							select.focus();
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
	
	var cls_text = "";
	var fund_id = "";
	
//////////////////////////////////////////////

//Demo2
	function EnviarDados(form,hid,pet){
			$("#hid_enviar").val(hid);
			$("#TIPOPET").val(pet);
			
			var sim = '';
			if(hid==2){		
				$('.input-default').each(function(index,object) {
					if($(object).attr("obrigatorio")==2 && $(object).val()==""){

						msgbox("<br><table align='center'><tr><td> Campo <b>" + $(object).attr("descricao") + "</b> é obrigatório</td></tr></table>", {
							Fechar: function() {
								$( this ).dialog( "close" );
								$(object).focus();
							}
						});
						sim = 1;
						return false;
					}
				});
			}
			
			if(sim==''){
				document.form_iniciais.action=form;	
				document.form_iniciais.submit();
			}
	}
	
	function PetiDados(valor1,valor2,valor3,valor4,valor5,valor6){
		
		$("#id_pecas").val(valor3);
		 $("#tipo_id").val(valor4);
		 $("#nomepet").val(valor5);
		 $("#nomecli").val(valor6);

		EnviarDados(valor1,valor2,valor4);
	}
	
	function mark_edit(valor1,valor2){
			
		$('.clspet').each(function(index,object) {
			if($(object).attr("grupo")==1){
				if(valor2==1){
					msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja deletar o modelo: <b>" + $(object).text() + "</b> ?</td></tr></table><br>",{
						"Sim": function(){
							$.ajax({
								type: "POST",
								url:  "inc/ajax_parag.php",
								data: "flag=DT&tipoid=" + $(object).attr("numpet"),
								success: function(retorno_ajax){
									$( this ).dialog( "close" );
									if(retorno_ajax=="OK"){
										msgbox("<br><table align='center'><tr><td>Modelo deletado com sucesso !</td></tr></table><br>",{
											Fechar: function(){
												$( this ).dialog( "close" );
												EnviarDados('form.php','5','');
											}
										});
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
									}
								}
							});
						},
						"Não": function(){
							$( this ).dialog( "close" );
						}
					});
					
				} else {	
					EnviarDados('form.php',valor1,$(object).attr("numpet"));
				}
			}
		});
	}
	function mark_active(valor){
		
		$('.clspet').each(function(index,object) {
			if($(object).attr("numpet")==$(valor).attr("numpet")){
				if($(object).attr("grupo")==0){
					$(object).attr("grupo",1);
					$(".cpanel-right-sub").show();				
					mark_css(object,1);
				}else{
					$(object).attr("grupo",0);
					mark_css(object,0);
					$(".cpanel-right-sub").hide();
				}
			} else {
				$(object).attr("grupo",0);
				mark_css(object,0);
			}
			
		});
		
	}
	
	function mark_css(valor,valor2){
		if(valor2==1){
		$(valor).css("background-position", 0);
			$(valor).css("-webkit-border-bottom-left-radius","50% 20px");
			$(valor).css("-moz-border-radius-bottomleft","50% 20px");
			$(valor).css("border-bottom-left-radius","50% 20px");
			$(valor).css("-webkit-box-shadow","-5px 10px 15px rgba(0, 0, 0, 0.25)");
			$(valor).css("-moz-box-shadow","-5px 10px 15px rgba(0, 0, 0, 0.25)");
			$(valor).css("box-shadow","-5px 10px 15px rgba(0, 0, 0, 0.25)");
			$(valor).css("position","relative");
			$(valor).css("z-index","10");
		} else {
			$(valor).css("background-color"," #fff");
			$(valor).css("background-position"," -30px");
			$(valor).css("display"," block");
			$(valor).css("float"," left");
			$(valor).css("height"," 97px");
			$(valor).css("width"," 108px");
			$(valor).css("color"," #565656");
			$(valor).css("vertical-align"," middle");
			$(valor).css("text-decoration"," none");
			$(valor).css("border"," 1px solid #CCC");
			$(valor).css("-webkit-border-radius"," 5px");
			$(valor).css("-moz-border-radius"," 5px");
			$(valor).css("border-radius"," 5px");
			$(valor).css("-webkit-transition-property","background-position,-webkit-border-bottom-left-radius,-webkit-box-shadow");
			$(valor).css("-moz-transition-property","background-position,-moz-border-radius-bottomleft,-moz-box-shadow");
			$(valor).css("-webkit-transition-duration","0.8s");
			$(valor).css("-moz-transition-duration","0.8s");
			$(valor).css("box-shadow","");
			$(valor).css("-webkit-box-shadow","");
			$(valor).css("-moz-box-shadow","");
			$(valor).css("box-shadow","");
		}
	}
	
	function fc_ajax_comp(tabela,campo0,input0,unir,id_ref,id_val,conex){
		var str = "";
		
		$(id_val).each(function(){
			str = $(this).find('option:selected').attr('ident');
		});
		
		$.ajax({
			type: "POST",
			url : "inc/ajax_comp.php",
			data: "flag=y" + 
				  "&tabela=" + tabela +
			      "&campo0=" + campo0 +
			      "&id_ref=" + id_ref +
			      "&id_val=" + str	  +
			      "&conex="  + conex,
				  
			success: function(x){
			
				var quebra="";
				var iSinput="";
				var iSunir="";
				var a = "";
				var b = "";
				quebra=x.split("_|_");
				iSinput=input0.split("|_|");
				for(a in quebra){
				
					if(quebra[a] && unir != 'unir'){
						$("#"+iSinput[a]).val(quebra[a]);
					} else {
						//iSunir += (quebra[a] ? quebra[a] + ', ' : '');
						iSunir += (quebra[a] ? quebra[a] + '' : '');
					}
				}
				if(unir=='unir'){
					for(b in iSinput){
						$("#"+iSinput[b]).val(iSunir);
						//$("#"+iSinput[b]).val($("#"+iSinput[b]).val().replace(", , undefined, ",""));
						$("#"+iSinput[b]).val($("#"+iSinput[b]).val().replace(", , ",""));
					}
				}
			}
		});
	}
	
	//Valor1 = I p/ Novo Campo OU valor1= E p/Editar Campo 
	function fc_inputs(valor1,valor2){
	var campoId="";
	valor1=="E"?campoId=valor2:"";
		$( "#dialog_inputs" ).dialog({
			
			title: valor1=="I"?"Novo Campo":valor1=="E"?"Editar Campo": "",
			modal: true,
			autoOpen: true,
			height: 440,
			width: 450,
			close: function() {
				
			},
			buttons: {
				Salvar: function() {
					var dadInp = '';
					var dadI = '';
					if($("#SELEINPUT:checked").val()=='TIPOINP'){
						dadInp = "&inpcheck=" + $("#INPCHECK:checked").val();
					}else if($("#SELEINPUT:checked").val()=='TIPOSEL'){
						$('.slInputs').each(function() {
							dadI += $(this).val() + "_|_";
						});
						dadInp = "&dadI=" + dadI;
					}
					
					$.ajax({
					   type: "POST",
					   url:  "inc/ajax_input.php",
					   data: "flag=" 	  + valor1 +
							 "&inptitle=" + escape($("#INPTITLE").val()) 	+ 
							 "&tipopet="  + escape($("#TIPOPET").val())		+ 
							 "&db_col="	  + escape($("#db_col").val())		+ 
							 "&inputcol=" + escape($("#inputcol").val())	+ 
							 "&inputReq=" + escape($("#inputReq").val())	+ 
							 "&inptFunc=" + escape($("#inptFunc").val())	+ 
							 "&tbBase="   + escape($("#tbBase").val())		+ 
							 "&dadSel="   + $("#SELEINPUT:checked").val()	+ dadInp +
							 "&campoId="  + campoId,
					   success: function(retorno_ajax){
							if(retorno_ajax==1){
								$( "#dialog_inputs" ).dialog( "close" );
								msgbox(valor1=="I"?"<br><table align='center'><tr><td>Campo criado com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
									Fechar: function(){
										$( this ).dialog( "close" );
										EnviarDados('form.php','7',$('#TIPOPET').val());
									}
								});
							}else if(retorno_ajax==2){
								alert("Campo já existente!");
							}else{
								alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
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
	//função editar usuário
	function fc_edit_usu(valor1,valor2){
		
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Novo Usuário";
			tu="criado";
			$(".validateTips").text("Crie Um " + tt);
		}else if(valor2=="U"){
			tt="Editar Usuário";
			tu="editado";
			$(".validateTips").text("Edite o Usuário Abaixo");
		}
	
		$.ajax({
			type: "POST",
			url:  "inc/ajax_usu.php",
			data: "flag=E&id_usu=" + valor1,
			success: function(retorno_ajax){
				var ret = retorno_ajax.split("-|-");
				//alert(ret[1]);
				$("#id_usu").val(ret[0]);
				$("#nome_usu").val(ret[1]);
				$("#login_usu").val(ret[2]);
				$("#email_usu").val(ret[4]);
				$("#nivel_usu").val(ret[5]);
				$("#status_usu").val(ret[9]);
				$( "#dialog-edit-usu" ).dialog({
					title: tt,
					modal: true,
					autoOpen: true,
					height: 440,
					width: 450,
					close: function(){ 
						$('.cls_usu').each(function() {
							$(this).val("");
						});
					},
					buttons: {
						Salvar: function() {
							var mdados="";
							$('.cls_usu').each(function(){
								if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
									alert("O campo " + $(this).attr("title") + " é obrigatório ");
									$(this).focus();
									return false;
								}
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							//alert(mdados);
							fc_teste_senha($("#senha_usu1").val(),$("#senha_usu2").val(),valor2);
							validaEmail($("#email_usu").val());									
							
							$.ajax({
							   type: "POST",
							   url:  "inc/ajax_usu.php",
							   data: "flag=" + valor2 + "&" + mdados,
							   success: function(retorno_ajax){
									if(retorno_ajax==1){
										$( "#dialog-edit-usu" ).dialog( "close" );
										msgbox(valor1=="I"?"<br><table align='center'><tr><td>Usuário " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												EnviarDados('form.php','8','');
											}
										});
									}else if(retorno_ajax==2){
										alert("Usuário já cadastrado!");
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
									}
								}
							});
							
						},
						Sair: function() {
							$( this ).dialog( "close" );
							$('.cls_usu').each(function() {
								$(this).val("");
							});
						}
					}
				});
				
				//alert($("#nivel_usu").find("option[value='USU']").attr("selected","selected"));
			}
		});
	}
	//função editar setores
	function fc_edit_setor(valor1,valor2){
		
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Novo Setor";
			tu="criado";
			$(".validateTips").text("Crie Um " + tt);
		}else if(valor2=="U"){
			tt="Editar Setor";
			tu="editado";
			$(".validateTips").text("Edite o Setor Abaixo");
		}
	
		$.ajax({
			type: "POST",
			url:  "inc/ajax_setor.php",
			data: "flag=E&id_setor=" + valor1,
			success: function(retorno_ajax){
				var ret = retorno_ajax.split("-|-");
				//alert(ret[1]);
				$("#id_setor").val(ret[0]);
				$("#nome_setor").val(ret[1]);
				
				$( "#dialog-edit-setor" ).dialog({
					title: tt,
					modal: true,
					autoOpen: true,
					height: 440,
					width: 450,
					close: function(){ 
						$('.cls_setor').each(function() {
							$(this).val("");
						});
					},
					buttons: {
						Salvar: function() {
							var mdados="";
							$('.cls_setor').each(function(){
								if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
									alert("O campo " + $(this).attr("title") + " é obrigatório ");
									$(this).focus();
									return false;
								}
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							
							$.ajax({
							   type: "POST",
							   url:  "inc/ajax_setor.php",
							   data: "flag=" + valor2 + "&" + mdados,
							   success: function(retorno_ajax){
									if(retorno_ajax==1){
										$( "#dialog-edit-setor" ).dialog( "close" );
										msgbox(valor2=="I"?"<br><table align='center'><tr><td>Setor " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												EnviarDados('form.php','9','');
											}
										});
									}else if(retorno_ajax==2){
										alert("Setor já cadastrado!");
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
									}
								}
							});
							
						},
						Sair: function() {
							$( this ).dialog( "close" );
							$('.cls_setor').each(function() {
								$(this).val("");
							});
						}
					}
				});
				
				//alert($("#nivel_usu").find("option[value='USU']").attr("selected","selected"));
			}
		});
	}
	
	function fc_del_usu(valor1,valor2){
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o usuário <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  "inc/ajax_usu.php",
					data: "flag=D&id_usu=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Usuário deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('form.php','8','');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
						}
					}
				});
				//EnviarDados('form.php','8','');
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
	}
	
	function fc_del_setor(valor1,valor2){
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o setor <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  "inc/ajax_setor.php",
					data: "flag=D&id_setor=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Setor deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('form.php','9','');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
						}
					}
				});
				//EnviarDados('form.php','8','');
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
	}
	//Abre o editor de texto ao entrar no campo que for input_tipo=TEXTEAREA
	function fc_textarea(valor,texto)
	{
		rand = parseInt(rand) + 1;
		var $dialog = $('<div></div>')
			.html(
				"<textarea id='id_text_"+rand+"'>" + valor.value + "</textarea>"
				)
			.dialog({
				position: ["50%",20],
				width: "600px",
				modal: true,
				autoOpen: true,
				buttons: {
					Sim: function() {
						$( this ).dialog( "close" );
						$('#'+valor.id).val($('#id_text_'+rand).val());
					},	
					"Não": function() {
						$( this ).dialog( "close" );
					}},
				title: texto
			});
		$('#id_text_'+rand).ckeditor(config);
	}
	
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
	
	function fc_del_input(valor){
		msgbox("<br><table align='center'><tr><td>Deseja realmente excluir esse campo?</td></tr></table>", {
			Sim: function() {
				$( this ).dialog( "close" );
				$.ajax({
				   type: "POST",
				   url:  "inc/ajax_input.php",
				   data: "flag=D" + 
						 "&idvalor=" + valor,
				   success: function(retorno_ajax){
						if(retorno_ajax ==1){
							msgbox("<br><table align='center'><tr><td> Campo excluir com sucesso !</td></tr></table>", {
								Fechar: function() {
									$( this ).dialog( "close" );
									EnviarDados('form.php','7',$('#TIPOPET').val());
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
	
	function cpfcnpj(valor){
		$("#"+valor).attr("alt",$("input[@TIPOPES=radioGroup]:checked").val());
		$('input:text').setMask();
	}
	
	function validaCaractaer(pEvent){
		if(navigator.appName.indexOf('Internet Explorer')>0){
			if ((pEvent.keyCode<97 || pEvent.keyCode>122)&&(pEvent.keyCode<48 || pEvent.keyCode>57)){
				alert("Caractere não aceito para esse campo");
				pEvent.keyCode = 0;
			}
		}else{
			if ((pEvent.which<97 || pEvent.which>122)&&(pEvent.which<48 || pEvent.which>57)) {	
				alert("Caractere não aceito para esse campo");
				pEvent.which = 0;
			}
		}
	}
	
	//Inserindo Inputs
	var dh = 440;
	$(function() {
		var i = $('input').size() + 1;
		$('a.add').click(function() {
			dh = dh + 20;
			$( "#dialog_inputs" ).dialog({height:dh});
			$('<p><input type="text" class="slInputs input-default" value="Campo ' + i + '" style="width:220px"/></p>').animate({ opacity: "show" }, "slow").appendTo('#inputs');
			i++;
		});

		$('a.remove').click(function() {
			if(i > 2) {
			dh = dh - (dh>440 ? 20 : 0);
			$( "#dialog_inputs" ).dialog({height:dh});
			$('.slInputs:last').animate({opacity:"hide"}, "slow").remove();
			i--;
			
		}

		});
		
		$('a.reset').click(function() {
			dh = 440;
			$( "#dialog_inputs" ).dialog({height:dh});
			while(i > 2) {
				$('.slInputs:last').remove();
				i--;
			}
		});
		
	});
	
	function fc_optTexto(valor){
		if(valor=="TIPOSEL"){
			$("#tb_addText").hide();
			$(".tb_addSel").show();
			$("#tb_addTit").hide();
			$("#tb_addBase").show();
		}else if(valor=="TIPOINP"){
			$("#tb_addText").show();
			$(".tb_addSel").hide();
			$("#tb_addTit").hide();
			$("#tb_addBase").show();
		}else if(valor=="TIPOTIT"){
			$("#tb_addText").hide();
			$(".tb_addSel").hide();
			$("#tb_addTit").show();
			$("#tb_addBase").hide();
		}
	}
	
	function fc_edit(valor){
		if(valor=='Editar'){
			$(".button_del").show();
			$("a.cls_edit").text('Cancelar');
			$("a.cls_edit").attr('onclick','fc_edit(\"Cancelar\")');
			$(".cls_campos").show();
			
		}
		else if(valor=='Cancelar'){
			$(".button_del").hide();
			$("a.cls_edit").text('Editar');
			$("a.cls_edit").attr('onclick','fc_edit(\"Editar\")');
			$(".cls_campos").hide();
		}
	}

function data_atual(campo1){
	var currentTime = new Date()
	var month  = currentTime.getMonth() + 1;
	var month2 = month<10?"0"+month:month;
	var day    = currentTime.getDate();
	var year   = currentTime.getFullYear();

	var date = day + "/" + month2 + "/" + year;
	return $(campo1).val(date);
}

function data_extenso_out(campo1){

var retorno = "";
var dt 		= "";
var iSdata 	= "";
var str = campo1;

	if(str.value.length==10){
		
		dt=$('#'+campo1.id).val();	
		iSdata=dt.split("/");
		//data = new Date();
		dia = iSdata[0];
		mes = iSdata[1]-1;
		ano = iSdata[2];
		meses = new Array(12);
		meses[0] = "Janeiro";
		meses[1] = "Fevereiro";
		meses[2] = "Março";
		meses[3] = "Abril";
		meses[4] = "Maio";
		meses[5] = "Junho";
		meses[6] = "Julho";
		meses[7] = "Agosto";
		meses[8] = "Setembro";
		meses[9] = "Outubro";
		meses[10] = "Novembro";
		meses[11] = "Dezembro";
		
		retorno = dia + " de " + meses[mes] + " de " + ano;
		$("#"+campo1.id).val(retorno);
		$("#"+campo1.id).attr('alt','');
		
		return false;
	}
}
function data_extenso_cur(valor,cidade){
	data = new Date();
	dia = data.getDate();
	mes = data.getMonth();
	ano = data.getFullYear();
	meses = new Array(12);
	meses[0] = "Janeiro";
	meses[1] = "Fevereiro";
	meses[2] = "Março";
	meses[3] = "Abril";
	meses[4] = "Maio";
	meses[5] = "Junho";
	meses[6] = "Julho";
	meses[7] = "Agosto";
	meses[8] = "Setembro";
	meses[9] = "Outubro";
	meses[10] = "Novembro";
	meses[11] = "Dezembro";
	$("#"+valor).val(cidade + ', ' + dia + " de " + meses[mes] + " de " + ano);
}
//Função Valor por extenso
String.prototype.extenso = function(c){
	var ex = [
		["zero", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove", "dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezessete", "dezoito", "dezenove"],
		["dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"],
		["cem", "cento", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"],
		["mil", "milhão", "bilhão", "trilhão", "quadrilhão", "quintilhão", "sextilhão", "setilhão", "octilhão", "nonilhão", "decilhão", "undecilhão", "dodecilhão", "tredecilhão", "quatrodecilhão", "quindecilhão", "sedecilhão", "septendecilhão", "octencilhão", "nonencilhão"]
	];
	var a, n, v, i, n = this.replace(c ? /[^,\d]/g : /\D/g, "").split(","), e = " e ", $ = "real", d = "centavo", sl;
	for(var f = n.length - 1, l, j = -1, r = [], s = [], t = ""; ++j <= f; s = []){
		j && (n[j] = (("." + n[j]) * 1).toFixed(2).slice(2));
		if(!(a = (v = n[j]).slice((l = v.length) % 3).match(/\d{3}/g), v = l % 3 ? [v.slice(0, l % 3)] : [], v = a ? v.concat(a) : v).length) continue;
		for(a = -1, l = v.length; ++a < l; t = ""){
			if(!(i = v[a] * 1)) continue;
			i % 100 < 20 && (t += ex[0][i % 100]) ||
			i % 100 + 1 && (t += ex[1][(i % 100 / 10 >> 0) - 1] + (i % 10 ? e + ex[0][i % 10] : ""));
			s.push((i < 100 ? t : !(i % 100) ? ex[2][i == 100 ? 0 : i / 100 >> 0] : (ex[2][i / 100 >> 0] + e + t)) +
			((t = l - a - 2) > -1 ? " " + (i > 1 && t > 0 ? ex[3][t].replace("ão", "ões") : ex[3][t]) : ""));
		}
		a = ((sl = s.length) > 1 ? (a = s.pop(), s.join(" ") + e + a) : s.join("") || ((!j && (n[j + 1] * 1 > 0) || r.length) ? "" : ex[0][0]));
		a && r.push(a + (c ? (" " + (v.join("") * 1 > 1 ? j ? d + "s" : (/0{6,}$/.test(n[0]) ? "de " : "") + $.replace("l", "is") : j ? d : $)) : ""));
	}
	return r.join(e);
}
//Função 'fc_newstring', valor por extenso à ser colocado no 'onblur' do input
function fc_newstring(valor){
	var resUlt = "";
	var resIds = "";
	var resVal = "";
	resIds=valor.id;
	resVal=valor.value;
	if($.isNumeric(resVal.replace(",",""))==true){
		resUlt = new String(resVal).extenso(true);
		$("#"+resIds).val(" ");
		return $("#"+resIds).val(resVal + " (" + resUlt + ")");
	}
}
function fc_verjuizo(valor1,valor2){
	var v = "";
	v = valor2.split(" ");
	if(valor1.value!=''){
		if(v[0]=='JUIZADO' || v[0]=='Juizado' ||  v[0]=='CARTÓRIO'){
			return valor1.value=(valor1.value.replace("º","")).replace("ª","") + 'º';
		} else if(v[0]=='VARA' || v[0]=='Vara'){
			return valor1.value=(valor1.value.replace("º","")).replace("ª","") + 'ª';
		} else {
			return valor1.value=(valor1.value.replace("º","")).replace("ª","") + 'º';
		}
	}
}

function fc_removeCapital(valor){
	return valor.value = valor.value.replace(" DA CAPITAL","");
}

function fc_teste_senha(valor1,valor2,valor3){
	if(valor1!=valor2){
		alert("Senhas não são iguais");
		return false;
	}else if(valor1=="" && valor3=="I"){
		alert("Informe sua senha!");
		return false;
	}else{
		if((valor1!="" && valor3=="U" && valor1.length<4) || (valor1.length<4 && valor3=="I")){
			alert("Sua senha deve conter no mínimo 4 caracteres!");
		}else{
			
			var er = RegExp(/[A-Za-z0-9_\-\.]{4,}/);
			if((er.test(valor1)==false && valor1!="" && valor3=="U") || (er.test(valor1)==false && valor3=="I"))
			{
				alert("Senha contém caractere inválido!");
				return false;
			}
		}
	}
}

function validaEmail(mail){

	var er = RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);

	if(mail == "")
	{
		alert("Informe seu e-mail!");
	}
	else if(er.test(mail) == false)
	{
		alert("E-mail inválido!");
	}
}