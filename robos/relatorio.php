<?php

	session_start();
	
	if (!isset($_SESSION['robot']) || !$_SESSION['robot']['access'])
	{
		header("location: login.php");
		exit;
	}
	
	if (isset($_POST['logoff']) && $_POST['logoff'] == 'S')
	{
		unset($_SESSION['robot']);
		header("location: login.php");
		exit;
	}
	
	$con_sisjur = mysql_connect('localhost', 'robosis', 'recife123');
	mysql_select_db('processos_db', $con_sisjur);
	
	$con_robo = mysql_connect('10.10.0.140', 'root', 'legemsrs');
	mysql_select_db('robo', $con_robo);
					
	$dt_hr = date('Y-m-d H:i:s');
					
	if (isset($_POST['id_followup']) && !empty($_POST['id_followup']))
	{
		if ($_POST['st'] == 0)
		{
			if (mysql_query("delete from tb_rob_relatorio where id_followup = " . $_POST['id_followup'], $con_robo))
			{
				mysql_query("update lcm_followup set robo_ins = 0, robo_cad = null where id_followup = " . $_POST['id_followup'], $con_sisjur);
			} 
		}
		elseif ($_POST['st'] == 7)
		{
			if (mysql_query("update tb_rob_relatorio set st = 7, dh_st_7 = '$dt_hr' where id_followup = " . $_POST['id_followup'], $con_robo))
			{
				mysql_query("update lcm_followup set robo_ins = 7, robo_cad = '$dt_hr' where id_followup = " . $_POST['id_followup'], $con_sisjur);
			} 
		}		
	}
			
	$robo       = isset($_POST['robo'])       ? $_POST['robo']   : '';
	$sel_status = isset($_POST['sel_status']) ? $_POST['sel_status'] : '';	
