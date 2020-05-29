<?php 


$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");
	
	function nome_email($valor){	
		$nm = explode("@",$valor);
		$nm = str_replace("."," ",$nm[0]);
		return ucwords($nm);
	}
	
	$arr_appname = array('court_session_title' => 'Audiência',
						 'meeting' => 'Reunião',
						 'phone_conversation' => 'Contato telefônico',
						 'court_session' => 'Audiência',
						 'appointments04' => 'Prazo',
						 'appointments05' => 'Compromisso',
						 'appointments06' => 'Publicação',
						 'appointments07' => 'Suspensão',
						 'appointments08' => 'Desistência',
						 'appointments09' => 'Diligência',
						 'appointments10' => 'Prosseguimento do Feito',
						 'meeting_title' => 'Reunião',
						 'phone_conversation_title' => 'Contato telefônico');
	
	$arr_meta = array();
	$Qmeta = mysql_query(" SELECT * FROM lcm_meta ",$conexao1);
	
	while($Wmeta = mysql_fetch_array($Qmeta)){
		$arr_meta[$Wmeta['name']] = $Wmeta['value'];
	}

	//Show appointments for today
	$q = "SELECT app.id_app, app.start_time, app.type, app.title, app.description, app.performed, 
		date_format(app.start_time, '%d/%m/%Y %H:%i:%s') as start_data, c.processo, c.legal_reason, c.p_cliente, 
		c.p_adverso, c.comarca, c.state, c.vara, c.id_case
		FROM lcm_app as app 
		LEFT JOIN lcm_author_app as aut on aut.id_app = app.id_app  
		LEFT JOIN lcm_case as c on c.id_case = app.id_case 
		WHERE 1 = 1 
		AND app.`type`!='appointments09'
		AND app.id_app = aut.id_app 
		AND app.performed not in ('Y') 
		AND DATE_FORMAT(app.start_time, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')
		GROUP BY app.id_app 
		ORDER BY app.reminder ASC";

	$result = mysql_query($q,$conexao1);
	
	if (mysql_num_rows($result) > 0) {
		
		$n=0;
		$para = explode(',',$arr_meta['email_agenda']);
		$assunto = 'Agenda Geral de hoje - Sistema Jurídico';
		$mensagem2 = "<br/>---------------------------------------------------------------------------------------";
	
		while ($row=mysql_fetch_array($result)){
			$n++;
			$mensagem2 .= "<br><font size='2'><b>" . 'Nº' 			. ": </b><u>" .  $n		  						  . "</u></font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Título' 		. ": </b> " .  $arr_appname[$row['type']]		  . "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Pasta' 		. ": </b> " .  utf8_encode($row['id_case']) 	  . "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Providência'  . ": </b> " .  utf8_encode($row['title'])		  . "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Partes' 		. ": </b> " .  utf8_encode($row['p_cliente'])     . " x " . utf8_encode($row['p_adverso']) . "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Descricão' 	. ": </b> " .  utf8_encode($row['description']) . "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Acão' 		. ": </b> " .  utf8_encode($row['legal_reason']). "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Data' 		. ": </b> " .  utf8_encode($row['start_data'])    . "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Comarca' 		. ": </b> " .  utf8_encode($row['comarca']) 	  . " - " . utf8_encode($row['state']) . "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Processo' 	. ": </b> " .  utf8_encode($row['processo']) 	  . "</font>";
			$mensagem2 .= "<br><font size='2'><b>" . 'Vara' 		. ": </b> " .  utf8_encode($row['vara']) 		  . "</font>";
			$mensagem2 .= "<br/>---------------------------------------------------------------------------------------";
		}

		$mensagem1  = "Prezados, bom dia!<br/><br/>Segue a agenda geral do SIJUR:<br/><br/>Total de: <u>" . $n . "</u> Agendas para hoje!";

		$mensagem2 .= "<br><br>";
		$mensagem2 .= "Atenciosamente,<br> Sistema Jurídico - SIJUR";
		
		require_once("phpmailer/class.phpmailer.php");
		$mail = new PHPMailer();
		$mail->IsSMTP(); // Define que a mensagem será SMTP
		$mail->Host = "smtp.gmail.com"; // Endereço do servidor SMTP
		$mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
		$mail->SMTPSecure = "ssl";
		$mail->Port = 465; // Porta
		$mail->Username = 'fabio.torres@eduardoalbuquerque.adv.br'; // Usuário do servidor SMTP
		$mail->Password = 'Torres.10'; // Senha do servidor SMTP
		$mail->From = utf8_encode($arr_meta["email_sysadmin"]);
		$mail->FromName = utf8_encode($arr_meta['site_description'] . " - " . $arr_meta['site_name']);  
		foreach($para as $email)
		{
			$mail->AddAddress($email, utf8_encode(nome_email($email)));
		}
		$mail->IsHTML(true); // Define que o e-mail será enviado como HTML
		$mail->CharSet 	= 'UTF-8'; // Charset da mensagem (opcional)
		$mail->Subject 	= $assunto; // Assunto da mensagem
		$mail->Body 	= $mensagem1 . $mensagem2; //<IMG src="http://direito2010.com.br/imagem.jpg" alt=5":)"   class="wp-smiley"> ';
		//$mail->AltBody 	= $mensagem1 . $mensagem2; //<IMG src="http://direito2010.com.br/imagem.jpg" alt=5":)"  class="wp-smiley"> ';
		$enviado = $mail->Send();
		$mail->ClearAllRecipients();
		$mail->ClearAttachments();
		if ($enviado) {
		  echo "E-mail enviado com sucesso!";
		} else {
		  echo "Não foi possível enviar o e-mail.";
		  echo "<b>Informações do erro:</b> " . $mail->ErrorInfo;
		}
	
	}
	
?>