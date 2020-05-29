<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	include("inc/conectar.php");
	include("inc/functions.php");
	
	
	$q_dados  = " SELECT ";
	$q_dados .= " c.cod_cli,";
	$q_dados .= " c.nmcont,";
	$q_dados .= " c.nomecli,";
	$q_dados .= " IF(substring(c.cpfcnpj, 9, 4) = '0001',c.cpfcnpj,right(trim(c.cpfcnpj), 11)) as cpfcnpj,";
	$q_dados .= " c.endresi,";
	$q_dados .= " c.nrresi,";
	$q_dados .= " c.compresi,";
	$q_dados .= " c.bairesi,";
	$q_dados .= " c.cidresi,";
	$q_dados .= " c.estresi,";
	$q_dados .= " c.cepresi,";
	$q_dados .= " c.endcom,";
	$q_dados .= " c.nrcom,";
	$q_dados .= " c.compcom,";
	$q_dados .= " c.baicom,";
	$q_dados .= " c.cidcom,";
	$q_dados .= " c.estcom,";
	$q_dados .= " c.cepcom,";
	$q_dados .= " b.marca,";
	$q_dados .= " b.modelo,";
	$q_dados .= " b.anofab,";
	$q_dados .= " b.placa,";
	$q_dados .= " b.cor,";
	$q_dados .= " b.chassi,";
	$q_dados .= " f.id_fil,";
	$q_dados .= " f.cid_fil,";
	$q_dados .= " n.nmcont,";
	$q_dados .= " n.outro,";
	$q_dados .= " tipopess,";
	$q_dados .= " c.idneg,";
	$q_dados .= " c.infoad2,";
	$q_dados .= " n.cod_age";
	$q_dados .= " FROM cadastros_tb as c";
	$q_dados .= " LEFT JOIN bens_tb as b on c.nmcont=b.nmcont";
	$q_dados .= " JOIN filial_tb as f on f.id_fil=c.id_fil";
	$q_dados .= " JOIN negocios_tb as n on c.nmcont=n.nmcont";
	$q_dados .= " WHERE c.cod_cad=" . $cod . "";
	$q_dados .= " GROUP BY c.nmcont";
	
	$m_dados  = mysql_query($q_dados) or die(mysql_error());
	$dados    = mysql_fetch_assoc($m_dados);
	
	$q_parc  = " SELECT";
	$q_parc .= " vlrparc,";
	$q_parc .= " count(numparc) as tt_parc,";
	$q_parc .= " max(numparc) as max_parc,";
	$q_parc .= " DATE_FORMAT(max(data_ven), '%d/%m/%Y') as max_data,";
	$q_parc .= " min(numparc) as min_parc,";
	$q_parc .= " DATE_FORMAT(min(data_ven), '%d/%m/%Y') as min_data,";
	$q_parc .= " nmcont";
	$q_parc .= " FROM receber_tb";
	$q_parc .= " WHERE nmcont='" . $dados['nmcont'] ."' AND (stparc IN ('0','26','105'))";
	$q_parc .= " ORDER BY numparc";
	$myparc  = mysql_query($q_parc);
	$parcs   = mysql_fetch_array($myparc);
	
	//-----Função de criar pastas ----------------------
	function MyFolder($nmcont)
	{
		$x = trim($nmcont);
		$x = substr($x, -2, 2);
		return $x;
	}
	//--------------------------------------------------

?>
<meta 	http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<html>
<head>
	<title>Gerador de Petições</title>
	<link rel="stylesheet" href="css/base/jquery.ui.all.css">
	<link rel="stylesheet" href="css/css.css">
	<script type="text/javascript" src="js/jquery-1.7.2.js">		   	</script>
	<script type="text/javascript" src="js/jquery.ui.core.js">		   	</script>
	<script type="text/javascript" src="js/jquery.ui.widget.js">	   	</script>
	<script type="text/javascript" src="js/jquery.ui.mouse.js">			</script>
	<script type="text/javascript" src="js/jquery.ui.autocomplete.js">	</script>
	<script type="text/javascript" src="js/jquery.ui.button.js">		</script>
	<script type="text/javascript" src="js/jquery.ui.position.js">		</script>
	<script type="text/javascript" src="js/jquery-ui.js">				</script>
	<script type="text/javascript" src="js/jquery.ui.dialog.js">		</script>
	<script type="text/javascript" src="js/jquery.meio.mask.js">	 	</script>
	<script type="text/javascript" src="js/default.js">					</script>
