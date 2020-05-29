<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');

$admin 	 = ($GLOBALS['author_session']['status']=='admin');
$manager = ($GLOBALS['author_session']['status']=='manager');
$title_onfocus = '';

$ac = get_ac_app($_GET['app']);

if (! $ac['w'])
	die("access denied");

if (empty($_SESSION['errors'])) {
	// Clear form data
	$_SESSION['form_data'] = array('ref_edit_app' => ( _request('ref') ? _request('ref') : $_SERVER['HTTP_REFERER']) );
	$_SESSION['authors'] = array();

	if ($_GET['app']>0) {
		$_SESSION['form_data']['id_app'] = intval(_request('app'));

		// Fetch the details on the specified appointment
		$q="SELECT *
			FROM lcm_app
			WHERE id_app=" . _session('id_app');

		$result = lcm_query($q);

		if ($row = lcm_fetch_array($result)) {
			foreach($row as $key=>$value) {
				$_SESSION['form_data'][$key] = $value;
			}

			// Get appointment participants
			$q = "SELECT au.id_author, au.name_first, au.name_middle, au.name_last
				FROM lcm_author_app as ap, lcm_author au
				WHERE ap.id_author = au.id_author
					AND id_app=" . _session('id_app') . "
				ORDER BY au.name_first, au.name_middle, au.name_last";
			$result = lcm_query($q);

			while ($row = lcm_fetch_array($result))
				$_SESSION['authors'][$row['id_author']] = $row;

			// Check the access rights
			//FT incluÃ­do manager
			if (! ($admin || $manager || isset($_SESSION['authors'][ $GLOBALS['author_session']['id_author'] ])))
				die( htmlentities('VocÃª nÃ£o estÃ¡ envolvido neste agendamento!'));
				
		} else die("NÃ£o hÃ¡ essa nomeaÃ§Ã£o!");

	} else {
		// This is new appointment
		$_SESSION['form_data']['id_app'] = 0;
		
		// New appointment created from case
		if (!empty($_GET['case']))
			$_SESSION['form_data']['id_case'] = intval(_request('case'));

		// New appointment created from followup
		if (($id_followup = intval(_request('followup')))) { 
			$_SESSION['form_data']['id_followup'] = $id_followup;

			if (! _session('id_case')) {
				$result = lcm_query("SELECT id_case FROM lcm_followup WHERE id_followup = $id_followup");

				if ($row = lcm_fetch_array($result))
					$_SESSION['form_data']['id_case'] = $row['id_case'];
			}
		}

		// Setup default values
		$_SESSION['form_data']['title'] = _T('title_app_new');

		if (_request('time')) {
			$time = rawurldecode(_request('time'));
		} else {
			$time = date('Y-m-d H:i:s');
		}

		$_SESSION['form_data']['start_time'] = $time;
		$_SESSION['form_data']['end_time']   = $time;
		$_SESSION['form_data']['reminder']   = $time;

		// erases the "New appointment" when focuses (taken from Spip)
		$title_onfocus = " onfocus=\"if(!title_antifocus) { this.value = ''; title_antifocus = true;}\" "; 
		
		// Set author as appointment participants
		$q = "SELECT id_author,name_first,name_middle,name_last
			FROM lcm_author
			WHERE id_author=" . $GLOBALS['author_session']['id_author'];
		$result = lcm_query($q);

		while ($row = lcm_fetch_array($result))
			$_SESSION['authors'][$row['id_author']] = $row;

	}

} else if ( array_key_exists('author_added',$_SESSION['errors']) || array_key_exists('author_removed',$_SESSION['errors']) ) {
	// Refresh appointment participants
	$q = "SELECT lcm_author.id_author,name_first,name_middle,name_last
		  FROM lcm_author_app,lcm_author
		  WHERE lcm_author_app.id_author=lcm_author.id_author
			AND id_app=" . $_SESSION['form_data']['id_app'] . "
		  ORDER BY name_first,name_middle,name_last";
	$result = lcm_query($q);
	$_SESSION['authors'] = array();
	while ($row = lcm_fetch_array($result))
		$_SESSION['authors'][$row['id_author']] = $row;
}

