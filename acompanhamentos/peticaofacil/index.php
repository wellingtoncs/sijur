<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr" >
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>Peti��o - F�cil</title>
	<link href="css/images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="css/system.css" type="text/css" />
	<link rel="stylesheet" href="css/template.css" type="text/css" />
	<!--[if IE 7]><link href="templates/bluestork/css/ie7.css" rel="stylesheet" type="text/css" /><![endif]-->
</head>
<body>
	<div id="border-top" class="h_blue">
		<span class="logo"><img src="css/images/logo.png" alt="Sistema de Peti��o" /></span>
		<span class="title"><a href="index.php">Peti��es F�cil</a></span>
	</div>
	<div id="content-box">
			<div id="element-box" class="login">
				<div class="m wbg">
					<h1>Acessar o Painel de Peti��es</h1>
					<div id="system-message-container"></div>
					<div id="section-box">
						<div class="m">
							<form action="inc/valida.php" method="post" id="form-login">
								<fieldset class="loginform">
									<label id="mod-login-username-lbl" for="mod-login-username">Nome de Usu�rio</label>
									<input type="text" name="username" id="mod-login-username" class="inputbox" size="15" />
									<label id="mod-login-password-lbl" for="mod-login-password">Senha</label>
									<input type="password" name="passwd" id="mod-login-password" class="inputbox" size="15" />
									
										<div class="button-holder">
											<div class="button1">
												<div class="next">
													<a href="#" onclick="document.getElementById('form-login').submit();">Acessar</a>
												</div>
											</div>
										</div>
									<div class="clr"></div>
									<input type="submit" class="hidebtn" value="Acessar" />
								</fieldset>
							</form>
							<div class="clr"></div>
						</div>
					</div>
					<p>Use um nome de usu�rio e senha v�lidos para acessar o Painel de Administra��o.</p>
					<p></p>
					<div id="lock"></div>
				</div>
			</div>
			<noscript>
				Aten��o! JavaScript deve estar habilitado para o bom funcionamento do backend do administrador.			</noscript>
	</div>
	<div id="footer">
		<p class="copyright">
			<a href="#">EA</a> - Cria��o do setor de Desenvolvimento. Contato: <a href="mailto:fabio@direito2010.com.br">fabio@direito2010.com.br</a>.		</p>
	</div>
</body>
</html>
