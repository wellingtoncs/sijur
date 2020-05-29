<?php
	
	$robo =  'SRS';
	
	include_once "../robo.php";
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
            function fc_submit(res)
            {
                //document.getElementById('div_form').style.display = 'none';
                //document.getElementById('div_proc').style.display = ''; 
                document.frm_robo.resultado.value = res;
                document.frm_robo.submit();
            }
        </script> 
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    </head>    
    <body>
        <table width="100%"> 
            <tr>
                <td width="80px"> 
                    <img src="img/robot.png" />
                </td>   
                <td>
                    Rob&ocirc;: EA002<br />
                    Atualiza&ccedil;&atilde;o dos eventos jur&iacute;dicos no SRS<br />
                    Criado em Abril de 2015<br />
					Contato: julianophp@gmail.com; alexandre.cavalcanti@bb.com.br (81) 9924-5330
                </td>    
            </tr>
            <tr>
                <td colspan="2"><hr></td>
            </tr>
            <tr>
                <td colspan="2">
					<?php
					if ($id_followup > 0)
					{
					?>				
                    <div style="padding: 5px;" id="div_form">
                        <form method="POST" name="frm_robo">
							<input type="hidden" name="txt_id_followup" value="<?php echo $id_followup; ?>">
                            <input type="hidden" name="resultado" value="N">
                            <input type="hidden" name="cont" value="<?php echo $cont; ?>"> 
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
                    </div>
					<div id="div_proc" style="padding: 5px; display: none;">Processando...</div>
					<?php
					}
					else
					{
					?>
						<div id="div_wait" style="padding: 5px;">Aguardando...</div>
						<script>
							setTimeout(function(){
								window.location = 'index.php';
							}, 5000);
						</script>						
					<?php	
					}
					?>					
                </td>    
            </tr>    
        </table>   
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
    </body>    
</html>    