// [ML]Â not clean hack, fix "delete" option
if (! empty($_SESSION['errors'])) {
	if ($_SESSION['form_data']['hidden'])
		$_SESSION['form_data']['hidden'] = 'Y';
		
	//FT inserindo a mesma condiÃ§Ã£o para performed
	if ($_SESSION['form_data']['performed'])
		$_SESSION['form_data']['performed'] = 'Y';
}

if (_session('id_app', 0) > 0)
	lcm_page_start(_T('title_app_edit'), '', '', 'tools_agenda');
else
	lcm_page_start(_T('title_app_new'), '', '', 'tools_agenda');

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
$ac = get_ac_app($app, _session('id_case'));

$admin = $ac['a'];
$write = $ac['w'];
$edit  = $ac['e'];

$dis = ($edit ? '' : 'disabled="disabled"');

//FT criando o bloqueio (desabilitei)
/*if(	$_GET['performed']==1) {
	$dis = 'disabled="disabled';
}*/
?>

<form action="upd_app.php" method="post">
	<table class="tbl_usr_dtl" width="99%">

		<!-- Start time -->
		<tr>
<?php

	echo "<td>" . f_err_star('start_time') . _T('time_input_date_start') . "</td>\n";
	echo "<td>";

	$name = ($edit ? 'start' : '');
	echo get_date_inputs($name, _session('start_time'), false);
	echo ' ' . _T('time_input_time_at') . ' ';
	echo get_time_inputs($name, _session('start_time'));

	echo "</td>\n";

?>
		</tr>
		<!-- End time -->
		<tr>
<?php

	if ($prefs['time_intervals'] == 'absolute') {
		echo "<td>" . f_err_star('end_time') . _T('time_input_date_end') . "</td>\n";
		echo "<td>";

		$name = (($admin || ($edit && ($_SESSION['form_data']['end_time']=='0000-00-00 00:00:00'))) ? 'end' : '');
		echo get_date_inputs($name, $_SESSION['form_data']['end_time']);
		echo ' ';
		echo _T('time_input_time_at') . ' ';
		echo get_time_inputs($name, $_SESSION['form_data']['end_time']);

		echo "</td>\n";
	} else {
		echo "<td>" . f_err_star('end_time') . _T('app_input_time_length') . "</td>\n";
		echo "<td>";

		$name = (($admin || ($edit && ($_SESSION['form_data']['end_time']=='0000-00-00 00:00:00'))) ? 'delta' : '');
		$interval = ( ($_SESSION['form_data']['end_time']!='0000-00-00 00:00:00') ?
				strtotime($_SESSION['form_data']['end_time']) - strtotime($_SESSION['form_data']['start_time']) : 0);
		echo get_time_interval_inputs($name, $interval);

		echo "</td>\n";
	}

?>

		</tr>

		<!-- Reminder -->
		
<?php
	/*
	[ML] Removing this because it's rather confusing + little gain in usability.
	Might be good in the future if we send e-mail reminders, for example.
	//FT revelando o lembrete que jÃ¡ estava oculto
	*/
	echo "<tr>\n";

	if ($prefs['time_intervals'] == 'absolute') {
		echo "<td>" . f_err_star('reminder') . _T('app_input_reminder_time') . "</td>\n";
		echo "<td>";

		$name = (($admin || ($edit && ($_SESSION['form_data']['end_time']=='0000-00-00 00:00:00'))) ? 'reminder' : '');
		echo get_date_inputs($name, $_SESSION['form_data']['reminder']);
		echo ' ';
		echo _T('time_input_time_at') . ' ';
		echo get_time_inputs($name, $_SESSION['form_data']['reminder']);

		echo "</td>\n";
	} else {
		echo "<td>" . f_err_star('reminder') . _T('app_input_reminder_offset') . "</td>\n";
		echo "<td>";

		$name = (($admin || ($edit && ($_SESSION['form_data']['end_time']=='0000-00-00 00:00:00'))) ? 'rem_offset' : '');
		$interval = ( ($_SESSION['form_data']['end_time']!='0000-00-00 00:00:00') ?
				strtotime($_SESSION['form_data']['start_time']) - strtotime($_SESSION['form_data']['reminder']) : 0);
		echo get_time_interval_inputs($name, $interval);
		echo " " . _T('time_info_before_start');
		echo f_err_star('reminder');

		echo "</td>\n";
	}

	echo "</tr>\n";
	//echo "<pre>";
	//print_r($system_kwg);
	//echo "</pre>";
	
	
