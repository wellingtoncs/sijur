<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');

include_lcm('inc_obj_adverso');
include_lcm('inc_obj_cliente');
include_lcm('inc_obj_case');
include_lcm('inc_obj_fu');

$id_case = 0;

// Don't clear form data if comming back from upd_case with errors
if (! isset($_SESSION['form_data']))
	$_SESSION['form_data'] = array();

if (empty($_SESSION['errors'])) {
	// Set the returning page, usually, there should not be, therefore
	// it will send back to "case_det.php?case=NNN" after update.
	$_SESSION['form_data']['ref_edit_case'] = _request('ref');

	$id_case = intval(_request('case'));

	if ($id_case) {
		// Check access rights
		if (!allowed($id_case,'e')) die(_T('error_no_edit_permission'));

		$q = "SELECT *
			FROM lcm_case
			WHERE id_case = $id_case";

		$result = lcm_query($q);

		if ($row = lcm_fetch_array($result)) {
			foreach ($row as $key => $value) {
				$_SESSION['form_data'][$key] = $value;
			}
		}

		$_SESSION['form_data']['admin'] = allowed($id_case,'a');

	} else {
		// Set default values for the new case
		$_SESSION['form_data']['date_assignment'] = date('Y-m-d H:i:s');
		$_SESSION['form_data']['public'] = (int) (read_meta('case_default_read') == 'yes');
		$_SESSION['form_data']['pub_write'] = (int) (read_meta('case_default_write') == 'yes');

		if (isset($GLOBALS['case_default_status']) && $GLOBALS['case_default_status'] == 'draft')
			$_SESSION['form_data']['status'] = 'draft';
		else
			$_SESSION['form_data']['status'] = 'open';

		$_SESSION['form_data']['admin'] = true;

	}
}

$attach_adverso = 0;
$attach_cliente = 0;

if (! $id_case) {
	$attach_adverso = intval(_request('attach_adverso', 0));
	$attach_cliente    = intval(_request('attach_cliente', 0));

	$attach_adverso = intval(_session('attach_adverso', $attach_adverso));
	$attach_cliente    = intval(_session('attach_cliente', $attach_cliente));
}

if ($attach_adverso) {
	$adverso = new LcmClient($attach_adverso);

	// Leave empty if user did the error of leaving it blank
	if (! isset($_SESSION['form_data']['p_adverso']))
		$_SESSION['form_data']['p_adverso'] = $adverso->getName();
}

if ($attach_cliente) {
	$cliente = new LcmOrg($attach_cliente);

	// Leave empty if user did the error of leaving it blank
	if (! isset($_SESSION['form_data']['p_adverso']))
		$_SESSION['form_data']['p_adverso'] = $info['name'];
}


// Start page and title
if ($id_case)
	lcm_page_start(_T('title_case_edit'), '', '', 'cases_intro#edit');
else
	lcm_page_start(_T('title_case_new'), '', '', 'cases_intro#new');

// Show the errors (if any)
echo show_all_errors();

if ($attach_adverso || $attach_cliente)
	show_context_start();

if ($attach_adverso) {
	$query = "SELECT id_adverso, name_first, name_middle, name_last
				FROM lcm_adverso
				WHERE id_adverso = " . $attach_adverso;
	$result = lcm_query($query);
	while ($row = lcm_fetch_array($result))  // should be only once
		echo '<li style="list-style-type: none;">' . _Ti('fu_input_involving_adversos') . get_person_name($row) . "</li>\n";
	
}

if ($attach_cliente) {
	$query = "SELECT id_cliente, name
				FROM lcm_cliente
				WHERE id_cliente = " . $attach_cliente;
	$result = lcm_query($query);
	while ($row = lcm_fetch_array($result))  // should be only once
		echo '<li style="list-style-type: none;">' . _Ti('fu_input_involving_adversos') . $row['name'] . "</li>\n";
}

