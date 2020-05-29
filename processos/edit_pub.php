<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');

$admin 	 = ($GLOBALS['author_session']['status']=='admin');
$manager = ($GLOBALS['author_session']['status']=='manager');
$title_onfocus = '';

$ac = get_ac_pub($_GET['pub']);

if (! $ac['w'])
	die("access denied");

if (empty($_SESSION['errors'])) {
	// Clear form data
	$_SESSION['form_data'] = array('ref_edit_pub' => ( _request('ref') ? _request('ref') : $_SERVER['HTTP_REFERER']) );
	$_SESSION['authors'] = array();

	if ($_GET['pub']>0) {
		$_SESSION['form_data']['id_pub'] = intval(_request('pub'));

		// Fetch the details on the specified Publicação
		$q="SELECT *
			FROM lcm_pub
			WHERE id_pub=" . _session('id_pub');

		$result = lcm_query($q);

		if ($row = lcm_fetch_array($result)) {
			foreach($row as $key=>$value) {
				$_SESSION['form_data'][$key] = $value;
			}

			// Get Publicação participants
			$q = "	SELECT au.id_author, au.name_first, au.name_middle, au.name_last
					FROM lcm_author_pub as ap, lcm_author au
					WHERE ap.id_author = au.id_author
					AND id_pub=" . _session('id_pub') . "
					ORDER BY au.name_first, au.name_middle, au.name_last";
			$result = lcm_query($q);

			while ($row = lcm_fetch_array($result))
				$_SESSION['authors'][$row['id_author']] = $row;

			// Check the access rights
			//FT incluído manager
			if (! ($admin || $manager || isset($_SESSION['authors'][ $GLOBALS['author_session']['id_author'] ])))
				die( htmlentities('Você não está envolvido neste agendamento!'));
				
		} else die("Não há essa nomeação!");

	} else {
		// This is new Publicação
		$_SESSION['form_data']['id_pub'] = 0;

		
		// New Publicação created from case
		if (!empty($_GET['case']))
			$_SESSION['form_data']['id_case'] = intval(_request('case'));

		// New Publicação created from followup
		if (($id_followup = intval(_request('followup')))) { 
			$_SESSION['form_data']['id_followup'] = $id_followup;

			if (! _session('id_case')) {
				$result = lcm_query("SELECT id_case FROM lcm_followup WHERE id_followup = $id_followup");

				if ($row = lcm_fetch_array($result))
					$_SESSION['form_data']['id_case'] = $row['id_case'];
			}
		}

		// Setup default values
		//$_SESSION['form_data']['tribunal'] = _T('title_app_new');

		if (_request('time')) {
			$time = rawurldecode(_request('time'));
		} else {
			$time = date('Y-m-d H:i:s');
		}

		$_SESSION['form_data']['start_pub'] = $time;
		$_SESSION['form_data']['end_pub']   = $time;
		$_SESSION['form_data']['reminder']   = $time;
		// erases the "New Publicação" when focuses (taken from Spip)
		$title_onfocus = " onfocus=\"if(!title_antifocus) { this.value = ''; title_antifocus = true;}\" "; 
		
		// Set author as Publicação participants
		$q = "SELECT id_author,name_first,name_middle,name_last
			FROM lcm_author
			WHERE id_author=" . $GLOBALS['author_session']['id_author'];
		$result = lcm_query($q);

		while ($row = lcm_fetch_array($result))
			$_SESSION['authors'][$row['id_author']] = $row;

	}

} else if ( array_key_exists('author_added',$_SESSION['errors']) || array_key_exists('author_removed',$_SESSION['errors']) ) {
	// Refresh Publicação participants
	$q = "SELECT lcm_author.id_author,name_first,name_middle,name_last
		  FROM lcm_author_pub,lcm_author
		  WHERE lcm_author_pub.id_author=lcm_author.id_author
			AND id_pub=" . $_SESSION['form_data']['id_pub'] . "
		  ORDER BY name_first,name_middle,name_last";
	$result = lcm_query($q);
	$_SESSION['authors'] = array();
	while ($row = lcm_fetch_array($result))
		$_SESSION['authors'][$row['id_author']] = $row;
}

// [ML] not clean hack, fix "delete" option
if (! empty($_SESSION['errors'])) {
	if ($_SESSION['form_data']['hidden'])
		$_SESSION['form_data']['hidden'] = 'Y';
		
	//FT inserindo a mesma condição para performed
	if ($_SESSION['form_data']['performed'])
		$_SESSION['form_data']['performed'] = 'Y';
}

