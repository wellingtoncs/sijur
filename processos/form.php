<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.cliente/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<?php
include('inc/inc.php');
include_lcm('inc_obj_case');
global $author_session;
global $prefs;
$remetente = $GLOBALS['author_session']['name_first'] . " " . $GLOBALS['author_session']['name_last'];
?>
<link rel="stylesheet" type="text/css" href="styles/lcm_basic_layout.css" media="screen" />
<link rel="stylesheet" type="text/css" media="screen" href="styles/lcm_ui_green.css" />
<link rel="stylesheet" type="text/css" media="screen" href="styles/lcm_opt_mediumfonts.css" />
<script type="text/javascript" language="JavaScript" src="inc/jquery.js"></script>
<script type="text/javascript" language="JavaScript" src="inc/meiomask.js"></script>
<script language="JavaScript" >
	function submit_email()
	{
		if($('#email').val()==""){
			alert("Favor preencha o campo 'e-mail'.");
		
		} else {
			//document.form_email.action = "form.php";
			document.form_email.submit();
		}
	}
</script>
<form name="form_email" method="post" action="form.php">
<table align="left">
	<tr>
		<td align="left" style="font-size:10pt; color:#00375a">Email: <input type="text" id="email" name="email" size="35" /></td>
		<td align="left" ><a href="#" class="create_new_lnk" onclick="submit_email();" style="float: right; margin-top: -5px; "><?php echo _T('adverso_button_send'); ?></a></td>
		<input type="hidden" name="send" value="1" />
	</tr>
<?php 
if($_POST['send']!=1)
{
	?>
	<tr>
		<td align="left" style="font-size:10pt; color:#00375a">Lista dos últimos relatórios:</td>
	</tr>
	<?php
	//define o caminho do diretório
	$dir = "arquivos";
	//listar arquivos
	$files = glob($dir."/*.xls") or die("Erro ao acessar " . $dir);
	arsort($files);
	//permorre a lista
	$i = 0;
	foreach($files as $file) {
		if (!is_dir($file)){
			$arquivo = str_replace("arquivos/", "", $file);
			$explode = explode("_",$arquivo);
			if($explode[1]==$_GET['exp'])
			{
				if (++$i == 5)
				{
					unlink($file);
				}
				echo '<tr>
						<td align="left" style="font-size:8pt; color:#00375a">
							<input type="radio" id="arquivo" '.($i==0 ? "checked" : "" ).' name="arquivo" value="'.$arquivo.'" />' . $arquivo . '
						</td>
					</tr>';
			}
		}
	}
}
else
{
	$arquivo = $_POST['arquivo'];
	 // Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
	require("phpmailer/class.phpmailer.php");
	 
	 // Inicia a classe PHPMailer
	$mail = new PHPMailer();

	// Define os dados do servidor e tipo de conexão

	$mail->IsSMTP(); // Define que a mensagem será SMTP
	$mail->Host = "smtp.direito2010.com.br"; // Endereço do servidor SMTP
	$mail->SMTPAuth = true; // Autenticação
	$mail->Username = 'fabio@direito2010.com.br'; // Usuário do servidor SMTP
	$mail->Password = 'torres'; // Senha da caixa postal utilizada
	 
	 // Define o remetente

	$mail->From = "sistema@eduardoalbuquerque.adv.br";
	$mail->FromName = utf8_decode($remetente);  
	 
	 // Define os destinatário(s) 
	$email = $_POST['email'];
	$mail->AddAddress("$email", "$email");
	//$mail->AddAddress('e-mail@destino2.com.br');
	//$mail->AddCC('copia@dominio.com.br', 'Copia'); 
	//$mail->AddBCC('CopiaOculta@dominio.com.br', 'Copia Oculta'); 
	 
	 // Define os dados técnicos da Mensagem

	$mail->IsHTML(true); // Define que o e-mail será enviado como HTML
	$mail->CharSet = 'ISO-8859-1'; // Charset da mensagem (opcional)
	 
	 // Texto e Assunto

	$mail->Subject  = "Relatório de processos"; // Assunto da mensagem
	$mail->Body = 'Prezado, Segue em anexo o relatório de nº: ' .str_replace(".xls", "", $arquivo) ; //<IMG src="http://direito2010.com.br/imagem.jpg" alt=5":)"   class="wp-smiley"> ';
	$mail->AltBody = 'Este é o corpo da mensagem de teste, em Texto Plano! \r\n '; //<IMG src="http://direito2010.com.br/imagem.jpg" alt=5":)"  class="wp-smiley"> ';
	 
	 // Anexos (opcional)

	$mail->AddAttachment("arquivos\\$arquivo", "$arquivo");  

	 // Envio da Mensagem
	$enviado = $mail->Send();
	 
	 // Limpa os destinatários e os anexos
	$mail->ClearAllRecipients();
	$mail->ClearAttachments();
	 
	 // Exibe uma mensagem de resultado
	if ($enviado) {
	echo "<tr><td style='font-size:10pt; color:#00375a'>E-mail enviado com sucesso! </td></tr>";
	} else {
		echo "<tr><td style='font-size:10pt; color:#00375a'> Não foi possível enviar o e-mail.</td></tr>";
		echo "<tr><td style='font-size:10pt; color:#00375a'>Informações do erro: " . $mail->ErrorInfo ."</td></tr>";
	}
}
?>
</table>
</form>