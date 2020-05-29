<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_contacts');
include_lcm('inc_obj_cliente');

$cliente = intval(_request('cliente'));

if (! ($cliente > 0)) {
	lcm_header("Location: listclientes.php");
	exit;
}

$q = "SELECT *
		FROM lcm_cliente
		WHERE id_cliente = $cliente";

$result = lcm_query($q);

if (! ($row = lcm_fetch_array($result))) 
	die("ERROR: There is no such clienteanisation in the database.");

lcm_page_start(_T('title_cliente_view') . ' ' . $row['name'], '', '', 'adversos_intro');

//
// Access control
//
$ac = get_ac_cliente($cliente);

if (! $ac['r'])
	die("Access denied");

	// Show tabs
	$groups = array(
		'general' => array('name' => _T('generic_tab_general'), 'tooltip' => _T('generic_subtitle_general')),
		// 'representatives' => _T('generic_tab_representatives'),
		'cases' => array('name' => _T('generic_tab_cases'), 'tooltip' => _T('cliente_subtitle_cases')),
		'attachments' => array('name' => _T('generic_tab_documents'), 'tooltip' => _T('cliente_subtitle_attachments')));

	$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'general' );
	show_tabs($groups,$tab,$_SERVER['REQUEST_URI']);

	if ($c = intval(_request('attach_case', 0))) {
		$q = "SELECT p_adverso
				FROM lcm_case
				WHERE id_case = " . $c;
		$result = lcm_query($q);

		while ($row1 = lcm_fetch_array($result)) {
			echo '<div class="sys_msg_box">';
			echo '<ul>';
			echo '<li>' . _Ti('cliente_info_created_attached')
				. '<a class="content_link" href="case_det.php?case=' . $c . '">' 
				. $row1['p_adverso'] 
				. "</a></li>\n";
			echo "</ul>\n";
			echo "</div>\n";
		}
	}

	switch ($tab) {
		//
		// Show clienteanisation general information
		//
		case 'general':
			echo '<fieldset class="info_box">';

			$obj_cliente = new LcmOrgInfoUI($cliente);
			$obj_cliente->printGeneral(true);

			if ($ac['e'])
				echo '<p><a href="edit_cliente.php?cliente=' . $row['id_cliente'] . '" class="edit_lnk">'
					. _T('cliente_button_edit')
					. "</a></p>\n";

			// [ML] Not useful
			// if ($GLOBALS['author_session']['status'] == 'admin')
			//	echo '<p><a href="export.php?item=cliente&amp;id=' . $row['id_cliente'] . '" class="exp_lnk">' . _T('export_button_cliente') . "</a></p>\n";

			echo "</fieldset>\n";

			break;

		//
		// Show clienteanisation representatives
		//
		case 'representatives' :
			echo '<fieldset class="info_box">';
			echo '<div class="prefs_column_menu_head">' . _T('cliente_subtitle_representatives') . "</div><br />\n";

			echo '<form action="add_cli_cliente.php" method="post">' . "\n";
			echo '<input type="hidden" name="cliente" value="' . $cliente . '" />' . "\n";

			// Show clienteanisation representative(s)
			$q = "SELECT cl.id_adverso, name_first, name_middle, name_last
					FROM lcm_adverso_cliente as clo, lcm_adverso as cl
					WHERE id_cliente = $cliente 
						AND clo.id_adverso = cl.id_adverso";
		
			$result = lcm_query($q);
			$show_table = false;
		
			if (lcm_num_rows($result)) {
				$show_table = true;

				echo '<table class="tbl_usr_dtl" width="100%">' . "\n";
				echo "<tr>\n";
				echo '<th class="heading">' . "#" . '</th>';
				echo '<th class="heading" width="99%">' . _Th('person_input_name') . '</th>';
				echo '<th class="heading">&nbsp;</th>';
				echo "</tr>\n";
			} else {
				// TODO info message?
			}

			$i = 0;
			while ($row = lcm_fetch_array($result)) {
				echo "<tr>\n";

				// ID
				echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">' . $row['id_adverso'] . '</td>';

				// Name of adverso
				echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '"><a href="adverso_det.php?adverso=' . $row['id_adverso'] . '" class="content_link">';
				echo get_person_name($row) . "</a></td>";

				// Delete association
				echo '<td nowrap="nowrap" class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">';

				echo '<label for="id_rem_cli_' . $row['id_adverso'] . '">';
				echo '<img src="images/jimmac/stock_trash-16.png" width="16" height="16" '
					. 'alt="' . _T('cliente_info_delete_adverso') . '" title="' . _T('cliente_info_delete_adverso') . '" />';
				echo '</label>&nbsp;';
				echo '<input type="checkbox" onclick="lcm_show(\'btn_delete\')" id="id_rem_cli_' .  $row['id_adverso'] . '" name="rem_adversos[]" value="' . $row['id_adverso'] . '" />';

				echo '</td>';
				echo "</tr>\n";
				$i++;
			}
		
			if ($show_table)
				echo "</table>";

			echo '<div align="right" style="visibility: hidden">';
			echo '<input type="submit" name="submit" id="btn_delete" value="' . _T('button_validate') . '" class="search_form_btn" />';
			echo "</div>\n";
		
			if ($ac['w'])
				echo "<p><a href=\"sel_cli_cliente.php?cliente=$cliente\" class=\"add_lnk\">" . _T('cliente_button_add_rep') . "</a></p>";

			echo "</form>\n";
			echo "</fieldset>";

			break;

		//
		// Show recent cases
		//
		case 'cases':

			$q = "SELECT clo.id_case, c.processo, c.p_adverso, c.date_creation, c.status, c.comarca 
					FROM lcm_case_adverso_cliente as clo, lcm_case as c
					WHERE id_cliente = $cliente
					AND clo.id_case = c.id_case ";

			// Sort cases by creation date
			$case_order = 'DESC';
			if (isset($_REQUEST['case_order']))
				if ($_REQUEST['case_order'] == 'ASC' || $_REQUEST['case_order'] == 'DESC')
					$case_order = $_REQUEST['case_order'];

			$q .= " ORDER BY c.date_creation " . $case_order;

			$result = lcm_query($q);
			$number_of_rows = lcm_num_rows($result);
			$list_pos = 0;

			if (isset($_REQUEST['list_pos']))
				$list_pos = $_REQUEST['list_pos'];

			if ($list_pos >= $number_of_rows)
				$list_pos = 0;

			// Position to the page info start
			if ($list_pos > 0)
				if (!lcm_data_seek($result,$list_pos))
					lcm_panic("Error seeking position $list_pos in the result");

			if (lcm_num_rows($result)) {
				echo '<fieldset class="info_box">' . "\n";
				show_page_subtitle(_T('cliente_subtitle_cases'), 'cases_participants');

				echo "<p class=\"normal_text\">\n";
				show_listcase_start();

				for ($cpt = 0; $row1 = lcm_fetch_array($result); $cpt++) {
					show_listcase_item($row1, $cpt);
				}

				show_listcase_end($list_pos, $number_of_rows);
				echo "</p>\n";
				echo "</fieldset>\n";
			}

			break;
		//
		// Organisation attachments
		//
		case 'attachments' :
			echo '<fieldset class="info_box">';
			show_page_subtitle(_T('cliente_subtitle_attachments'), 'tools_documents');

			echo "<p class=\"normal_text\">\n";
			echo '<form enctype="multipart/form-data" action="attach_file.php" method="post">' . "\n";
			echo '<input type="hidden" name="cliente" value="' . $cliente . '" />' . "\n";

			// List of attached files
			show_attachments_list('cliente', $cliente);

			// Attach new file form
			if ($ac['w']) {
				show_attachments_upload('cliente', $cliente);
				echo '<input type="submit" name="submit" value="' . _T('button_validate') . '" class="search_form_btn" />' . "\n";
			}

			echo "</form>\n";

			echo "</p>\n";
			echo '</fieldset>';

			break;

	}

// Show this in all tabs
echo '<p>';
echo '<a href="edit_case.php?case=0&amp;attach_cliente=' . $row['id_cliente'] . '" class="create_new_lnk">';
echo _T('case_button_new');
echo "</a>";
echo "</p>\n";

$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();
$_SESSION['cliente_data'] = array(); // DEPRECATED since 0.6.4

lcm_page_end();

?>
