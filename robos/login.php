<?php

	session_start();
	
	$con_sisjur = mysql_connect('localhost', 'robosis', 'recife123');
	mysql_select_db('processos_db', $con_sisjur);
	

	
	if (isset($_POST['login']))
	{
		if (trim($_POST['login']) == 'robo' && trim($_POST['pwd']) == '#legemsrs')
		{
			$_SESSION['robot'] = array('access' => true);
			header('location: relatorio.php');
			exit;			
		}
		else
		{
			$error = 'Acesso negado!';
		}
	}
	else
	{
		$error = '';
	}
?>
<html>
    <head>
        <style>
            table tr td{
                font-family: arial;
                font-size: 12px;
            }
			
			.div_circ
			{
			    -moz-border-radius: 10px 10px 10px 10px;
				-webkit-border-radius: 10px 10px 10px 10px;
				border-radius: 10px 10px 10px 10px;
				background-color: #ffffff;
				border: 2px solid #c0c0c0;
			}
        </style>
        <script>
            function fc_submit()
            {

            }
        </script> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>    
    <body onload='document.getElementById("login").focus();'>
        <table width="100%"> 
            <tr>
                <td width="80px"> 
                    <img src="img/robot.png" />
                </td>   
                <td>
                    Relat&oacute;rio dos Rob&ocirc;s: EA001 e EA002<br />                    
                    Criado em Abril de 2015<br />
					Contato: julianophp@gmail.com; alexandre.cavalcanti@bb.com.br (81) 9924-5330
                </td>    
            </tr>
            <tr>
                <td colspan="2"><hr></td>
            </tr>
			<tr>
				<td colspan="2">
					<br><br><br><br><br><br><br><br>
					<center>
						<div class="div_circ" style="width: 300px; height: 150px;">
							<br><br>
							<center>
								<form method='POST' name='frm_login' action='login.php'>
									<table>
										<tr>
											<td>Login</td>
											<td><input type='text' name='login' id='login' style='width:100px' /></td>	
										</tr>
										<tr>
											<td>Senha</td>
											<td><input type='password' name='pwd' style='width:100px' /></td>	
										</tr>
									</table>
									<br><br>
									<input type="submit" value="Entrar" />
								</form>
								
								<?php
								if (!empty($error))
								{
								?>
									<div style='color:red; font-size: 14px;' id='div_error'>
										<br><br><?php echo $error; ?>
									</div>
									<script>
										setTimeout(function(){
											document.getElementById('div_error').style.display = 'none';
										}, 4000);
									</script>
								<?php
								}
								?>
								
							</center>
						</div>
					</center>
				</td>
			</tr>
		</table>
    </body>
</html>	
