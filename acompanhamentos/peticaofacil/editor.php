<?php
$cls_text = "";
for($i=0;$i<=$_POST['edit_text'];$i++)
{
	$cls_text .= str_replace('\"','"',$_POST['cls_text_' . $i]);
}

if(isset($_POST['is_pecas'])==1){
	$query_pecas = mysql_query("SELECT cod_pecas from tp_pecas_tb where id_pecas='".$_POST['id_pecas']."' ", $conexao1) or die(mysql_error());
	$arr_pecas = mysql_fetch_array($query_pecas);
	$cls_text = $arr_pecas['cod_pecas'];
	$flag = 2;
	$id_pecas=$_POST['id_pecas'];
}else{
	$flag = 1;
	$id_pecas="";
}
?>
<script language="javascript">	
$(function (){
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
			{ name: 'editing', items : [ 'SpellChecker', 'Scayt' ] }
		]
	};
	$('#name_text').ckeditor(config);
	
	$('#scrlBotm').click(function (){
		$('html, body').animate({scrollTop: $(document).height()},1500);
		return false;
	});
	$('#scrlTop').click(function () {
		$('html, body').animate({scrollTop: '0px'},1500);
		return false;
	});
	
	$("#div_bottom").mouseover(function(){
		$("#div_bottom").fadeTo("slow", 1.0);
	});
	$("#div_bottom").mouseout(function(){
		$("#div_bottom").fadeTo("slow", 1.0);
	});
	$("#nomepet").mouseover(function(){
		$("#nomepet").val()=="Nome_do_Arquivo"?$("#nomepet").val(""):$("#nomepet").val();
	});
	$("#nomepet").mouseout(function(){
		$("#nomepet").val()==""?$("#nomepet").val("Nome_do_Arquivo"):$("#nomepet").val();
	});
});

$(document).ready(function(){

	$('#div_bottom').hide();
	
	$("body").css("color","#000000");	
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > ($("#tb_editor").height()-800)) {
				//$("#div_bottom").fadeTo("slow", 0.8);
				$('#div_bottom').fadeIn();
			} else {
				$('#div_bottom').fadeOut();
			}
		});
	});
});


function ver_title(){
	var n = 0;
	$('.titulos').each(function(index){
		var n  = parseInt(n)+1;
		$("#topicT").append("<button type='button' id='bt_" + index + "' style='background:url(\"img/topicos.png\")no-repeat;color:#ffffff;width:55px;height:20px;font-size:6pt;margin-top:1px;text-align:left;' onclick='ver_topico(" + $(this).offset().top + "," + index + ");' title='" + $(this).text() + "'>" + $(this).text().substr(0,6) +  "..</button>");
		
		$("#bt_"+ index).fadeTo("slow", 0.6);
		
		$("#bt_"+ index).mouseover(function(){
			$("#bt_"+ index).fadeTo("slow", 1.0);
		});
		
		$("#bt_"+ index).mouseout(function(){
			$("#bt_"+ index).fadeTo("slow", 0.6);
		});
		
		$("#bt_"+ index).click(function(){
			$("#bt_"+ index).css("opacity", 1);
		});
	});
}

function ver_topico(valor,nume){
	$('html,body').animate({scrollTop: (valor)-50},'slow');
	var tags = $(".titulos").length;
	tags2 = (parseInt(tags)-1);
	
	if(nume==tags2){
		$("#botao_next").hide();
	}
	if(nume<tags2){
		$("#botao_next").show();
	}
	if(nume>0){
		$("#botao_prev").show();
	}
	if(nume<1){
		$("#botao_prev").hide();
	}
	$("#id_topicos").val(parseInt(nume)+1);
}

function goToByScroll(id,num,par){
	var tags = $(".titulos").length;
	if(par==1){
		$('.titulos').each(function(index){
			alert(par);
			if(index==num){
				$('html,body').animate({scrollTop: ($(this).offset().top)-50},'slow');
			}
		});
		num = parseInt(num) + 1;
	}
	if(par==0){
		num = parseInt(num) - 1;
		$('.titulos').each(function(index){
			if(index==(parseInt(num) - 1)){
				$('html,body').animate({scrollTop: ($(this).offset().top)-50},'slow');
			}
		});
	}
	
	if(num==tags){
		$("#botao_next").hide();
	}
	if(num<tags){
		$("#botao_next").show();
	}
	if(num>0){
		$("#botao_prev").show();
	}
	if(num==1){
		$("#botao_prev").hide();
	}
	
	$("#id_topicos").val(num);
}

$(window).load(function(){
	ver_title();
	$("#ger_rtf").attr("disabled",true);
	$("#ger_pdf").attr("disabled",true);
	$("#ger_rtf").css("background","url('/peticaofacil/img/doc-c.png') no-repeat");
	$("#ger_pdf").css("background","url('/peticaofacil/img/pdf-c.png') no-repeat");
})