if ($attach_adverso || $attach_cliente)
	show_context_end();

// Start edit case form
echo '<form action="upd_case.php" method="post">' . "\n";

if (! $id_case) {
	if ($attach_cliente) {
		show_page_subtitle(_Th('title_cliente_view'), 'adversos_intro');

		$cliente = new LcmOrgInfoUI($attach_cliente);
		$cliente->printGeneral(false);
		$cliente->printCases();
		$cliente->printAttach();
	}

	if ($attach_adverso) {
		show_page_subtitle(_Th('title_adverso_view'), 'adversos_intro');

		$adverso = new LcmClientInfoUI($attach_adverso);
		$adverso->printGeneral(false);
		$adverso->printCases();
		$adverso->printAttach();
	}
	
	if ((! $attach_adverso) && (! $attach_cliente)) {
		//
		// Find or create an clienteanisation for case
		//
		if (read_meta('case_new_showcliente') == 'yes') {
			show_page_subtitle(_Th('title_cliente_view'), 'adversos_intro');
	
			echo '<p class="normal_text">';
			echo '<input type="checkbox"' . isChecked(_session('add_cliente')) .  'name="add_cliente" id="box_new_cliente" onclick="display_block(\'new_cliente\', \'flip\')" />';
			echo '<label for="box_new_cliente">' . _T('case_button_add_cliente') . '</label>';
			echo "</p>\n";
	
			// Open box that hides this form by default
			echo '<div id="new_cliente" ' . (_session('add_cliente') ? '' : ' style="display: none;"') . '>';
	
			echo "<div style='overflow: hidden; width: 100%;'>";
			echo '<div style="float: left; text-align: right; width: 29%;">';
			echo '<p class="normal_text" style="margin: 0; padding: 4px;">' .  _Ti('input_search_cliente') . '</p>';
			echo "</div>\n";
	
			echo '<div style="float: right; width: 69%;">';
			echo '<p class="normal_text" style="margin: 0; padding: 4px;"><input type="text" autocomplete="off" name="clientesearchkey" id="clientesearchkey" size="25" />' . "</p>\n";
			echo '<span id="autocomplete-cliente-popup" class="autocomplete" style="position: absolute; visibility: hidden;"><span></span></span>';
			echo '</div>';
	
			echo '<div style="clear: right;"></div>';
	
			echo '<div id="autocomplete-cliente-data"></div>' . "\n";
			echo "</div>\n";
	
			echo '<div id="autocomplete-cliente-alt">';
			$cliente = new LcmOrgInfoUI();
			$cliente->printEdit();
			echo '</div>';
	
			echo "<script type=\"text/javascript\">
				autocomplete('clientesearchkey', 'autocomplete-cliente-popup', 'ajax.php', 'autocomplete-cliente-data', 'autocomplete-cliente-alt')
				</script>\n";
	
			echo "</div>\n"; // closes box that hides this form by default
		}

		//
		// For to find or create new adverso for case
		//
		show_page_subtitle(_Th('title_adverso_view'), 'adversos_intro');

		echo '<p class="normal_text">';
		echo '<input type="checkbox"' . isChecked(_session('add_adverso')) . 'name="add_adverso" id="box_new_adverso" onclick="display_block(\'new_adverso\', \'flip\')" />';
		echo '<label for="box_new_adverso">' . _T('case_button_add_adverso') . '</label>';
		echo "</p>\n";

		// Open box that hides this form by default
		echo '<div id="new_adverso" ' . (_session('add_adverso') ? '' : ' style="display: none;"') . '>';

		echo "<div style='overflow: hidden; width: 100%;'>";
		echo '<div style="float: left; text-align: right; width: 29%;">';
		echo '<p class="normal_text" style="margin: 0; padding: 4px;">' .  _Ti('input_search_adverso') . '</p>';
		echo "</div>\n";

		echo '<div style="float: right; width: 69%;">';
		echo '<p class="normal_text" style="margin: 0; padding: 4px;"><input type="text" name="adversosearchkey" id="adversosearchkey" size="25" />' . "</p>\n";
		echo '<span id="autocomplete-adverso-popup" class="autocomplete" style="visibility: hidden;"><span></span></span>';
		echo '</div>';

		echo '<div style="clear: right;"></div>';

		echo '<div id="autocomplete-adverso-data"></div>' . "\n";
		echo "</div>\n";

		echo '<div id="autocomplete-adverso-alt">';
		$adverso = new LcmClientInfoUI();
		$adverso->printEdit();
		echo '</div>';

		echo "<script type=\"text/javascript\">
			autocomplete('adversosearchkey', 'autocomplete-adverso-popup', 'ajax.php', 'autocomplete-adverso-data', 'autocomplete-adverso-alt')
			</script>\n";

		echo "</div>\n"; // closes box that hides this form by default
	}
}

