<table align="center" class="sub_title_tb">
	<tr>
		<td align="left"><?php echo $tw[0]; ?></td>
		<td align="right"><?php echo $cntdo; ?></td>
	</tr>
</table>
<table align="center" class="content_form">
<?php
if($TIPOPET!="")
{
	$displ = $_POST['hid_enviar']==7?'block':'none';
	$q = mysql_query("SELECT * FROM tp_inputs_tb where tipo_id = '" . $TIPOPET . "' ORDER BY input_order, id_input",$conexao1);
	if(mysql_num_rows($q)>0)
	{
		echo "<tr>";
		$n = 0;
		$onFuncoes = "";
		while($w = mysql_fetch_array($q))
		{
			$onFuncoes .= $w['input_focus']!='' ? " onfocus='" . $w['input_focus'] 	. "' " : "";
			$onFuncoes .= $w['input_load'] 		? " onload='" . $w['input_load'] 	. "' " : "";
			$onFuncoes .= ($w['input_blur'] 	? (" onblur='" . $w['input_blur'] 	. "' "): "");
			
			$n++;
			$tag = "campo" . $w['id_input'];
			$dd = $w['input_val'];			
			if($w['input_tipo']=='SELECT')
			{
				echo "<td colspan='" . $w['input_cols'] . "' class='td_title'><label>" . $w['input_title'] . "</label><br>";
				echo "<select type='text' id='" . $tag . "' name='" . $tag . "' class='input-default' style='width:" . $w['input_width'] . "px' " . $onFuncoes . " obrigatorio='" . $w['input_req'] . "' descricao='" . strtoupper($w['input_title']) . "' >";
					if($w['input_db']!="")
					{
						$input_db = explode("_|_",$w['input_db']);
						$where = $input_db[2] ? $input_db[2] : '1=1';
						$qsel = mysql_query("SELECT * FROM " . $input_db[0] . " WHERE $where ORDER BY " . $input_db[1] . " asc ",$conexao1);
						echo "<option></option>";
						while($wsel = mysql_fetch_array($qsel))
						{
							echo "<option value='" . $wsel[2] . "' ident='" . $wsel[0] . "' " . ( trim($dados[$w['input_val']])==trim($wsel[$input_db[1]]) ? 'selected' : '') . " >" . $wsel[$input_db[1]] . "</option>";
						}
					}
					else
					{
						$qsel = mysql_query("SELECT * FROM tp_dados_tb where id_input = '" . $w['id_input'] . "' ORDER BY nome_dados asc ",$conexao1);
						echo "<option>" . ($_POST["$tag"]!="" ? $_POST["$tag"] : $dados[$dd]) . "</option>";
						while($wsel = mysql_fetch_array($qsel))
						{
							echo "<option value='" . $wsel['nome_dados'] . "' ident='" . $wsel['id_dados'] . "' >" . $wsel['nome_dados'] . "</option>";
						}
					}
					
				echo "</select><br>" . fc_botoes($w['id_input'],$displ) . "</td>";
				echo "<script>$(function() { $('#$tag').combobox(); });</script>";
			}
			elseif($w['input_tipo']=='TEXT')
			{
				echo "<td colspan='" . $w['input_cols'] . "' class='td_title' ><label>" . $w['input_title'] . "</label><br><input type='text' id='" . $tag . "' name='" . $tag . "' value='" . ($_POST["$tag"]!="" ? $_POST["$tag"] : $dados[$dd]) . "' class='input-default' style='width:" . $w['input_width'] . "px' alt='" . $w['input_alt'] . "' " . $onFuncoes . " obrigatorio='" . $w['input_req'] . "' descricao='" . strtoupper($w['input_title']) . "'/>
						<br>" . fc_botoes($w['id_input'],$displ) . "</td>";
				//utilizar para o nome da petição
				if($w['nomepet']=="Y"){
					echo "<input type='hidden' name='nomepet' id='nomepet' value='".$tag."' />";
				}
			}
			elseif($w['input_tipo']=='RADIO')
			{
				//Exemplo abaixo - tem que ser alterado posteriormente
				echo "<td colspan='" . $w['input_cols'] . "' class='td_title'>
						<label>Tipo Pessoa:	</label><br>
						<div 	style='height:23px; width: 200px;text-align:center'>
							<label>Física:&nbsp;</label>
							<input type='radio' name='TIPOPES' value='cpf' class='input-default' " . ($dados[$w['input_val']] == 'F' ? 'checked' : '') . " />
							<label>&nbsp;&nbsp;Jurídica:&nbsp;</label>
							<input type='radio' name='TIPOPES' value='cnpj' class='input-default' " . ($dados[$w['input_val']] == 'J' ? 'checked' : '') . " />
						</div></td>";
			}
			elseif($w['input_tipo']=='TITLE')
			{
				echo "</tr><tr>";
				echo "<td colspan='" . $w['input_cols'] . "' class='td_title' ><div>&nbsp;</div><p align='center' class='input-default' style='width:" . $w['input_width'] . "px; height:20px; margin-left:0px; padding-top:3px; margin-bottom:0px'><b>" . $w['input_title'] . "</b>
					" . fc_botoes($w['id_input'],$displ) . "</td>";
				echo "</tr>";
				$n=0;
			}
			elseif($w['input_tipo']=='BOTTOM')
			{
				echo "<td colspan='" . $w['input_cols'] . "' class='td_title' ><label>" . $w['input_title'] . "</label><br><input type='text' id='" . $tag . "' name='" . $tag . "' value='" . ($_POST["$tag"]!="" ? $_POST["$tag"] : $dados[$dd]) . "' class='input-default' style='color:666; width:" . $w['input_width'] . "px' alt='" . $w['input_alt'] . "' " . $onFuncoes . " readonly='readonly' />
						" . fc_botoes($w['id_input'],$displ) . "</td>";
			}
			elseif($w['input_tipo']=='TEXTAREA')
			{
				echo "<td colspan='" . $w['input_cols'] . "' class='td_title' ><label>" . $w['input_title'] . "</label><br><input type='text' id='" . $tag . "' name='" . $tag . "' value='" . ($_POST["$tag"]!="" ? $_POST["$tag"] : $dados[$dd]) . "' class='input-default' style='width:" . $w['input_width'] . "px' alt='" . $w['input_alt'] . "' " . $onFuncoes . " obrigatorio='" . $w['input_req'] . "' descricao='" . strtoupper($w['input_title']) . "' onfocus='fc_textarea(this,\"" . $w['input_title'] . "\");' carregar='0'/>
					<br>". fc_botoes($w['id_input'],$displ) . "</td>";
			}
			
			$cols = $w['input_rols'];
			for($i=1;$i<=$cols;$i++)
			{
				echo "</tr><tr>";
				$n=0;
			}
			
			if($n==3)
			{
				echo "</tr><tr>";
				$n=0;
			}
			//Limpa as funcões
			$onFuncoes = "";
		}
		echo "</tr>";
	}
}
?>
</table>
<table align="center" width="650px" >
	<tr>
		<td height="30px" align="right"><button type="button" value="" class="input-default cls_campos" onclick="fc_inputs('I',this)" style="height:25px; display:<?php echo $displ; ?>">+ Campos</button></td>
	</tr>
	<tr>
		<td height="30px" align="center"><button type="button" onclick="EnviarDados('form.php','2','<?php echo $_POST['TIPOPET']; ?>')" style="height:25px" class="input-default">Enviar Dados</button></td>
	</tr>
</table>