?>
		<script>
		$(document).ready(function(){
			agenda_title($("#agenda_type").val());
		});
			function agenda_title(valor){
				
				if(valor=="appointments04"){
					$("#prazo_selec").show();
					$("#prazo_selec").attr("disabled",false);
					
					$("#agenda_input").hide();
					$("#agenda_input").attr("disabled","disabled");
					
					$("#comp_selec").hide();
					$("#comp_selec").attr("disabled","disabled");
					
					$("#dilig_selec").hide();
					$("#dilig_selec").attr("disabled","disabled");
					
				}else if(valor=="appointments05"){
					$("#agenda_input").hide();
					$("#agenda_input").attr("disabled","disabled");
					
					$("#prazo_selec").hide();
					$("#prazo_selec").attr("disabled","disabled");
					
					$("#comp_selec").show();
					$("#comp_selec").attr("disabled",false);
					
					$("#dilig_selec").hide();
					$("#dilig_selec").attr("disabled","disabled");
				
				}else if(valor=="appointments09"){
					$("#agenda_input").hide();
					$("#agenda_input").attr("disabled","disabled");
					
					$("#prazo_selec").hide();
					$("#prazo_selec").attr("disabled","disabled");
					
					$("#comp_selec").hide();
					$("#comp_selec").attr("disabled","disabled");
					
					$("#dilig_selec").show();
					$("#dilig_selec").attr("disabled",false);
				
				}else{
					$("#agenda_input").show();
					$("#agenda_input").attr("disabled",false);
					
					$("#prazo_selec").hide();
					$("#prazo_selec").attr("disabled","disabled");
					
					$("#comp_selec").hide();
					$("#comp_selec").attr("disabled","disabled");
					
					$("#dilig_selec").hide();
					$("#dilig_selec").attr("disabled","disabled");
				}
			}
			
			
		</script>
		<!-- //FT Invertendo o tÃ­tulo com as opÃ§Ãµes -->
		<!-- Appointment type -->
		<?php 
		//echo "<pre>";
		//print_r($system_kwg);
		//echo "</pre>";
		?>
		<tr><td><?php echo _T('app_input_type'); ?></td>
			<td><select <?php echo $dis; ?> name="type" size="1" class="sel_frm" id="agenda_type" onchange="agenda_title(this.value); ">
			<?php

			global $system_kwg;

			if ($_SESSION['form_data']['type']){
				$default_app = $_SESSION['form_data']['type'];
			}else{
				$default_app = $system_kwg['appointments']['suggest'];
			}
			//agendando uma diligÃªncia
			if($_GET['app_title']!=''){
				echo $default_app = $_GET['app_title'];
			}
			foreach($system_kwg['appointments']['keywords'] as $kw) {
				$sel = ($kw['name'] == $default_app ? ' selected="selected"' : '');
				echo "<option value='" . $kw['name'] . "'" . "$sel>" . _T(remove_number_prefix($kw['title'])) . "</option>\n";
			}

			?>
			</select></td></tr>
			
			
		<!-- Appointment title -->
		<tr><td valign="top"><?php echo f_err_star('title') . _T('app_input_title'); ?></td>
			<td>
			<select <?php echo $dis; ?> name="title" size="1" class="sel_frm" id="prazo_selec" style="display:none; width:380px; height:23px" >
			<?php
				
				if ($_SESSION['form_data']['title']){	
					$default_app = $_SESSION['form_data']['title'];
					echo "<option value='" . $_SESSION['form_data']['title'] . "'" . "$sel>" . $_SESSION['form_data']['title'] . "</option>\n";
				}else{
					$default_app = $system_kwg['_titleprazo']['suggest'];
				}

				foreach($system_kwg['_titleprazo']['keywords'] as $kw) {
					$sel = ($kw['name'] == $default_app ? ' selected="selected"' : '');
					echo "<option value='" . $kw['title'] . "'" . "$sel>" . _T(remove_number_prefix($kw['title'])) . "</option>\n";
				}

			?>
			</select>
			<select <?php echo $dis; ?> name="title" size="1" class="sel_frm" id="comp_selec" style="display:none; width:380px; height:23px" >
			<?php
			
				if ($_SESSION['form_data']['title']){
					$default_app = $_SESSION['form_data']['title'];
					echo "<option value='" . $_SESSION['form_data']['title'] . "'" . "$sel>" . $_SESSION['form_data']['title'] . "</option>\n";
				} else {
					$default_app = $system_kwg['_titlecomp']['suggest'];
				}

				foreach($system_kwg['_titlecomp']['keywords'] as $kw) {
					$sel = ($kw['name'] == $default_app ? ' selected="selected"' : '');
					echo "<option value='" . $kw['title'] . "'" . "$sel>" . _T(remove_number_prefix($kw['title'])) . "</option>\n";
				}

			?>
			</select>
			<select <?php echo $dis; ?> name="title" size="1" class="sel_frm" id="dilig_selec" style="display:none; width:380px; height:23px" >
			<?php
			
				if ($_SESSION['form_data']['title']){
					$default_app = $_SESSION['form_data']['title'];
					echo "<option value='" . $_SESSION['form_data']['title'] . "'" . "$sel>" . $_SESSION['form_data']['title'] . "</option>\n";
				} else {
					$default_app = $system_kwg['_titledilig']['suggest'];
				}

				foreach($system_kwg['_titledilig']['keywords'] as $kw) {
					$sel = ($kw['name'] == $default_app ? ' selected="selected"' : '');
					echo "<option value='" . $kw['title'] . "'" . "$sel>" . _T(remove_number_prefix($kw['title'])) . "</option>\n";
				}

			?>
			</select>
			<input type="text" <?php echo $title_onfocus . $dis; ?> name="title" size="50" id="agenda_input" style="display:none" value="<?php echo clean_output($_SESSION['form_data']['title']); ?>" />
			</td></tr>

			<?php
			
			$teste_corresp = trim($_SESSION['form_data']['type']) == 'appointments11' ? "" : "display: none";
			
			#echo $teste_corresp;exit;
			
			?>
			
		
			<tr style="<?php echo $teste_corresp; ?>">
				<td valign="top">
					<?php echo _T('Correspondente:'); ?>
				</td>
				<td valign="top">
					<input type="text" name="nome_corresp" size="30" id="agenda_input" value="<?php echo clean_output($_SESSION['form_data']['nome_corresp']); ?>" />
				</td>
			</tr>
			<tr style="<?php echo $teste_corresp; ?>">
				<td valign="top">
					<?php echo _T('Valor:'); ?>
				</td>
				<td valign="top">
					<input type="text" name="valor" size="10" id="agenda_input" value="<?php echo clean_output($_SESSION['form_data']['valor']); ?>" />
				</td>
			</tr>
			<tr style="<?php echo $teste_corresp; ?>">
				<td valign="top">
					<?php echo _T('Data Correspondência:'); ?>
				</td>
				<td valign="top">
					<input type="text" name="data_corresp" size="8" id="agenda_input" value="<?php echo clean_output($_SESSION['form_data']['data_corresp']); ?>" />
				</td>
			</tr>
		
			
		<!-- Appointment description -->
		<tr><td valign="top"><?php echo _T('app_input_description'); ?></td>
			<td><textarea <?php echo $dis; ?> name="description" rows="5" cols="40" class="frm_tarea"><?php
			echo clean_output(_session('description')) . "</textarea></td></tr>\n";

		// Appointment participants - authors
		echo "\t\t<tr><td valign=\"top\">";
		echo _T('app_input_authors');
		echo "</td><td>";
		if (count($_SESSION['authors'])>0) {
			$q = '';
			$author_ids = array();
			foreach($_SESSION['authors'] as $author) {
				$q .= ($q ? ', ' : '');
				$author_ids[] = $author['id_author'];
				$q .= get_person_name($author);

				if ($author['id_author'] != $author_session['id_author'])
					$q .= '&nbsp;(<label for="id_rem_author' . $author['id_author'] . '"><img src="images/jimmac/stock_trash-16.png" width="16" height="16" alt="Remove?" title="Remove?" /></label>&nbsp;<input type="checkbox" id="id_rem_author' . $author['id_author'] . '" name="rem_author[]" value="' . $author['id_author'] . '" />)'; // TRAD

				$q .= "<br />\n";

			}
			echo "\t\t\t$q\n";
		}
		// List rest of the authors to add
