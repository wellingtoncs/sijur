
//Demo
	$(function() {
		
		$('input:text').setMask();
		//Autocomplete
		$("#BANCO").combobox();
		$("#FILIAL").combobox();
		$("#TCONTRATO").combobox();
		$("#TIPOACAO").combobox();
		$("#ADVOGADO").combobox();
		$("#PUBADV").combobox();
		$("#TIPOPET").combobox();
		
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
	function EnviarDados(){
		var sim = '';
		$('.input-default').each(function(index,object) {
			if($(object).attr("obrigatorio")==1 && $(object).val()==""){

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
		if(sim==''){
			document.form_iniciais.action="paragrafos.php";	
			document.form_iniciais.submit();
		}
	}
	
	function fc_ajax_comp(tabela,campo0,campo1,input0,input1,id_ref,id_val,conex){

		$.ajax({
			type: "POST",
			url: "inc/ajax_comp.php",
			data: "flag=y" + 
				  "&tabela=" + tabela   +
			      "&campo0=" + campo0   +
			      "&campo1=" + campo1   +
			      "&id_ref=" + id_ref +
			      "&id_val=" + id_val 	+
			      "&conex="  + conex,
			success: function(x){
				var quebra=x.split("_|_");
				$("#"+input0).val(quebra[0]);
				$("#"+input1).val(quebra[1]);
			}
		});
		
	}
	
	function fc_inputs(){
		$( "#dialog_inputs" ).dialog({
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
					   data: "flag=I" 	  + 
							 "&inptitle=" + escape($("#INPTITLE").val()) 	+ 
							 "&tipopet="  + escape($("#TIPOPET").val())		+ 
							 "&db_col="	  + escape($("#db_col").val())		+ 
							 "&inputcol=" + escape($("#inputcol").val())	+ 
							 "&inputReq=" + escape($("#inputReq").val())	+ 
							 "&inptFunc=" + escape($("#inptFunc").val())	+ 
							 "&tbBase="   + escape($("#tbBase").val())		+ 
							 "&dadSel="   + $("#SELEINPUT:checked").val()	+ dadInp,
							 
					   success: function(retorno_ajax){
							if(retorno_ajax==1){
								$( "#dialog_inputs" ).dialog( "close" );
								msgbox("<br><table align='center'><tr><td> Campo criado com sucesso !</td></tr></table><br>", {
									Fechar: function(){
										$( this ).dialog( "close" );
										document.form_iniciais.action="index.php";	
										document.form_iniciais.submit();
									}
								});
							}else if(retorno_ajax==2){
								alert("Campo já existente!");
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
									document.form_iniciais.action="index.php";
									document.form_iniciais.submit();
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
			$("#tb_addSel").show();
			$("#tb_addTit").hide();
			$("#tb_addBase").show();
		}else if(valor=="TIPOINP"){
			$("#tb_addText").show();
			$("#tb_addSel").hide();
			$("#tb_addTit").hide();
			$("#tb_addBase").show();
		}else if(valor=="TIPOTIT"){
			$("#tb_addText").hide();
			$("#tb_addSel").hide();
			$("#tb_addTit").show();
			$("#tb_addBase").hide();
		}
	}
	
	function fc_edit(valor){
		if(valor=='Editar'){
			$(".button_del").show();
			$("input.cls_edit").val('Cancelar');
			$(".cls_campos").show();
			
		}
		else if(valor=='Cancelar'){
			$(".button_del").hide();
			$("input.cls_edit").val('Editar');
			$(".cls_campos").hide();
		}
	}
	
function data_extenso(valor){
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
	$("#"+valor).val(dia + " de " + meses[mes] + " de " + ano);
}
	
	 