if (! $id_case) {
	//
	// Find case (show only if new case)
	//FT incluindo o _Ti('show_page_subtitle_info') no lugar de "Informações
	show_page_subtitle(_Ti('show_page_subtitle_info'), 'cases_intro'); // TRAD

	echo "<div style='overflow: hidden; width: 100%;'>";
	echo '<div style="float: left; text-align: right; width: 29%;">';
	echo '<p class="normal_text" style="margin: 0; padding: 4px;">' . _Ti('input_search_case') . '</p>';
	echo "</div>\n";
	
	echo '<div style="float: right; width: 69%;">';
	echo '<p class="normal_text" style="margin: 0; padding: 4px;"><input type="text" autocomplete="off" name="casesearchkey" id="casesearchkey" size="25" />' . "</p>\n";
	echo '<span id="autocomplete-case-popup" class="autocomplete" style="position: absolute; visibility: hidden;"><span></span></span>';
	echo '</div>';
	
	echo '<div style="clear: right;"></div>';
	
	echo '<div id="autocomplete-case-data"></div>' . "\n";
	echo "</div>\n";
}

echo '<div id="case_data">';
	
$obj_case = new LcmCaseInfoUI($id_case);
$obj_case->printEdit();

echo "</div>\n"; /* div case_data */

echo "<script type=\"text/javascript\">
		autocomplete('casesearchkey', 'autocomplete-case-popup', 'ajax.php', 'autocomplete-case-data', 'case_data')
	</script>\n";

//
// Follow-up data (only for new case, not edit case)
//
if (! $id_case) {
	echo '<p class="normal_text">';
	echo '<input type="checkbox"' . isChecked(_session('add_fu')) . 'name="add_fu" id="box_new_followup" onclick="display_block(\'new_followup\', \'flip\')" />';
	echo '<label for="box_new_followup">' . "adicionar um andamento para o processo" . '</label>'; // TRAD
	echo "</p>\n";

	echo '<div id="new_followup" ' . (_session('add_fu') ? '' : ' style="display: none;"') . '>';

	show_page_subtitle(_Ti('show_page_subtitle_info_followup'), 'followups_intro'); // TRAD

	echo '<div id="autocomplete-fu-alt">';
	$fu = new LcmFollowupInfoUI();
	$fu->printEdit();
	echo "</div>\n";

	echo "</div>\n";
}

echo '<p><button name="submit" type="submit" value="submit" class="simple_form_btn">' . _T('button_validate') . "</button></p>\n";

echo '<input type="hidden" name="admin" value="' . $_SESSION['form_data']['admin'] . "\" />\n";
echo '<input type="hidden" name="ref_edit_case" value="' . $_SESSION['form_data']['ref_edit_case'] . "\" />\n";

echo "</form>\n\n";

// Reset error messages and form data
$_SESSION['errors'] = array();
$_SESSION['case_data'] = array(); // DEPRECATED
$_SESSION['form_data'] = array();

lcm_page_end();

?>