</head>
<body style="overflow-y: scroll;">
<form name="form_iniciais" action="index.php" method="POST">
<div id="demo0" align="center">
	<div class="tabela_form">
		<table align="center" width="98%" style=" padding-top:5px">
			<tr>
				<td class="input-default" style="text-align:center; height:25px; font-size:10pt"><b>DADOS DA PETIÇÃO</b></td>
			</tr>
		</table>
		<table align="center" width="898px">
			<tr>
				<td align="left"  class="td_title"><label>MODELO DE PETIÇÃO:		</label><br><select type="text" id="TIPOPET" 	name="TIPOPET" 	class="input-default" onfocus="javascript:document.form_iniciais.submit();"><?php echo fc_select("tp_tipo_tb",$_POST['TIPOPET'],"tipo_id","tipo_nome","$cod_usu",$conexao1); ?></select></td>
				<td align="right" class="td_title"><input type="button" class="input-default cls_edit" value="Editar" onclick="fc_edit(this.value)" /></td>
			</tr>
		</table>
		<table align="center" width="890px" style="border:1px solid #cccccc; padding: 10px 0px 10px 0px">
			<?php
				$q = mysql_query("SELECT * FROM tp_inputs_tb where tipo_id = '" . $_POST['TIPOPET'] . "'",$conexao1);
				if(mysql_num_rows($q)>0)
				{
					echo "<tr>";
					$n = 0;
					while($w = mysql_fetch_array($q))
					{
						$n++;
						$tag = "CAMPO" . $w['id_input'];
						$dd = $w['input_val'];						
						if($w['input_tipo']=='SELECT')
						{
							echo "<td colspan='" . $w['input_cols'] . "' class='td_title'><label>" . $w['input_title'] . "</label><br>";
							echo "<select type='text' id='" . $tag . "' name='" . $tag . "' class='input-default' style='width:" . $w['input_width'] . "px' " . $w['input_func'] . " obrigatorio='" . $w['input_req'] . "' descricao='" . ucfirst(strtolower($w['input_title'])) . "' >";
								if($w['input_db']!="")
								{
									$input_db = explode("_|_",$w['input_db']);
									$qsel = mysql_query("SELECT * FROM " . $input_db[0] . " ORDER BY " . $input_db[1] . " asc ",$conexao1);
									echo "<option></option>";
									while($wsel = mysql_fetch_array($qsel))
									{
										echo "<option value='" . $wsel[$input_db[1]] . "' >" . $wsel[$input_db[1]] . "</option>";
									}

								}
								else
								{
									$qsel = mysql_query("SELECT * FROM tp_dados_tb where id_input = '" . $w['id_input'] . "' ORDER BY nome_dados asc ",$conexao1);
									echo "<option>" . ($_POST["$tag"]!="" ? $_POST["$tag"] : $dados[$dd]) . "</option>";
									while($wsel = mysql_fetch_array($qsel))
									{
										echo "<option value='" . $wsel['id_dados'] . "' >" . $wsel['nome_dados'] . "</option>";
									}
								}
								
							echo "</select><button type='button' class='button_del' style='margin-left:25px; display:none;' onclick='fc_del_input(this.value)' value='" . $w['id_input'] . "'>X</button></td>";
							echo "<script>$(function() { $('#$tag').combobox(); });</script>";
						}
						elseif($w['input_tipo']=='TEXT')
						{
							echo "<td colspan='" . $w['input_cols'] . "' class='td_title' ><label>" . $w['input_title'] . "</label><br><input type='text' id='" . $tag . "' name='" . $tag . "' value='" . ($_POST["$tag"]!="" ? $_POST["$tag"] : $dados[$dd]) . "' class='input-default' style='width:" . $w['input_width'] . "px' alt='" . $w['input_alt'] . "' " . $w['input_func'] . "/><button type='button' class='button_del' style='display:none;' onclick='fc_del_input(this.value)' value='" . $w['id_input'] . "'>X</button></td>";
						}
						elseif($w['input_tipo']=='RADIO')
						{
							//Exemplo abaixo - tem que ser alterado posteriormente
							echo "<td colspan='" . $w['input_cols'] . "' class='td_title'><label>Tipo Pessoa:			</label><br><div 	style='height:23px; width: 200px;text-align:center'><label>Física:&nbsp;</label><input type='radio' name='TIPOPES' value='cpf' class='input-default' checked /><label>&nbsp;&nbsp;Jurídica:&nbsp;</label><input type='radio' name='TIPOPES' value='cnpj' class='input-default' /></div></td>";
							//echo "<td colspan='" . $w['input_cols'] . "' class='td_title' ><label>" . ucfirst(strtolower($w['input_title'])) . "</label><br><input type='text' id='" . $tag . "' name='" . $tag . "' value='" . $_POST["$tag"] . "' class='input-default' style='width:" . $sty . "px' alt='" . $w['input_alt'] . "' " . $w['input_func'] . "/><button type='button' class='button_del' onclick='fc_del_input(this.value)' value='" . $w['id_input'] . "'>X</button></td>";
						}
						elseif($w['input_tipo']=='TITLE')
						{
							echo "</tr><tr>";
							echo "<td colspan='" . $w['input_cols'] . "' class='td_title' ><div>&nbsp;</div><p align='center' class='input-default' style='width:" . $w['input_width'] . "px; height:25px;'><b>" . $w['input_title'] . "</b><button type='button' class='button_del' style='display:none;' onclick='fc_del_input(this.value)' value='" . $w['id_input'] . "'>X</button></p></td>";
							echo "</tr>";
							$n=0;
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
					}
					echo "</tr>";
				}
			?>
		</table>
		<table align="center" width="650px" >
			<tr>
				<td height="30px" align="right"><button type="button" value="" class="input-default cls_campos" onclick="fc_inputs()" style="height:25px; display:none">+ Campos</button></td>
			</tr>
			<tr>
				<td height="30px" align="center"><button type="button" onclick="EnviarDados()" style="height:25px" class="input-default">Enviar Dados</button></td>
			</tr>
		</table>
	</div>
</div>

<input type="hidden" name="cod" 	id="cod"	 value="<?php echo $cod ? $cod : $_POST['cod']; ?>">
<input type='hidden' name='url_dir' id='url_dir' value='<?php echo $urlfiles . "/" . $dados['cod_cli'] . "/" . MyFolder($dados['nmcont']) . "/" . $dados['nmcont']; ?>' />
<input type="hidden" name="nomecli"	id="nomecli" value="<?php echo $dados['nomecli']; ?>">
</form>

<!--Crinado Inputs dinâmicos-->
<div>
	<div id="dialog_inputs" title="Novo Campo" style="display:none">
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
				<table id="tb_addSel" width="100%" style="border: 1px solid #D0D0D0; display:none">
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
								<option value="0">Nao</option>
								<option value="1">Sim</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">Associar com o Banco de Dados:</td>
						<td align="left">
							<select name="db_col" id="db_col" class="input-default" style="width:120px">
								<?php
								echo "<option></option>";
								foreach($dados as $k => $v)
								{
									echo "<option>" . $k . "</option>";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">Associar com Base existente:</td>
						<td align="left">
							<select name="tbBase" id="tbBase" class="input-default" style="width:120px">
								<option></option>
								<option value="advogados_tb_|_nome_adv">Advogados</option>
								<option value="filial_tb_|_nome_fil">Filial</option>
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
	</div>
</div>
</body>
</html>