if (_session('id_pub', 0) > 0)
	lcm_page_start(_T('title_pub_edit'), '', '', 'tools_agenda');
else
	lcm_page_start(_T('title_pub_new'), '', '', 'tools_agenda');

if (_session('id_case', 0) > 0) {
	// Show a bit of background on the case
	show_context_start();
	show_context_case_title(_session('id_case'));
	show_context_case_involving(_session('id_case'));
	show_context_end();
}

// Show the errors (if any)
echo show_all_errors();

// Disable inputs when edit is not allowed for the field
$ac = get_ac_pub($pub, _session('id_case'));

$admin = $ac['a'];
$write = $ac['w'];
$edit  = $ac['e'];

$dis = ($edit ? '' : 'disabled="disabled"');

if ($admin || $manager){
	$readonly = "";
}else{
	$readonly = "readonly='readonly'";
}
		
//FT criando o bloqueio (desabilitei)
/*if(	$_GET['performed']==1) {
	$dis = 'disabled="disabled';
}*/
?>

<form action="upd_pub.php" method="post">
	<table class="tbl_usr_dtl" width="99%">

	<!-- Start time -->
	<!-- Publicação id_case -->
<?php
	if($_GET['new']=='yes'){
		
		$qp = mysql_query("SELECT processo, p_adverso FROM lcm_case where id_case = '". _session('id_case')."' ");
		$wp = mysql_fetch_array($qp);
		
		?>
		<input type="hidden" name="new" value="yes" />
		<input type="hidden" name="numero_processo" value="<?php echo $wp['processo']; ?>" />
		<tr>
			<td><?php echo _T('case_input_id'); ?></td>
			<td>
				<?php echo $_SESSION['form_data']['id_case']; ?><input type="hidden" name="id_case" size="50" value="<?php  echo $_SESSION['form_data']['id_case']; ?>" />
			</td>
		</tr>
		<?php
		echo "<td>" . f_err_star('start_pub') . _T('time_input_date_start') . "</td>\n";
		echo "<td>";
		$name = 'start';
		echo get_date_inputs($name, _session('start_pub'), false);
		echo ' ' . _T('time_input_time_at') . ' ';
		echo get_time_inputs($name, _session('start_pub'));
		echo "</td></tr>\n";
		
		?>

		<!-- //FT Invertendo o título com as opções -->
		<!-- Publicação type -->
		<tr><td><?php echo _T('pub_input_recorte'); ?></td>
			<td><input type="text" name="n_recorte" value="" /></td></tr>
		<!-- Publicação type -->
		<tr><td><?php echo _T('case_input_state'); ?></td>
			<td>
				<select name="jornal" >
					<option value="ALAGOAS">ALAGOAS</option>
					<option value="BAHIA">BAHIA</option>				
					<option value="CEARA">CEARA</option>				
					<option value="PARAIBA">PARAIBA</option>
					<option value="PERNAMBUCO">PERNAMBUCO</option>
					<option value="PIAUI">PIAUI</option>
				</select>
			</td>
		</tr>
			
		<!-- Publicação tribunal -->
		<tr><td valign="top"><?php echo f_err_star('tribunal') . _T('kwg__institutions_title'); ?></td>
			<td>
				<select name="tribunal" >
					<option value="TRIBUNAL DE JUSTICA">TRIBUNAL DE JUSTICA</option>
					<option value="JUIZADOS ESPECIAIS">JUIZADOS ESPECIAIS</option>
					<option value="TRIBUNAL REGIONAL DO TRABALHO">TRIBUNAL REGIONAL DO TRABALHO</option>
				</select>
			</td>
		</tr>
		<!-- Publicação publicacao -->
		<tr><td valign="top"><?php echo _T('app_input_description'); ?></td>
			<td><textarea name="publicacao" ></textarea></td>
			</tr>
		<?php 	
	
	}else{
		
	?>
		<tr>
			<td><?php echo _T('pub_input_id'); ?></td>
			<td><?php echo $_SESSION['form_data']['id_pub']; ?><input type="hidden" <?php echo $dis; echo $readonly; ?> name="id_pub" value="<?php echo $_SESSION['form_data']['id_pub']; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo _T('case_input_id'); ?></td>
			<td><input type="text" <?php echo $readonly; ?> name="pasta" size="50" value="<?php  echo $_SESSION['form_data']['id_case']; ?>" /></td>
		</tr>
		<?php
		echo "<td>" . f_err_star('start_pub') . _T('time_input_date_start') . "</td>\n";
		echo "<td>";

		if($author_session['id_author']==1 || $author_session['id_author'] ==76 || $author_session['id_author'] ==95 || $author_session['id_author'] ==96 || $author_session['id_author'] ==112){
			$name = ($edit ? 'start' : '');
		} else {
			$name = "";
		}
		
		echo get_date_inputs($name, _session('start_pub'), false);
		echo ' ' . _T('time_input_time_at') . ' ';
		echo get_time_inputs($name, _session('start_pub'));

		echo "</td></tr>\n";
		
		?>

		<!-- //FT Invertendo o título com as opções -->
		<!-- Publicação type -->
		<tr><td><?php echo _T('pub_input_recorte'); ?></td>
			<td><?php echo clean_output($_SESSION['form_data']['n_recorte']); ?><input type="hidden" name="n_recorte" value="<?php echo clean_output($_SESSION['form_data']['n_recorte']); ?>" /></td></tr>
		
		<!-- Publicação type -->
		<tr><td><?php echo _T('case_input_state'); ?></td>
			<td><?php echo clean_output($_SESSION['form_data']['jornal']); ?><input type="hidden" name="jornal" value="<?php echo clean_output($_SESSION['form_data']['jornal']); ?>" /></td></tr>
				
			
		<!-- Publicação tribunal -->
		<tr><td valign="top"><?php echo f_err_star('tribunal') . _T('kwg__institutions_title'); ?></td>
			<td><?php echo clean_output($_SESSION['form_data']['tribunal']); ?><input type="hidden" name="tribunal" value="<?php echo clean_output($_SESSION['form_data']['tribunal'])==""?'Sem dados':clean_output($_SESSION['form_data']['tribunal']); ?>" /></td></tr>

		<!-- Publicação publicacao -->
		<tr><td valign="top"><?php echo _T('app_input_description'); ?></td>
			<td><?php echo clean_output(_session('publicacao')); ?>
				<input type="hidden" name="publicacao" value="<?php echo clean_output(_session('publicacao')); ?>" /></td>
		</tr>
		<?php 
	}
		
