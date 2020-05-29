<?php
//
// Show filters form
//
?>
<!--//FT inclus?o do javascript abaixo bem como a tag <fielset> e <legend> para o efeito de filtro-->
<script language="javascript">
$(document).ready(function()
{
	$("#subpanel").click(function()
	{
		$("#toppanel").slideToggle();	
		var el = $("#shText");  
		var state = $("#shText").html();
		state = (state == 'Mostrar Filtro' ? '<span id="shText">Ocultar Filtro</span>' : '<span id="shText">Mostrar Filtro</span>');					
		el.replaceWith(state);
		
		if( $('#fil_mais').is(':visible') ) {
			$('#fil_menos').show();
			$('#fil_mais').hide();
			$('#info_box_filtro').css("border", "1px solid #cccccc");
		} else {
			$('#fil_menos').hide();
			$('#fil_mais').show();
			$('#info_box_filtro').css("border", "0px solid #cccccc");
		}
	});
});

function reset_filter()
	{
		document.getElementById('pasta_f').value='';
		document.getElementById('parte_f').value='';
		document.getElementById('status_f').value='';
		document.getElementById('case_owner').value='';
		document.getElementById('case_period').value='all';
		document.getElementById('condicao_f').value='';
		document.getElementById('diversos_f').value='';
		document.getElementById('processo_f').value='';
		document.getElementById('comar_f').value='';
		document.getElementById('type_f').value='';
		document.getElementById('stopday_f').value='';
		document.getElementById('state_f').value='';
		document.getElementById('cliente_f').value='';
		document.getElementById('input_case_vara').value='';
		document.getElementById('select_comp_0').value=0;
		document.getElementById('acao_f').value='';
		$("#last_f").attr('checked',false);
                competencias(0,1,'');
	}
	function fc_show_last()
	{
		if($("#type_f").val()=="")
		{
			$("#td_last_f").hide();
		}
		else
		{
			$("#td_last_f").show();
		}
	}
</script>
<?php 
if(basename($_SERVER['PHP_SELF'])== 'index.php')
{
	$dpl_menos = "";
	$dpl_mais  = "none";
	$dpl_filtro = "Ocultar Filtro";
	$filter_title = _T('input_filter_case_title');
}
else
{
	$dpl_menos = "none";
	$dpl_mais  = "";
	$dpl_filtro = "Mostrar Filtro";
	$filter_title = _T('input_filter_list_title');
}

