<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include("inc/conectar.php");
include("inc/functions.php");
$cls_text = "";
for($i=0;$i<=$_POST['edit_text'];$i++)
{
	$cls_text .= str_replace('\"','"',$_POST['cls_text_' . $i]);
}
?>
<meta 	http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<html>
<head>
	<title>Gerador de Petições</title>
	<link type="text/css" rel="stylesheet" href="css/css.css">
	<script type="text/javascript" src="js/jquery-1.7.2.js">			</script>
   	<script type="text/javascript" src="ckeditor/ckeditor.js">			</script>
   	<script type="text/javascript" src="ckeditor/adapters/jquery.js">	</script>
	<script language="javascript">	
	$(function (){
		
		
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
		$('#name_text').ckeditor(config);
		
		$('#scrlBotm').click(function (){
			$('html, body').animate({scrollTop: $(document).height()},1500);
			return false;
		});
		$('#scrlTop').click(function () {
			$('html, body').animate({scrollTop: '0px'},1500);
			return false;
		});
		
		$("#ger_pdf").mouseover(function(){
			$("#ger_pdf").fadeTo("slow", 1.0);
		});
		$("#ger_pdf").mouseout(function(){
			$("#ger_pdf").fadeTo("slow", 0.8);
		});
		$("#ger_rtf").mouseover(function(){
			$("#ger_rtf").fadeTo("slow", 1.0);
		});
		$("#ger_rtf").mouseout(function(){
			$("#ger_rtf").fadeTo("slow", 0.8);
		});
	});
	
	$(document).ready(function(){
		$('#ger_pdf').hide();
		$('#ger_rtf').hide();
		$("body").css("color","#000000");	
		$(function () {
			$(window).scroll(function () {
				if ($(this).scrollTop() > ($("#tb_editor").height()-800)) {
					$("#ger_pdf").fadeTo("slow", 0.8);
					$("#ger_rtf").fadeTo("slow", 0.8);
					$('#ger_pdf').fadeIn();
					$('#ger_rtf').fadeIn();
				} else {
					$('#ger_pdf').fadeOut();
					$('#ger_rtf').fadeOut();
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
	
	function fc_submit(valor){
		if(valor==1){
			$("#form2").attr("action","inc/getpdf.php");
		}
		else if(valor==2){
			$("#form2").attr("action","inc/getrtf.php");
		}
	}
	
	$(window).load(function(){
		ver_title();
	})
	
	function fc_focus(valor){
		$("#"+valor).focus();
	}
	
	</script>
	
<div class="demo2">
	<form id="form2" name="form2" action="inc/getpdf.php"  method="POST" >
		<table align="center" class="topo2" cellpadding="2" cellspacing="4">
			<tr>
				<td class="topo2" align="right"><center><div id="topSpace" style="width: 800px;"></div></center></td>
			</tr>
		</table>
		<table id="tb_left" align="left" width="60px" height="90%">
			<tr height="30px" valign="top">
				<td>
					<a id="botao_next" href="#" onclick="goToByScroll('titulos',$('#id_topicos').val(),1)" ></a>
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
		<table id="tb_right" align="right" width="60px" height="90%">
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
		<table id="tb_editor" background="img/fundo.jpg" align="center" width="788px" style="border-bottom: 1px solid #999999; margin-top:85px">
			<tr>
				<td width="45px" height="25px"></td>
				<td colspan="3" align="center" style="margin-bottom:100px;" >
					<textarea id="name_text" name="name_text" style="color:#000000; background-color: transparent !important; border: 1px solid #ccc; width: 620px; height: 800px;" ><?php echo $cls_text; ?></textarea>
					<input type='hidden' name='url_dir' id='url_dir' value='<?php echo $_POST['url_dir']; ?>' />
					<input type="hidden" name="nomecli"	id="nomecli" value="<?php echo $_POST['nomecli']; ?>" />
				</td>
				<td align="center" width="45px"></td>
			</tr>
		</table>
		<table width="800px" align="center">
			<tr height="40px" >
				<td align="center">
					<div align="center" id="bottomSpace" style="width:790px"></div>
				</td>
			</tr>
		</table>
		<div id="div_bottom" align="center" width="200px" height="40px">
			<!--input type="submit" id="ger_pdf" value="Gerar Petição em PDF" onclick="fc_submit(1);" style="height: 30px; cursor:pointer;">&nbsp; -->
			<input type="submit" id="ger_rtf" value="Gerar Petição em DOC" onclick="fc_submit(2);" style="height: 30px; cursor:pointer;" >
		</div>
		<br/>
	</form>
</div>
</body>
</html>