?>
		
		<!-- End time>
		<tr>
<?php

	//if ($prefs['time_intervals'] == 'absolute') {
	//	echo "<td>" . f_err_star('end_pub') . _T('time_input_date_end') . "</td>\n";
	//	echo "<td>";
    //
	//	$name = (($admin || ($edit && ($_SESSION['form_data']['end_pub']=='0000-00-00 00:00:00'))) ? 'end' : '');
	//	echo get_date_inputs($name, $_SESSION['form_data']['end_pub']);
	//	echo ' ';
	//	echo _T('time_input_time_at') . ' ';
	//	echo get_time_inputs($name, $_SESSION['form_data']['end_pub']);
    //
	//	echo "</td>\n";
	//} else {
	//	echo "<td>" . f_err_star('end_pub') . _T('app_input_time_length') . "</td>\n";
	//	echo "<td>";
    //
	//	$name = (($admin || ($edit && ($_SESSION['form_data']['end_pub']=='0000-00-00 00:00:00'))) ? 'delta' : '');
	//	$interval = ( ($_SESSION['form_data']['end_pub']!='0000-00-00 00:00:00') ?
	//			strtotime($_SESSION['form_data']['end_pub']) - strtotime($_SESSION['form_data']['start_time']) : 0);
	//	echo get_time_interval_inputs($name, $interval);
    //
	//	echo "</td>\n";
	//}

?>

		</tr-->

		<!-- Reminder -->
		