function fc_focus(valor){
	$("#"+valor).focus();
}
function replaceAll(string, token, newtoken) {
	while (string.indexOf(token) != -1) {
 		string = string.replace(token, newtoken);
	}
	return string;
}

function fc_salvar_pet(){
	$("#ger_sav").attr("disabled",true);	
	$("#ger_sav").css("background","url('/peticaofacil/img/progress.gif') no-repeat");
	var name_text = $("#name_text").val();
	name_text = replaceAll(name_text,"&","_|_");
	
	$.ajax({
	   type: "POST",
	   url:  "inc/getsav.php",
	   data: "flag="+ $("#id_sav").attr("flag") + "&id_pecas=" + $("#id_sav").val() +
			"&tipo_id=" 	+ $("#tipo_id").val() + 
			"&nomepet=" 	+ $("#nomepet").val() +
			"&name_text=" 	+ name_text,
			
	   success: function(retorno_ajax){
			$("#ger_sav").css("background","url('/peticaofacil/img/salvar_ok.png') no-repeat");
			$("#id_sav").val(retorno_ajax);
			$("#id_sav").attr("flag",2);
			$("#ger_sav").attr("disabled",false);
			$("#ger_rtf").attr("disabled",false);
			$("#ger_pdf").attr("disabled",false);
			$("#ger_rtf").css("background","url('/peticaofacil/img/doc.png') no-repeat");
			$("#ger_pdf").css("background","url('/peticaofacil/img/pdf.png') no-repeat");
			$("#ger_rtf").css("cursor","pointer");
			$("#ger_pdf").css("cursor","pointer");
		}
	});
}


</script>	
<div class="include_arq">
	<table id="tb_left" align="left" width="60px" height="80%">
		<tr height="30px" valign="top">
			<td>
				<a id="botao_next" href="#" onclick="goToByScroll('titulos',$('#id_topicos').val(),1);" ></a>
			</td>
		</tr>
		<tr>
			<td align="left" valign="middle">
				<div id="topicT" ></div>
			</td>					
		</tr>
		<tr height="30px" valign="bottom">
			<td>
				<a id='botao_prev' href="#" onclick="goToByScroll('titulos',$('#id_topicos').val(),0)" style="display:none" ></a>
			</td>
		</tr>
	</table>
	<table id="tb_right" align="right" width="60px" height="80%">
		<tr>
			<td align="right" valign="top">
				<a id="scrlBotm" href="#"></a>
			</td>
			<input type="hidden" id="id_topicos" value="0" />
		</tr>
		<tr>
			<td align="right" valign="bottom">
				<a id="scrlTop" href="#"></a>
			</td>
		</tr>
	</table>
	<table id="tb_editor" background="img/fundo.jpg" align="center" width="795px" style="border-bottom: 1px solid #999999; margin-top:85px">
		<tr>
			<td width="100px"></td>
			<td colspan="3" align="center" style="margin-bottom:100px;" width="600px">
				<textarea id="name_text" name="name_text" style="color:#000000; background-color: transparent !important; border: 1px solid #ccc; width: 600px; height: 800px;font-size:12pt;" ><?php echo $cls_text; ?></textarea>
				<input type="hidden" name="tipo_id" id="tipo_id" value="<?php echo $_POST['tipo_id']; ?>" />
				<textarea name='cod_cabec' id='cod_cabec' style='float:left;position:relative;margin-left:-1000px' ><?php echo cabecalhoerodape($_POST['tipo_id'],"cab","rtf"); ?></textarea>
				<textarea name='cod_rodap' id='cod_rodap' style='float:left;position:relative;margin-left:-1000px' ><?php echo cabecalhoerodape($_POST['tipo_id'],"rod","rtf"); ?></textarea>
			</td>
			<td width="70px"></td>
		</tr>
	</table>
	<table width="800px" align="center" >
		<tr height="40px" >
			<td align="center">
				<div align="center" id="bottomSpace" style="width:790px"></div>
			</td>
		</tr>
	</table>
	<br>
	<table id="div_bottom" align="center">
		<tr>
			<td>
				<?php 
					$_POST['nomepet'] = $_POST['nomecli']?$_POST['nomecli']:$_POST['nomepet'];
				?>
				<br><span id="spn_nom">Nome do Arquivo: &nbsp;</span>
				<input type="text" name="nomepet" id="nomepet" value="<?php echo $_POST['nomepet']==""?"Nome_do_Arquivo":$_POST['nomepet']; ?>" />
				<input type="submit" id="ger_pdf" name="ger_pdf" value="" onclick="EnviarDados('inc/getpdf.php','','');" >
				<input type="submit" id="ger_rtf" name="ger_rtf" value="" onclick="EnviarDados('inc/getrtf.php','','');" >
				<input type="button" id="ger_sav" name="ger_sav" value="" onclick="fc_salvar_pet();" >
				<input type="hidden" id="id_sav" value="<?php echo $id_pecas; ?>" flag="<?php echo $flag; ?>"/>
			</td>
		</tr>
	</table>
</div>