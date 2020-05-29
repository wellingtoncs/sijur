<?php

include('inc/inc.php');
include_lcm('inc_contacts');
include_lcm('inc_obj_adverso');

$adverso = intval(_request('adverso'));

if (! ($adverso > 0)) {
	lcm_header("Location: listadversos.php");
	exit;
}

$q = "SELECT *
		FROM lcm_adverso as c
		WHERE c.id_adverso = $adverso";

$result = lcm_query($q);

if (! ($row = lcm_fetch_array($result)))
	die("ERROR: There is no such adverso in the database.");

lcm_page_start(_T('title_adverso_view') . ' ' . get_person_name($row), '', '', 'adversos_intro');

		/* Saved for future use
			// Check for access rights
			if (!($row['public'] || allowed($adverso,'r'))) {
				die("You don't have permission to view this adverso details!");
			}
			$edit = allowed($adverso,'w');
		*/

		$edit = true;

		// Show tabs
		$groups = array(
			'general' => array('name' => _T('generic_tab_general'), 'tooltip' => _T('generic_subtitle_general')),
			// 'clienteanisations' => _T('generic_tab_cliente'),
			'cases' => array('name' => _T('generic_tab_cases'), 'tooltip' => _T('adverso_subtitle_cases')),
			'attachments' => array('name' => _T('generic_tab_documents'), 'tooltip' => _T('adverso_subtitle_attachments')));

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
				echo '<li>' . _Ti('adverso_info_created_attached')
					. '<a class="content_link" href="case_det.php?case=' . $c . '">' 
					. $row1['p_adverso'] 
					. "</a></li>\n";
				echo "</ul>\n";
				echo "</div>\n";
			}
		}

		switch ($tab) {
			case 'general':
				//
				// Show adverso general information
				//
				echo '<fieldset class="info_box">';
		
				$obj_adverso = new LcmClientInfoUI($row['id_adverso']);
				$obj_adverso->printGeneral();

				if ($edit)
					echo '<a href="edit_adverso.php?adverso=' .
					$row['id_adverso'] . '" class="edit_lnk">' .  _T('adverso_button_edit') . '</a>' . "\n";

				// [ML] Not useful
				// if ($GLOBALS['author_session']['status'] == 'admin')
				//	echo '<a href="export.php?item=adverso&amp;id=' . $row['id_adverso'] . '" class="exp_lnk">' . _T('export_button_adverso') . "</a>\n";

				echo '<br /><br />';
				echo "</fieldset>\n";
				break;
			case 'clienteanisations':
				//
				// Show adverso associated clienteanisations
				//
				echo '<fieldset class="info_box">';
				echo '<div class="prefs_column_menu_head">' . _T('adverso_subtitle_associated_cliente') . "</div>\n";

				echo '<form action="add_cliente_cli.php" method="post">' . "\n";
				echo '<input type="hidden" name="adverso" value="' . $adverso . '" />' . "\n";
				
				//
				// Show clienteanisation(s)
				//
				$q = "SELECT lcm_cliente.id_cliente,name
						FROM lcm_adverso_cliente,lcm_cliente
						WHERE id_adverso=$adverso
							AND lcm_adverso_cliente.id_cliente=lcm_cliente.id_cliente";
		
				$result = lcm_query($q);
				$show_table = false;

				if (lcm_num_rows($result)) {
					$show_table = true;

					echo '<table border="0" class="tbl_usr_dtl" width="100%">' . "\n";
					echo "<tr>\n";
					echo '<th class="heading">&nbsp;</th>';
					echo '<th class="heading">' . _Th('cliente_input_name') . '</th>';
					echo '<th class="heading">&nbsp;</th>';
					echo "</tr>\n";
				} else {
					// TODO info message?
				}

				$i = 0;
				while ($row1 = lcm_fetch_array($result)) {
					echo "<tr>\n";

					// Image
					echo '<td width="25" align="center" class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '"><img src="images/jimmac/stock_people.png" alt="" height="16" width="16" /></td>' . "\n";

					// Name of cliente
					echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '"><a style="display: block;" href="cliente_det.php?cliente=' . $row1['id_cliente'] .  '" class="content_link">' . $row1['name'] . "</a></td>\n";

					// Delete association
					echo '<td width="1%" nowrap="nowrap" class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">';

					echo '<label for="id_rem_cliente_' . $row1['id_cliente'] . '">';
					echo '<img src="images/jimmac/stock_trash-16.png" width="16" height="16" '
						. 'alt="' . _T('adverso_info_delete_cliente') . '" title="' . _T('adverso_info_delete_cliente') . '" />';
					echo '</label>&nbsp;';
					echo '<input type="checkbox" onclick="lcm_show(\'btn_delete\')" id="id_rem_cliente_' . $row1['id_cliente'] . '" name="rem_clientes[]" value="' . $row1['id_cliente'] . '" /></td>';

					echo "</tr>\n";
					$i++;
				}
				
				if ($show_table)
					echo "</table>";

				echo '<div align="right" style="visibility: hidden">';
				echo '<input type="submit" name="submit" id="btn_delete" value="' . _T('button_validate') . '" class="search_form_btn" />';
				echo "</div>\n";
		
				if ($edit)
					echo "<p><a href=\"sel_cliente_cli.php?adverso=$adverso\" class=\"add_lnk\">" . _T('adverso_button_add_cliente') . "</a></p>";

				echo "</form>\n";
				echo "</fieldset>";
				
				break;

			case 'cases':
				//
				// Show recent cases
				//

				$q = "SELECT clo.id_case, c.processo, c.p_adverso, c.date_creation, c.status, c.comarca 
						FROM lcm_case_adverso_cliente as clo, lcm_case as c
						WHERE id_adverso = " . $adverso . "
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
					show_page_subtitle(_T('adverso_subtitle_cases'), 'cases_participants');

					echo "<p class=\"normal_text\">\n";
					show_listcase_start();
		
					for ($cpt = 0; (($i<$prefs['page_rows']) && ($row1 = lcm_fetch_array($result))); $cpt++)
						show_listcase_item($row1, $cpt);

					show_listcase_end($list_pos, $number_of_rows);
					echo "</p>\n";
					echo "</fieldset>\n";
				}

				break;
			//
			// Client attachments
			//
			case 'attachments' :
				echo '<fieldset class="info_box">';
				show_page_subtitle(_T('adverso_subtitle_attachments'), 'tools_documents');
				echo "<p class=\"normal_text\">\n";

				echo '<form enctype="multipart/form-data" action="attach_file.php" method="post">' . "\n";
				echo '<input type="hidden" name="adverso" value="' . $adverso . '" />' . "\n";

				// List of attached files
				show_attachments_list('adverso', $adverso);

				// Attach new file form
				if ($edit)
					show_attachments_upload('adverso', $adverso);

				echo '<input type="submit" name="submit" value="' . _T('button_validate') . '" class="search_form_btn" />' . "\n";
				echo "</form>\n";

				echo "</p>\n";
				echo "</fieldset>\n";
				break;
		}

// Show this in all tabs
echo '<p>';
echo '<a href="edit_case.php?case=0&amp;attach_adverso=' . $row['id_adverso'] . '" class="create_new_lnk">';
echo _T('case_button_new');
echo "</a>";
echo "</p>\n";
				
// Clear session info
$_SESSION['adverso_data'] = array(); // DEPRECATED since 0.6.4
$_SESSION['form_data'] = array();
$_SESSION['errors'] = array();

lcm_page_end();
?>
