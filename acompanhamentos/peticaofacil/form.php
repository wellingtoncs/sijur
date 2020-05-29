<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php

	include("inc/seguranca.php");
	include("inc/functions.php");
	protegePagina();

	$qdb = mysql_query("SELECT * FROM tp_config_db WHERE tipo_id = '" . $_POST['TIPOPET'] . "' ",$conexao1);
	$wdb = mysql_fetch_assoc($qdb);
	
	if($_POST['TIPOCHA']!="")
	{
		if(mysql_num_rows($qdb)>0)
		{
			$serv2	  = $wdb['ip_db'];
			$user2 	  = $wdb['usu_db'];
			$senha2	  = $wdb['senha_db'];
			$db2	  = $wdb['data_db'];
			$query2	  = $wdb['query_db'];
			$where2	  = $wdb['where_db'];
			
			$conexao2 = @mysql_connect( $serv2, $user2, $senha2 );
			if($conexao2){
				$banco2   = mysql_select_db( $db2, $conexao2);
				if($query2!=""){
					$Cd = mysql_query("$query2 $where2 AND " . $wdb['chave_db'] . " like '%" . $_POST['TIPOCHA'] . "%' ", $conexao2 );
				}else{
					$Cd = mysql_query("SELECT * FROM " . $wdb['table_db'] . "   WHERE " . $wdb['chave_db'] . " like '%" . $_POST['TIPOCHA'] . "%' ", $conexao2 );
				}
				$dados = mysql_fetch_assoc($Cd);			
			}
		}
	} elseif($_POST['TIPOPET']!="") {
		if(mysql_num_rows($qdb)>0)
		{
			$serv2	  = $wdb['ip_db'];
			$user2 	  = $wdb['usu_db'];
			$senha2	  = $wdb['senha_db'];
			$db2	  = $wdb['data_db'];
			$query2	  = $wdb['query_db'];
			$where2	  = $wdb['where_db'];
			
			$conexao2 = @mysql_connect( $serv2, $user2, $senha2 );
			if($conexao2){
				$banco2   = mysql_select_db( $db2, $conexao2);
				if($query2!=""){
					$Cd = mysql_query("$query2 $where2 limit 1", $conexao2 );
				}else{
					$Cd = mysql_query("SELECT * FROM " . $wdb['table_db'] . " limit 1 ", $conexao2);
				}
				$dados2 = mysql_fetch_assoc($Cd);
			}
		}
	}
	//verifica se foi conectado ao servidor
	if (!$conexao2) {
		$cntdo = "<i style='color:#FF2626'> Não foi possível conectar ao servidor: <b>$serv2</b>.</i>";
		$style = "style='display:none'";
	}
	else {
		$cntdo = "<i> Conectado ao servidor: <b>$serv2</b>, e banco de dados: <b>$db2</b>.</i>";
		$style = "";
	}
	//parâmetros dos usuários
	$usu_setor = $_SESSION['usuarioSetor'];
	$usu_nivel = $_SESSION['usuarioNivel'];
	$usu_id    = $_SESSION['usuarioID'];
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr" >
	<head>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
		<title>Apresentação - Administração</title>
		<link href="css/images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
		<link rel="stylesheet" href="css/template.css" type="text/css" />
		<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.23.custom.css">
		<script type="text/javascript" src="js/jquery-1.8.0.min.js">		</script>
		<script type="text/javascript" src="js/jquery-ui-1.8.23.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery.meio.mask.js">	 	</script>
		<script type="text/javascript" src="js/default.js">					</script>   	
		<script type="text/javascript" src="ckeditor/ckeditor.js">			</script>
		<script type="text/javascript" src="ckeditor/adapters/jquery.js">	</script>		
		<!--[if IE 7]><link href="templates/bluestork/css/ie7.css" rel="stylesheet" type="text/css" /><![endif]-->
	</head>