?>
<html>
    <head>
        <style>
            table tr td{
                font-family: arial;
                font-size: 12px;
            }
        </style>
        <script>
			function fc_submit_rel()
            {
				document.getElementById('div_relatorio').style.display = 'none'; 
				document.getElementById('spn_processando').style.display = ''; 
				document.frm_relatorio.submit();
            }
			
            function fc_submit_st(n_id_followup, n_st)
            {
				document.getElementById('div_relatorio').style.display = 'none'; 
				document.getElementById('spn_processando').style.display = ''; 				
				document.frm_relatorio.id_followup.value = n_id_followup;
				document.frm_relatorio.st.value          = n_st;
				document.frm_relatorio.submit();
            }
						
        </script> 
		<?php
		if (false && $robo == 'SRS')
		{
		?>
			<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<?php	
		}
		else
		{
		?>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<?php
		}
		?>        
    </head>    
    <body>
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
				<td align="right">
					<div style='width: 42px; text-align: left;'>
					   <img src="img/exit.png" width="36px" height="36px" style="cursor: pointer" 
					        onclick='document.frm_exit.submit();' title="Sair" />
					</div>
				</td>					
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
			<tr>
				<td colspan="3">
					<br />
					<form method="POST" action="relatorio.php" name="frm_relatorio">
						<table>
							<tr>
								<td>Rob&ocirc;:</td>
								<td>
									<select name="robo" onchange="document.getElementById('div_relatorio').style.display = 'none'; document.getElementById('spn_processando').style.display = ''; document.frm_relatorio.submit();">
										<option></option>
										<option value='LEG' <?php echo ($robo == 'LEG' ? 'selected' : ''); ?>>Legem</option>
										<option value='SRS' <?php echo ($robo == 'SRS' ? 'selected' : ''); ?>>SRS</option>
									</select>
								</td>
								
								<td>&nbsp;&nbsp;&nbsp;</td>
								
								<td>Status:</td>
								<td>
									<select name="sel_status" onchange="fc_submit_rel();">
										<option></option>
										<option value='0'   <?php echo ($sel_status == '0'   ? 'selected' : ''); ?>>Pendentes</option>
										<option value='2'   <?php echo ($sel_status == '2'   ? 'selected' : ''); ?>>Realizados</option>
										<option value='3'   <?php echo ($sel_status == '3'   ? 'selected' : ''); ?>>Marcados como Não Transferir</option>
										<option value='4,6' <?php echo ($sel_status == '4,6' ? 'selected' : ''); ?>>Não Realizados</option>
										<option value='5'   <?php echo ($sel_status == '5'   ? 'selected' : ''); ?>>Faltando realizar "De-Para"</option>
										<option value='7'   <?php echo ($sel_status == '7'   ? 'selected' : ''); ?>>Realizados Manualmente</option>
									</select>
								</td>
								<td>&nbsp;&nbsp;</td>
								<td>
									<?php
									if ($robo != '' && $sel_status == '0')										
									{	
										$rs_time = mysql_query("select last_time from tb_rob_sit where robo = '$robo'", $con_robo);
										
										if ($ln_time = mysql_fetch_assoc($rs_time))
										{																						
											if (microtime(true) - $ln_time['last_time'] > 600)
											{
											?>
												<img src='img/red_ball.gif'>
											<?php
											}
											else
											{
											?>
												<img src='img/Ball_Green.png'>
											<?php
											}												
										}
									}
									?>
								</td>	
							</tr>
						</table>					
						<span id='spn_processando' style='display:none;'><br>Processando...</span>
						<div style='font-size: 5px;'>&nbsp;</div>
						<div id='div_relatorio'> 
							<?php
							if ($robo != '' && $sel_status != '')
							{							
								include_once "get_eve_atu_st.php";
								
								if ($sel_status == '0')
								{
									$id_kw = $robo == 'LEG' ? 52 : 147;
									
									$sql = "select distinct c.id_case, f.id_followup, f.`description`, f.`type`, " .
										   "kc.value, c.vara, f.date_cad  " .
										   "from lcm_followup f inner join lcm_case c on f.id_case = c.id_case  " .
										   "join lcm_keyword_case as kc on kc.id_case=c.id_case " . 
										   "where robo_ins in (0, 1) and system_name = '$robo' and " . 
										   "kc.id_keyword = $id_kw and " .
										   "c.`status` = 'open' and not kc.value is null order by f.date_cad ";
										   
									$rs = mysql_query($sql, $con_sisjur);	

									$arr_followup = array();
									
									while($ln = mysql_fetch_assoc($rs))
									{
										/*
										switch($robo)
										{
											case 'LEG':
												$evento = fc_get_evento_legem($ln['type'], $ln['id_followup'], $dt_hr);
												break;
												
											case 'SRS':
												$evento = fc_get_evento_srs($ln['type'], $ln['id_followup'], $dt_hr);
												break;
										}
										
										$ln['evento'] = $evento;
										*/
										$arr_followup[$ln['id_followup']] = $ln;	
									}

									?>
									<table style="border-collapse: collapse;" border="1" cellpadding='2' cellspacing="0">
										<tr style='background-color: #E3EAE9;'>									
											<td width='80px'><b>Id FollowUP</b></td>
											<td width='80px'><b>Id Case</b></td>
											<td width='200px'><b>Description</b></td>
											<td width='80px'><b>Type</b></td>
											<td width='80px'><b>Código</b></td>
											<td width='200px'><b>Vara</b></td>
											<td width='80px' align='center'><b>Data</b></td>
											<td width='70px' align='center'><b>Hora</b></td>
											<!--td width='200px'><b>Evento</b></td-->
											<td width='16px'>&nbsp;</td>
										</tr>
									</table>
									<div style='width: 930px; height: 350px; overflow: auto; border: 1px solid #000000;'>
										<table style="border-collapse: collapse;" border="1" cellpadding='2' cellspacing="0">	
										<?php	

										$cont = 0;	
										
										foreach($arr_followup as $id_followup => $ln)
										{
											$description = utf8_encode($ln['description']);// => SENTEN�A AINDA N�O PROFERIDA
											$type 		 = $ln['type'];// => followups21
											$value 		 = $ln['value'];// => 317147
											$vara 		 = utf8_encode($ln['vara']);// => 16a VARA C�VEL  - JUSTI�A ESTADUAL - COMUM
											//$evento 	 = utf8_encode($ln['evento']);// => CONTESTA��O APRESENTADA										
											
											$dh = $ln['date_cad'];
											$dt = substr($dh, 8, 2) . "/" . substr($dh, 5, 2) . "/" . substr($dh, 0, 4);
											$hr = substr($dh, 11, 5);
										?>
											<tr>
												<td width='80px' valign='top'><?php echo $ln['id_followup']; ?></td>									
												<td width='80px' valign='top'><?php echo $ln['id_case']; ?></td>
												<td width='200px' valign='top'><div style='width: 200px; overflow: auto;'><?php echo $description; ?></div></td>
												<td width='80px' valign='top'><?php echo $type; ?></td>
												<td width='80px' valign='top'><?php echo $value; ?></td>
												<td width='200px' valign='top'><?php echo $vara; ?></td>
												<td width='80px' align='center' valign='top'><?php echo $dt; ?></td>
												<td width='70px' align='center' valign='top'><?php echo $hr; ?></td>												
												<!--td width='200px'><?php //echo $evento; ?></td-->
											</tr>
										<?php	
											$cont++;
										}
										?>
										</table>
									</div>
									<?php								   
								}
								else
								{								
									$rs = mysql_query("select * from tb_rob_relatorio " . 
													  "where tipo = '$robo' and st in ($sel_status) " . 
													  "order by dh_st_1 desc", $con_robo);					    											
									?>
									<table style="border-collapse: collapse;" border="1" cellpadding='2' cellspacing="0">
										<tr style='background-color: #E3EAE9;'>
											<td width='80px'><b>Id FollowUP</b></td>
											<td width='80px'><b>Id Case</b></td>
											<td width='300px'><b>Description</b></td>
											<td width='80px'><b>Type</b></td>
											<td width='80px'><b>Código</b></td>
											<td width='200px'><b>Vara</b></td>
											<td width='80px' align='center'><b>Data</b></td>
											<td width='70px' align='center'><b>Hora</b></td>
											<?php
											if ($sel_status == '4,6' || $sel_status == '5')
											{
											?>
											<td width='100px' align='center'><b>Operações</b></td>
											<?php
											}
											?>
											<td width='16px'>&nbsp;</td>
										</tr>
									</table>
									<div style='width: <?php echo ($sel_status == '4,6' || $sel_status == '5') ? '1135' : '1030'; ?>px; height: 350px; overflow: auto; border: 1px solid #000000;'>
										<table style="border-collapse: collapse;" border="1" cellpadding='2' cellspacing="0">	
										<?php	

										$cont = 0;	
										
										while($ln = mysql_fetch_assoc($rs))
										{
											$dh = $ln['dh_st_' . $ln['st']];
											
											$dt = substr($dh, 8, 2) . "/" . substr($dh, 5, 2) . "/" . substr($dh, 0, 4);
											$hr = substr($dh, 11, 5);
											
											$id_kw = $robo == 'LEG' ? 52 : 147;
											
											$rs2 = mysql_query("select distinct c.id_case, f.`description`, f.`type`, " . 
															   "kc.value, c.vara  " . 
																"from lcm_followup f inner join lcm_case c on f.id_case = c.id_case " .  
																"join lcm_keyword_case as kc on kc.id_case=c.id_case " .   
																"where system_name = '$robo' and " . 
																"kc.id_keyword = $id_kw and " . 
																"not kc.value is null " . 
																"and id_followup = " . $ln['id_followup'], $con_sisjur);

											if ($ln2 = mysql_fetch_assoc($rs2))	
											{
												$id_case     = $ln2['id_case'];
												$description = utf8_encode($ln2['description']);// => SENTEN�A AINDA N�O PROFERIDA
												$type 		 = $ln2['type'];// => followups21
												$value 		 = $ln2['value'];// => 317147
												$vara 		 = utf8_encode($ln2['vara']);
											}	
											else	
											{
												$id_case     = '';
												$description = ''; 
												$type 		 = ''; 
												$value 		 = ''; 
												$vara 		 = ''; 
											}				
										?>
											<tr>
												<td width='80px' valign='top'><?php echo $ln['id_followup']; ?></td>
												<td width='80px' valign='top'><?php echo $id_case; ?></td>
												<td width='300px' valign='top'><div style='width: 300px; overflow: auto;'><?php echo $description; ?></div></td>
												<td width='80px' valign='top'><?php echo $type; ?></td>
												<td width='80px' valign='top'><?php echo $value; ?></td>
												<td width='200px' valign='top'><?php echo $vara; ?></td>
												<td width='80px' align='center' valign='top'><?php echo $dt; ?></td>
												<td width='70px' align='center' valign='top'><?php echo $hr; ?></td>
												<?php
												if ($sel_status == '4,6' || $sel_status == '5')
												{
												?>
												<td width='100px' align='center' valign='top'>
													<img src='img/robot2.png' width='24px' height='24px' title='Enviar Novamente Para o Robô' style='cursor:pointer;' onclick="fc_submit_st('<?php echo $ln['id_followup']; ?>', 0);" /> &nbsp;&nbsp;&nbsp;&nbsp;
													<img src='img/hand.png' width='24px' height='24px' title='Fazer Manualmente' style='cursor:pointer;' onclick="fc_submit_st('<?php echo $ln['id_followup']; ?>', 7);"  />
												</td>
												<?php
												}
												?>
											</tr>
										<?php	
											$cont++;
										}
										?>
										</table>
									</div>
									<?php	
								}
								
								echo "<br />Total: " . $cont;	
							}
							?>
						</div>
						
						<input type="hidden" name='id_followup' value='' />
						<input type="hidden" name='st' value='' />
					</form>
				</td>
			</tr>
		</table>
		<form action="relatorio.php" method="POST" name='frm_exit'>
			<input type="hidden" name='logoff' value='S' />
		</form>
		<?php
			if ($robo != '' && $sel_status == '0')										
			{
		?>
				<script>
					setTimeout(fc_submit_rel, 60000);
				</script>
		<?php
			}
		?>	
    </body>
</html>	