?>
<form action="listcases.php" method="get">
	<fieldset class="info_box_filtro" id="info_box_filtro">
		<div id="subpanel" class="prefs_column_menu_head_filtro">
			<span><?php echo $filter_title; ?> </span>
			<a href="#" id="toggle" style="font-size:7pt; text-decoration: none; float:right" >
				<img border="0" src="images/filtro/next_t.png" id="fil_menos" name="fil_menos" style="cursor:pointer; display: <?php echo $dpl_menos; ?>;"/>
				<img border="0" src="images/filtro/next_b.png" id="fil_mais" name="fil_mais" style="cursor:pointer; display: <?php echo $dpl_mais; ?>;" />
				<span id="shText" ><?php echo $dpl_filtro; ?></span>
			</a>
		</div>
		<div id="toppanel" style="display:<?php echo $dpl_menos; ?>;" >
			<table width="100%">
				<tr>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_pasta'); ?> <br/>
						<input type="text" id="pasta_f" name="pasta_f" value="<?php echo $_GET['pasta_f']; ?>" style="width:95%"/>
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_parte'); ?> <br/>
						<input type="text" id="parte_f" name="parte_f" value="<?php echo $_GET['parte_f']; ?>" style="width:95%" onkeyup="this.value=this.value.toUpperCase()" />
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_status'); ?> <br/>
					<?php 
						$stt = array("open"=>"Ativo","closed"=>"Inativo","suspended"=>"Suspenso",""=>"Todos");
					?>
						<select id="status_f" name="status_f" style="width:95%" >
							<?php
							if(!isset($_GET['status_f'])){
								$_GET['status_f'] = 'open';
							}
							foreach($stt as $st => $s){
								$sel = ($_GET['status_f']==$st ? 'selected' : '');
								echo '<option value="'.$st.'" '.$sel.'>'.$s.'</option>';
							}
								
							?>
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_owner'); ?><br />
						<select id="case_owner" name="case_owner" style="width:95%">
							<option value="" ></option>
							<?php
							foreach ($types_owner as $t => $foo) 
							{
								$sel = ($prefs['case_owner'] == $t ? ' selected="selected" ' : '');
								?>
								<option value="<?php echo $t; ?>" <?php echo $sel; ?> ><?php echo _T('case_filter_owner_option_' . $t); ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_processo'); ?> <br/>
						<input type="text" id="processo_f" name="processo_f" value="<?php echo $_GET['processo_f']; ?>" style="width:95%"/>
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_comar'); ?> <br/>
						<input type="text" id="comar_f" name="comar_f" value="<?php echo $_GET['comar_f']; ?>" style="width:95%"/>
					</td>			
					
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_state'); ?> <br/>
						<select id="state_f" name="state_f" style="width:95%" >
							<option value="" ></option>
							<?php
							$q_state = "SELECT DISTINCT state as state FROM lcm_case order by state asc";
							$result = lcm_query($q_state);

							while($row = lcm_fetch_array($result)) {
								$sel = ($_GET['state_f'] == $row['state'] ? 'selected' : '');
								echo "<option value=" . $row['state'] . " " . $sel . " >" . $row['state'] . "</option>";
							}
							?>
						</select>
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_time'); ?><br/>
						<select id="case_period" name="case_period" style="width:95%">
							<option value="all" ></option>
							<?php
							//altera??o provis?ria da p?gina inicial
							if(basename($_SERVER['PHP_SELF'])=='index.php')
							{
								echo '<option value="all" ></option>';
								
							}
							else
							{
								foreach ($types_period as $key => $val) {
									$sel = isSelected($prefs['case_period'] == $val);
									?>
									<option value="<?php echo $val; ?>" <?php echo $sel; ?> ><?php echo _T('case_filter_period_option_' . $key); ?></option>
									<?php
								}
							}
							$q_dates = "SELECT DISTINCT " . lcm_query_trunc_field('date_creation', 'year') . " as year
										FROM lcm_case as c, lcm_case_author as a
										WHERE c.id_case = a.id_case AND " . $q_owner . " order by year asc";

							$result = lcm_query($q_dates);

							while($row = lcm_fetch_array($result)) {
								$sel = isSelected($prefs['case_period'] == $row['year']);
								if($_GET['case_period'])
								$sel = ($_GET['case_period']==$row['year']? 'selected': '');
									?>
									<option value="<?php echo $row['year']; ?>" <?php echo $sel; ?> ><?php echo _T('case_filter_period_option_year', array('year' => $row['year'])); ?></option>
									<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>	
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_cliente_name'); ?> <br/>
						<select id="cliente_f" name="cliente_f" style="width:95%" >
							<option value="" ></option>
							<?php
							$q_types = "SELECT DISTINCT o.id_cliente, o.name FROM lcm_cliente as o order by o.name asc";
							$result = lcm_query($q_types);

							while($row = lcm_fetch_array($result)) {
								$sel = ($_GET['cliente_f'] == $row['id_cliente'] ? 'selected' : '');
								echo "<option value=" . $row['id_cliente'] . " " . $sel . " >" . ((strlen($row['name']) > 20)? substr($row['name'], 0, 20).'...' : $row['name']) . "</option>";
							}
							?>
						</select>
					</td>
					
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_condicao'); ?> <br/>
						<select id="condicao_f" name="condicao_f" style="width:95%" >
							<option value="" ></option>
							<?php
							$q_types = "SELECT DISTINCT id_keyword as id_keyword, title FROM lcm_keyword where id_keyword in (70,71)";
							$result = lcm_query($q_types);

							while($row = lcm_fetch_array($result)) {
								$sel = ($_GET['condicao_f'] == $row['id_keyword'] ? 'selected' : '');
								echo "<option value=" . $row['id_keyword'] . " " . $sel . " >" . $row['title'] . "</option>";
							}
							?>
						</select>
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_type'); ?> <br/>
						<select id="type_f" name="type_f" style="width:95%" onchange="fc_show_last()">
							<option value="" ></option>
							<?php
							$q_types = "SELECT DISTINCT type  as types FROM lcm_followup";
							$result = lcm_query($q_types);

							while($row = lcm_fetch_array($result)) {
								$arr_acionamentos[$row['types']] = trim(_T('kw_followups_' . $row['types'] . '_title'));
							}
							//Ordenando pelo alfabeto
							natsort($arr_acionamentos);
							foreach($arr_acionamentos as $key_acto => $val_acto){
								$sel = ($_GET['type_f'] == $key_acto ? 'selected' : '');
								echo "<option value=" . $key_acto . " " . $sel . " >" . $val_acto . "</option>";
							}
							
							?>
						</select>
					</td>
					<td class="normal_text_filtro" style="width:15%">
						<span style="display:<?php //echo $_GET['last_f']=='on' ? '' : 'none' ; ?>" id="td_last_f" ><br/><?php echo _T('fu_input_last_text'); ?> 
						
						<?php echo _Ti('fu_input_type'); ?><input type="checkbox" id="last_f" name="last_f" <?php echo $_GET['last_f']=='on' ? 'checked' : '' ; ?> style="width:30px;"/>
						</span>
					</td>
				</tr>
				<tr>
					<td class="normal_text_filtro"><?php echo _Ti('case_input_legal_reason'); ?> <br/>
						<select id="acao_f" name="acao_f" style="width:95%" >
							<option value="" ></option>
							<?php
							$q_types = "SELECT DISTINCT legal_reason as legal_reason FROM lcm_case order by legal_reason asc";
							$result = lcm_query($q_types);

							while($row = lcm_fetch_array($result)) {
								$sel = ($_GET['acao_f'] == $row['legal_reason'] ? 'selected' : '');
								echo "<option value=" . str_replace(' ', '_|_', $row['legal_reason']) . " " . $sel . " >" . $row['legal_reason'] . "</option>";
							}
							?>
						</select>
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('input_filter_case_vara'); ?> <br/>
						<?php 
							selecao_competencia('','vara_f','hidden');
						?>
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('fu_input_stopday_text'); ?><br />
						<input type="text" id="stopday_f" name="stopday_f" value="<?php echo $_GET['stopday_f']; ?>" style="width:30px;"/>
						<span style="font-size:8pt">(<?php echo _T('fu_input_stopday'); ?>)</span>
					</td>
					<td class="normal_text_filtro"><?php echo _Ti('fu_input_diversos'); ?> <br/>
						<select id="diversos_f" name="diversos_f" style="width:95%" >
							<option value="" ></option>
							<?php
							$q_types = "SELECT DISTINCT id_keyword as id_keyword, title FROM lcm_keyword where id_keyword in (149)";
							$result = lcm_query($q_types);

							while($row = lcm_fetch_array($result)) {
								$sel = ($_GET['diversos_f'] == $row['id_keyword'] ? 'selected' : '');
								echo "<option value=" . $row['id_keyword'] . " " . $sel . " >" . $row['title'] . "</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 100%;" colspan="4">
						<table width="100%">
							<tr>
								<td width="80%"></td>
								<td align="right"><input type="button" value="" class="simple_form_btn_limpar" style=" float:right;" onclick="reset_filter()" /></td>
								<td align="left"><button name="submit" type="submit" value="submit" class="simple_form_btn" style=" height: 20px; cursor:pointer;"><?php echo _T('button_filter'); ?></button></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</fieldset>
</form>