/*		$q = "SELECT lcm_author.id_author,lcm_author.name_first,lcm_author.name_middle,lcm_author.name_last
			FROM lcm_author
			LEFT JOIN lcm_author_app
			ON (lcm_author.id_author=lcm_author_app.id_author AND id_app=" . $_SESSION['form_data']['id_app'] . ")
			WHERE id_app IS NULL";
*/
		
		$q = "SELECT id_author,name_first,name_middle,name_last
			FROM lcm_author " .
			(count($author_ids) ? " WHERE id_author NOT IN (" . join(',',$author_ids) . ")" : "") . " AND status!='trash' 
			ORDER BY name_first,name_middle,name_last";
		$result = lcm_query($q);

		echo '<select name="author">' . "\n";
		echo '<option selected="selected" value="0"> ... </option>' . "\n";

		while ($row = lcm_fetch_array($result)) {
			echo "<option value=\"" . $row['id_author'] . '">'
				. get_person_name($row)
				. "</option>\n";
		}
		echo "</select>\n";
		//FT inserido o nome do botÃ£o "Adicionar"
		echo "<button name=\"submit\" type=\"submit\" value=\"add_author\" class=\"simple_form_btn\">" . 'Adicionar' . "</button>\n"; // TRAD
		echo "</td></tr>\n";
		
		/* //FT Ocultando o "adicionar clientes"
		// Appointment participants - adversos
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
		//FT inserido o nome do botÃ£o "Adicionar"
		echo "<button name=\"submit\" type=\"submit\" value=\"add_adverso\" class=\"simple_form_btn\">" . 'Adicionar' . "</button>\n"; // TRAD
		echo "</td></tr>\n";
		*/
		//FT criando o javascript para exibir o campo de descriÃ§Ã£o do cumprimento
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
			
		//FT inserido novo campo para descriÃ§Ã£o do cumprimento
		echo "<tr><td id='perf_desc_text' valign='top' style='display:none'>";
		echo _T('app_input_perf_desc');
		echo "</td>";
		echo "<td id='perf_desc' style='display:none'>";
		echo '<textarea id="frm_tarea" name="perf_desc" rows="3" cols="40" class="frm_tarea" ></textarea>';
		echo "</td></tr>\n";
		echo "</table>\n";
		//FT Criando o checkbox do cumprimento da atividade
			echo '<p class="normal_text">';
		if(	$_GET['performed']==1) {
			$checked = ($_SESSION['form_data']['performed'] == 'Y' ? ' checked="checked" ' : '');
			echo '<input type="checkbox" ' . $checked . ' name="performed" onclick="fc_show_cumprir();" />';
			echo '<label for="box_delete">' . _T('app_info_cumprir') . '</label>';
		} else {
			// Delete appointment
			if (_session('id_app', 0)) {
				// $checked = ($this->getDataString('hidden') == 'Y' ? ' checked="checked" ' : '');
				$checked = ($_SESSION['form_data']['hidden'] == 'Y' ? ' checked="checked" ' : '');

				echo '<input type="checkbox"' . $checked . ' name="hidden" id="box_delete" />';
				echo '<label for="box_delete">' . _T('app_info_delete') . '</label>';
			}
		}
			echo "</p>\n";
			
		// Submit buttons
		echo '<button name="submit" type="submit" value="adddet" class="simple_form_btn">' . _T('button_validate') . "</button><br /><br />\n";

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