<?php
	/*
	[ML] Removing this because it's rather confusing + little gain in usability.
	Might be good in the future if we send e-mail reminders, for example.
	//FT revelando o lembrete que já estava oculto
	*/
	//echo "<tr>\n";
    //
	//if ($prefs['time_intervals'] == 'absolute') {
	//	echo "<td>" . f_err_star('reminder') . _T('app_input_reminder_time') . "</td>\n";
	//	echo "<td>";
    //
	//	$name = (($admin || ($edit && ($_SESSION['form_data']['end_pub']=='0000-00-00 00:00:00'))) ? 'reminder' : '');
	//	echo get_date_inputs($name, $_SESSION['form_data']['reminder']);
	//	echo ' ';
	//	echo _T('time_input_time_at') . ' ';
	//	echo get_time_inputs($name, $_SESSION['form_data']['reminder']);
    //
	//	echo "</td>\n";
	//} else {
	//	echo "<td>" . f_err_star('reminder') . _T('app_input_reminder_offset') . "</td>\n";
	//	echo "<td>";
    //
	//	$name = (($admin || ($edit && ($_SESSION['form_data']['end_pub']=='0000-00-00 00:00:00'))) ? 'rem_offset' : '');
	//	$interval = ( ($_SESSION['form_data']['end_pub']!='0000-00-00 00:00:00') ?
	//			strtotime($_SESSION['form_data']['start_pub']) - strtotime($_SESSION['form_data']['reminder']) : 0);
	//	echo get_time_interval_inputs($name, $interval);
	//	echo " " . _T('time_info_before_start');
	//	echo f_err_star('reminder');
    //
	//	echo "</td>\n";
	//}
    //
	//echo "</tr>\n";
		
		// Publicação participants - authors
		//echo "\t\t<tr><td valign=\"top\">";
		//echo _T('app_input_authors');
		//echo "</td><td>";
		//if (count($_SESSION['authors'])>0) {
		//	$q = '';
		//	$author_ids = array();
		//	foreach($_SESSION['authors'] as $author) {
		//		$q .= ($q ? ', ' : '');
		//		$author_ids[] = $author['id_author'];
		//		$q .= get_person_name($author);
        //
		//		if ($author['id_author'] != $author_session['id_author'])
		//			$q .= '&nbsp;(<label for="id_rem_author' . $author['id_author'] . '"><img src="images/jimmac/stock_trash-16.png" width="16" height="16" alt="Remove?" title="Remove?" /></label>&nbsp;<input type="checkbox" id="id_rem_author' . $author['id_author'] . '" name="rem_author[]" value="' . $author['id_author'] . '" />)'; // TRAD
        //
		//		$q .= "<br />\n";
        //
		//	}
		//	echo "\t\t\t$q\n";
		//}
		// List rest of the authors to add
