<?php

	$robo = $_POST['robo'];
	
	include_once "robo.php";

	if ($id_followup > 0)
	{
		switch($robo)
		{
			case 'LEG':
				?>
				<form method="POST" name="frm_robo" id="frm_robo">
					<input type="hidden" name="txt_id_followup" value="<?php echo $id_followup; ?>">
					<input type="hidden" name="resultado" value="N">
					<input type="hidden" name="cont" value="<?php echo $cont; ?>"> 
					<input type="hidden" name="robo" value="">
					<table width="300px">
						<tr><td>C&oacute;digo Interno:</td><td><input name="cod_int" type="text" value="<?php echo $arr_dado['cod_int']; ?>" /></td></tr>
						<tr><td>Vara:</td><td><input name="vara" type="text" value="<?php echo $arr_dado['vara']; ?>"/></td></tr>
						<tr><td>Data:</td><td><input name="data" type="text" value="<?php echo $arr_dado['data']; ?>"/></td></tr>
						<tr><td>Atividade:</td><td><input name="atividade" type="text" value="<?php echo utf8_encode($arr_dado['atividade']); ?>"/></td></tr>
						<tr><td>Coment&aacute;rios:</td><td><input name="comentario" type="text" value="<?php echo $arr_dado['comentario']; ?>"/></td></tr>
						<tr>
							<td colspan="2" align="center">
								<br />
								<input type="button" value="Positivo" style="color: white; background: green;" onclick="fc_submit('P')">&nbsp;&nbsp;&nbsp;
								<input type="button" value="Negativo" style="color: white; background: red;"   onclick="fc_submit('N')">
							</td>
						</tr>								
					</table>
				</form>						
				<?php
				break;
				
			case 'SRS':
				?>
				<form method="POST" name="frm_robo" id="frm_robo">
					<input type="hidden" name="txt_id_followup" value="<?php echo $id_followup; ?>">
					<input type="hidden" name="resultado" value="N">
					<input type="hidden" name="cont" value="<?php echo $cont; ?>"> 
					<input type="hidden" name="robo" value="">
					<table width="300px">                                
						<tr><td>C&oacute;digo Contrato:</td><td><input name="loan_no" type="text" value="<?php echo $arr_dado['loan_no']; ?>" /></td></tr>
						<tr><td>Remarks:</td><td><input name="remarks" type="text" value="<?php echo ($arr_dado['remarks']); ?>"/></td></tr>
						<tr><td>C&oacute;digo do Evento:</td><td><input name="evento" type="text" value="<?php echo ($arr_dado['evento']); ?>"/></td></tr>                                
						<tr>
							<td colspan="2" align="center">
								<br />
								<input type="button" value="Positivo" style="color: white; background: green;" onclick="fc_submit('P')">&nbsp;&nbsp;&nbsp;
								<input type="button" value="Negativo" style="color: white; background: red;"   onclick="fc_submit('N')">
							</td>
						</tr>
					</table>
				</form>				
				<?php	
				break;
		}		
		?>
		<br><br>
		<table>
			<tr>
				<td colspan="2" align="center">
					<br><br>
					<table cellpadding="2" cellspacing="0" border="1">
						<tr style="background: yellow;">
							<td align="center" width='40px'><b>Status</b></td>
							<td align="center" width='40px'><b>Qtd.</b></td>
						</tr>
						<?php
						foreach($arr_st as $st)
						{
						?>
						<tr>
							<td align="center"><?php echo $st['st']; ?></td>
							<td align="center"><?php echo $st['qtd']; ?></td>
						</tr>											
						<?php
						}
						?>
					</table>
				</td>
			</tr>	
		</table>		
		<?php
	}
	else
	{
		echo "N";
	}
	exit;
?>