<body id="minwidth-body">
<form name="form_iniciais" action="form.php" method="POST">
	<div class="head_bk" ></div>
	<div class="head_fixed" >
		<div id="border-top" class="h_blue">
			<span class="logo"><img src="css/images/logo.png" alt="Sistema de Petição" /></span>
			<span class="title"><a href="index2.php">Petição Fácil</a></span>
		</div>
		<?php
		if($_POST['hid_enviar']==2 || $_POST['hid_enviar']==3 || $_POST['hid_enviar']==4)
		{
			?>
			<div id="header-box">
				<div id="topSpace" ></div>
				<div id="module-status">
					<span class="viewsite"><a href="javascript:EnviarDados('form.php','','');">In&iacute;cio</a></span>
					<span class="viewcopy"><a href="../publicacao/form.php" >Publicações</a></span>
					<span class="voltar"><a href="javascript:window.history.go(-1)">Voltar</a></span>
					<span class="logout"><a href="sair.php">Sair</a></span>
				</div>
				<div id="module-menu">
					<?php
					if($_POST['hid_enviar']==6)
					{
					?>
					<ul id="menu" >
						<li class="node"><a href="#">Tipo de Petição</a>
							<ul>
								<?php fc_select_li("tp_tipo_tb","6","tipo_id","tipo_nome","2156",$conexao1,$usu_setor); ?>
							</ul>
						</li>
					</ul>
					<?php
					}
					?>
				</div>
				<div class="clr" ></div>
			</div>
			<?php
		}
		elseif($_POST['hid_enviar']==5 || $_POST['hid_enviar']==6 || $_POST['hid_enviar']==7 || $_POST['hid_enviar']==8 || $_POST['hid_enviar']==9)
		{
			?>
			<!--Painel de Administração-->
			<div id="header-box">
				<div id="topSpace" ></div>
				<div id="module-status">
					<span class="viewsite"><a href="javascript:EnviarDados('form.php','','');">Início</a></span>
					<?php 
					if($usu_nivel=='ADM')
					{
						if($_POST['hid_enviar']==8)
						{
							?>
							<span class='newuser'><a href='javascript:fc_edit_usu("","I");'>Novo Usuário</a></span>
							<?php
						}
						elseif($_POST['hid_enviar']==9)
						{
							?>
							<span class='newsetor'><a href='javascript:fc_edit_setor("","I");'>Novo Setor</a></span>
							<?php
						}
						?>
						<span class="viewconfig"><a href="#" onclick="EnviarDados('form.php','5','')">Administrar</a></span>
						<?php
					}
					?>
					<span class="viewcopy"><a href="../publicacao/form.php" >Publicações</a></span>
					<span class="voltar"><a href="javascript:window.history.go(-1)">Voltar</a></span>
					<span class="logout"><a href="sair.php">Sair</a></span>
				</div>
				<div id="module-menu">
					<?php
					if($_POST['hid_enviar']==6 || $_POST['hid_enviar']==7)
					{
						?>
						<ul id="menu" >
							<li class="node"><a href="#">Tipo de Petição</a>
								<ul>
									<?php fc_select_li("tp_tipo_tb",$_POST['hid_enviar'],"tipo_id","tipo_nome","2156",$conexao1,$usu_setor); ?>
								</ul>
							</li>
						</ul>
						<?php
						if($_GET['TIPOPET']!='' || $_POST['TIPOPET']!='')
						{
							$TIPOPET = $_POST['TIPOPET'] ? $_POST['TIPOPET'] : $_GET['TIPOPET'];
							$t = mysql_query("SELECT tipo_nome FROM tp_tipo_tb WHERE tipo_id = '" . $TIPOPET . "' ",$conexao1);
							$tw = mysql_fetch_array($t);
							//echo $tw[0];
						}
						?>
						<?php
					}
					?>
				</div>
				<div class="clr"></div>
			</div>
			<?php
		}
		else
		{
			?>
			<!--Painel do Usuário-->
			<div id="header-box">
				<div id="module-status">
					<span class="viewsite"><a href="javascript:EnviarDados('form.php','','');">Início</a></span>
					<?php 
					if($usu_nivel=='ADM' || $usu_nivel=='GER')
					{
						?>
						<span class="viewconfig"><a href="#" onclick="EnviarDados('form.php','5','')">Administrar</a></span>
						<?php
					}
					if($usu_nivel=='USU')
					{
						?>
						<span class="viewcopy"><a href="#" onclick="EnviarDados('form.php','10','')">Minhas Petições</a></span>
						<?php
					}
					if($usu_nivel=='ADM')
					{
						?>
						<span class="viewcopy"><a href="#" onclick="EnviarDados('form.php','10','')">Petições Salvas</a></span>
						<?php
					}
					?>
					<span class="viewcopy"><a href="#" onclick="EnviarDados('../publicacao/form.php','10','')">Ler Publicações</a></span>
					<span class="voltar"><a href="javascript:window.history.go(-1)">Voltar</a></span>
					<span class="logout"><a href="sair.php">Sair</a></span>
				</div>
				<div id="module-menu">
					<ul id="menu" >
						<li class="node"><a href="#">Tipo de Petição</a>
							<ul>
								<?php fc_select_li("tp_tipo_tb",'1',"tipo_id","tipo_nome","2156",$conexao1,$usu_setor); ?>
							</ul>
						</li>
						<?php if(isset($_POST['TIPOPET']) && $_POST['TIPOPET']!="" ){ ?>
						<li class="node" <?php echo $style; ?>><table><tr><td><input type="text" name="TIPOCHA" id="TIPOCHA" class="inputbox"></td></tr></table></li>
						<li class="node" <?php echo $style; ?>><a href="#">Buscar</a>
							<ul>
								<li><a class="icon-16-help" href="#" onclick="EnviarDados('form.php','1','<?php echo $_POST['TIPOPET']; ?>');"><?php echo $wdb['chave_db']; ?></a></li>
								<li class="separator"><span></span></li>
							</ul>
						</li>
						<?php } ?>
					</ul>
				</div>
				<div class="clr"></div>
			</div>
			<?php
			if($_GET['TIPOPET']!='' || $_POST['TIPOPET']!='')
			{
				$TIPOPET = $_POST['TIPOPET'] ? $_POST['TIPOPET'] : $_GET['TIPOPET'];
				$t = mysql_query("SELECT tipo_nome FROM tp_tipo_tb WHERE tipo_id = '" . $TIPOPET . "' ",$conexao1);
				$tw = mysql_fetch_array($t);
				//echo $tw[0];
			}
		}
		?>
	</div>
	<div id="content-box">
		<div id="element-box">
			<div class="m wbg" >
				<div class="adminform">
					<?php
						if($_POST['hid_enviar']==1)
						{
							include 'dados.php';
						}
						elseif($_POST['hid_enviar']==2)
						{
							include 'parag.php';
						}
						elseif($_POST['hid_enviar']==3)
						{
							include 'editor.php';
						}
						elseif($_POST['hid_enviar']==5)
						{
							include 'admin.php';
						}
						elseif($_POST['hid_enviar']==6)
						{
							include 'config.php';
						}
						elseif($_POST['hid_enviar']==7)
						{
							include 'dados.php';
						}
						elseif($_POST['hid_enviar']==8)
						{
							include 'usu.php';
						}
						elseif($_POST['hid_enviar']==9)
						{
							include 'setor.php';
						}
						elseif($_POST['hid_enviar']==10)
						{
							include 'pecas.php';
						}
						else
						{
							?>
							<div class="content_body">
								<div class="cpanel-left">
									<div class="cpanel">
										<?php fc_select_div("tp_tipo_tb",'1',"tipo_id","tipo_nome","2156","S",$conexao1,$usu_setor); ?>
									</div>
								</div>
							</div>	
							<?php
						}
						?>
				</div>
			</div>
		</div>
	</div>
	
	<input type="hidden" name="hid_enviar" id="hid_enviar" value="<?php echo $_POST['hid_enviar']; ?>" />
	<input type="hidden" id="TIPOPET" name="TIPOPET" value="<?php echo $TIPOPET; ?>">
	
	<div id="footer">
		<p class="copyright">
			<a href="#">EA</a> - Criado pelo setor de Desenvolvimento. Contato: <a href="fabio@direito2010.com.br">fabio@direito2010.com.br</a>.		
		</p>
	</div>