/*		$q = "SELECT lcm_author.id_author,lcm_author.name_first,lcm_author.name_middle,lcm_author.name_last
			FROM lcm_author
			LEFT JOIN lcm_author_app
			ON (lcm_author.id_author=lcm_author_app.id_author AND id_app=" . $_SESSION['form_data']['id_app'] . ")
			WHERE id_app IS NULL";
*/
		
		//$q = "SELECT id_author,name_first,name_middle,name_last
		//	FROM lcm_author " .
		//	(count($author_ids) ? " WHERE id_author NOT IN (" . join(',',$author_ids) . ")" : "") . " AND status!='trash' 
		//	ORDER BY name_first,name_middle,name_last";
		//$result = lcm_query($q);
        //
		//echo '<select name="author">' . "\n";
		//echo '<option selected="selected" value="0"> ... </option>' . "\n";
        //
		//while ($row = lcm_fetch_array($result)) {
		//	echo "<option value=\"" . $row['id_author'] . '">'
		//		. get_person_name($row)
		//		. "</option>\n";
		//}
		//echo "</select>\n";
		////FT inserido o nome do botão "Adicionar"
		//echo "<button name=\"submit\" type=\"submit\" value=\"add_author\" class=\"simple_form_btn\">" . 'Adicionar' . "</button>\n"; // TRAD
		//echo "</td></tr>\n";
		
		/* //FT Ocultando o "adicionar clientes"
		// Publicação participants - adversos
		echo '<tr><td valign="top">';
		echo _T('app_input_adversos');
		echo "</td><td>";

		$q = "SELECT c.id_adverso, c.name_first, c.name_middle, c.name_last, o.id_cliente, o.name
			FROM lcm_adverso as c, lcm_app_adverso_cliente aco
			LEFT JOIN lcm_cliente as o USING (id_cliente)
			WHERE id_app = " . _session('id_app', 0) . "
				AND c.id_adverso = aco.id_adverso
			ORDER BY c.name_first, c.name_middle, c.name_last, o.name";

		$result = lcm_query($q);
		$q = '';

		while ($row = lcm_fetch_array($result)) {
			// $q .= ($q ? ', ' : '');
			$q .= get_person_name($row) . ( ($row['name']) ? " of " . $row['name'] : ''); // TRAD
			$q .= '&nbsp;(<label for="id_rem_adverso' . $row['id_adverso'] . ':' . $row['id_cliente'] . '">';
			$q .= '<img src="images/jimmac/stock_trash-16.png" width="16" height="16" alt="Remove?" title="Remove?" /></label>&nbsp;';
			$q .= '<input type="checkbox" id="id_rem_adverso' . $row['id_adverso'] . ':' . $row['id_cliente'] . '" name="rem_adverso[]" value="' . $row['id_adverso'] . ':' . $row['id_cliente'] . '"/>)<br />';	// TRAD
		}

		echo "\t\t\t$q\n";
		
		// List rest of the adversos to add
		$q = "SELECT c.id_adverso, c.name_first, c.name_last, co.id_cliente, o.name
			FROM lcm_adverso AS c
			LEFT JOIN lcm_adverso_cliente AS co USING (id_adverso)
			LEFT JOIN lcm_cliente AS o ON (co.id_cliente = o.id_cliente)
			LEFT JOIN lcm_app_adverso_cliente AS aco ON (aco.id_adverso = c.id_adverso AND aco.id_app = " . _session('id_app', 0) . ")
			WHERE id_app IS NULL
			ORDER BY c.name_first, c.name_last, o.name";
		
		$result = lcm_query($q);

		echo '<select name="adverso">' . "\n";
		echo '<option selected="selected" value="0"> ... </option>' . "\n";

		while ($row = lcm_fetch_array($result)) {
			echo '<option value="' . $row['id_adverso'] . ':' . $row['id_cliente'] . '">'
				. get_person_name($row)
				. ($row['name'] ? ' of ' . $row['name'] : '') // TRAD
				. "</option>\n";
		}
		echo "</select>\n";
		//FT inserido o nome do botão "Adicionar"
		echo "<button name=\"submit\" type=\"submit\" value=\"add_adverso\" class=\"simple_form_btn\">" . 'Adicionar' . "</button>\n"; // TRAD
		echo "</td></tr>\n";
		*/
		//FT criando o javascript para exibir o campo de descrição do cumprimento
		echo "<script language='javascript'>
				function fc_show_cumprir(){
					if( $('#perf_desc_text').is(':visible') ) 
					{
						$('#perf_desc_text').hide();
						$('#perf_desc').hide();
						$('#frm_tarea').val('');
						
					} else {
						$('#perf_desc_text').show();
						$('#perf_desc').show();
						$('#perf_desc').val('');
					}
				}
			</script>";
			
		//FT inserido novo campo para descrição do cumprimento
		if(	$_GET['performed']==1 || $_GET['new']=='yes') {
			echo "<tr><td id='perf_desc_text' valign='top'>";
			echo _T('app_input_perf_desc');
			echo "</td>";
			echo "<td id='perf_desc'>";
			echo "<input type='hidden' name='performed' value='Y' />";
			echo '<textarea id="frm_tarea" name="perf_desc" rows="3" cols="40" class="frm_tarea" ></textarea>';
			echo "</td></tr>\n";
			echo "</table>\n";
		} else {
			echo "</table>\n";
		}
		//FT Criando o checkbox do cumprimento da atividade
		//	echo '<p class="normal_text">';
		//if(	$_GET['performed']==1) {
		//	$checked = ($_SESSION['form_data']['performed'] == 'Y' ? ' checked="checked" ' : '');
		//	echo '<input type="checkbox" ' . $checked . ' name="performed" onclick="fc_show_cumprir();" />';
		//	echo '<label for="box_delete">' . _T('pub_info_ler') . '</label>';
		//} else {
		//	// Delete Publicação
		//	if (_session('id_app', 0)) {
		//		// $checked = ($this->getDataString('hidden') == 'Y' ? ' checked="checked" ' : '');
		//		$checked = ($_SESSION['form_data']['hidden'] == 'Y' ? ' checked="checked" ' : '');
        //
		//		echo '<input type="checkbox"' . $checked . ' name="hidden" id="box_delete" />';
		//		echo '<label for="box_delete">' . _T('app_info_delete') . '</label>';
		//	}
		//}
		//	echo "</p>\n";
			
		// Submit buttons
		echo '<button name="submit" type="submit" value="adddet" class="simple_form_btn">' . _T('button_validate') . "</button><br /><br />\n";
		
		echo '<br /><a href="#" onClick="history.go(-1)" class="create_new_lnk" style="float:right">Voltar</a><br />';
	
		echo '<input type="hidden" name="id_app" value="' . _session('id_app', 0) . '" />' . "\n";
		echo '<input type="hidden" name="id_case" value="' . _session('id_case', 0) . '" />' . "\n";
		echo '<input type="hidden" name="id_followup" value="' . _session('id_followup', 0) . '" />' . "\n";

		// because of XHTML validation...
		if (_session('ref_edit_app')) {
			$ref_link = new Link(_session('ref_edit_app'));
			echo '<input type="hidden" name="ref_edit_app" value="' . $ref_link->getUrl() . '" />' . "\n";
		}

echo "</form>\n";

lcm_page_end();

// Clear the errors, in case user jumps to other 'edit' page
$_SESSION['errors'] = array();
$_SESSION['app_data'] = array(); // DEPRECATED since 0.7.0
$_SESSION['form_data'] = array();

?>