</form>
<!--Crinado Inputs dinâmicos-->
<div>
	<div id="dialog_inputs" style="display:none">
		<div style="height:340px">
			<center>
				<br/>
				<div align="center">
					<label>TIPO DE CAMPO:</label><br><br>
					<span><input type="radio" id="SELEINPUT" name="SELEINPUT" class="input-default" value="TIPOINP" onclick="fc_optTexto(this.value);" checked></span>
					<label>TEXTO</label>
					<span><input type="radio" id="SELEINPUT" name="SELEINPUT" class="input-default" value="TIPOSEL" onclick="fc_optTexto(this.value);"></span>
					<label>OPÇÕES</label>
					<span><input type="radio" id="SELEINPUT" name="SELEINPUT" class="input-default" value="TIPOTIT" onclick="fc_optTexto(this.value);"></span>
					<label>TÍTULO</label>
				</div>
				<br/>
				<table width="100%" style="border: 1px solid #D0D0D0">
					<tr height="30px"><td align="left"><b>Nome:	<br></td><td align="left"><input type="text"  name="INPTITLE" id="INPTITLE" class="input-default" style="width:300px"	/></td></tr>
				</table> 
				<table id="tb_addText" width="100%" style="border: 1px solid #D0D0D0">
					<tr>
						<td>
							<table width="50%" align="left">
								<tr height="30px"><td align="right"><input type="radio" name="INPCHECK" id="INPCHECK" class="input-default" value=""   checked  /></td><td align="left"><b>Padrão	</b></td></tr>
								<tr height="30px"><td align="right"><input type="radio" name="INPCHECK" id="INPCHECK" class="input-default" value="date"		/></td><td align="left"><b>Data		</b></td></tr>
								<tr height="30px"><td align="right"><input type="radio" name="INPCHECK" id="INPCHECK" class="input-default" value="decimal"		/></td><td align="left"><b>Valor	</b></td></tr>
							</table> 
							<table width="50%" align="left">
								<tr height="30px"><td align="right"><input type="radio" name="INPCHECK" id="INPCHECK" class="input-default" value="cnpj"		/></td><td align="left"><b>Cpf/Cnpj	</b></td></tr>
								<tr height="30px"><td align="right"><input type="radio" name="INPCHECK" id="INPCHECK" class="input-default" value="cep"			/></td><td align="left"><b>CEP		</b></td></tr>
								<tr height="30px"><td align="right"><input type="radio" name="INPCHECK" id="INPCHECK" class="input-default" value="phone"		/></td><td align="left"><b>Fone		</b></td></tr>
							</table> 
							
						</td>
					</tr>
				</table>
				<table id="tb_addSel" class="tb_addSel" width="100%" style="border: 1px solid #D0D0D0; display:none">
					<tr>
						<td>
							<h3>Adic/remover texto</h3>

							<a href="#" class="add">	<img src="img/add.png" alt="add" title="add input" height="24" width="24"></a> 
							<a href="#" class="remove">	<img src="img/remove.png" alt="remove input" height="24" width="24"></a>
							<a href="#" class="reset">	<img src="img/reset.png" alt="reset" height="24" width="24"></a>
							<div id="inputs"></div>
						</td>
					</tr>
				</table>
				<table id="tb_addTit" width="100%" style="border: 1px solid #D0D0D0; display:none">
					<tr>
						<td></td>
					</tr>
				</table>
				<table id="tb_addBase" width="100%" style="border: 1px solid #D0D0D0">
					<tr>
						<td align="left">Colunas:</td>
						<td align="left">
							<select name="inputcol" id="inputcol" class="input-default" style="width:120px">
								<option>1</option>
								<option>2</option>
								<option>3</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">Obrigatório:</td>
						<td align="left">
							<select name="inputReq" id="inputReq" class="input-default" style="width:120px">
								<option value="1">Nao</option>
								<option value="2">Sim</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">Associar com o Banco de Dados:</td>
						<td align="left">
							<select name="db_col" id="db_col" class="input-default" style="width:120px">
								<?php
								echo "<option></option>";
								if(isset($dados)){
									foreach($dados as $k => $v)
									{
										echo "<option>" . $k . "</option>";
									}
								}elseif(isset($dados2)){
									foreach($dados2 as $k => $v)
									{
										echo "<option>" . $k . "</option>";
									}
								}
								
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" class="tb_addSel" style="display:none">Associar com Base existente:</td>
						<td align="left" class="tb_addSel" style="display:none">
							<select name="tbBase" id="tbBase" class="input-default" style="width:120px">
								<?php
								$qlist = mysql_query("SELECT * FROM tp_inputs_tb WHERE listsel = 'Y'",$conexao1);
								echo "<option></option>";
								while($wlist = mysql_fetch_array($qlist))
								{
									echo "<option value='tp_dados_tb_|_nome_dados_|_return_5_|_id_input=" . $wlist['id_input'] . "'>" . $wlist['input_title'] . "</option>";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">Extra:</td>
						<td align="left">
							<input type="text" name="inptFunc" id="inptFunc" class="input-default" style="width:120px"/>
						</td>
					</tr>
				</table>
			</center>	
		</div>
	</div><br/>
</div>
</body>
</html>