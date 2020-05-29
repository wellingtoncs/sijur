<?php

include('inc/inc.php');
include_lcm('inc_filters');
include_lcm('inc_contacts');
include_lcm('inc_obj_adverso');

// Get input value(s)
$id_adverso = intval(_request('adverso', 0));

// Get site preferences
$adverso_name_middle = read_meta('adverso_name_middle');
$adverso_citizen_number = read_meta('adverso_citizen_number');
$adverso_cpfcnpj = read_meta('adverso_cpfcnpj');
$adverso_civil_status = read_meta('adverso_civil_status');
$adverso_income = read_meta('adverso_income');

if (empty($_SESSION['errors'])) {
	$form_data = array('id_adverso' => 0,'referer' => $_SERVER['HTTP_REFERER']);

	if ($id_adverso > 0) {
		$q = 'SELECT * 
				FROM lcm_adverso 
				WHERE id_adverso = ' . $id_adverso;

		$result = lcm_query($q);

		if ($row = lcm_fetch_array($result)) {
			foreach($row as $key=>$value) {
				$form_data[$key] = $value;
			}
		}
	}
} else {
	// Fetch previously submitted values, if any
	if (! $_SESSION['form_data']['id_adverso'])
		$_SESSION['form_data']['id_adverso'] = 0;

	if (isset($_SESSION['form_data']))
		foreach($_SESSION['form_data'] as $key => $value)
			$form_data[$key] = $value;

}

if ($id_adverso > 0) {
	lcm_page_start(_T('title_adverso_edit') . ' ' . get_person_name($form_data), '', '', 'adversos_newadverso');
} else {
	lcm_page_start(_T('title_adverso_new'), '', '', 'adversos_newadverso');
}

echo show_all_errors();

echo '<form action="upd_adverso.php" method="post">' . "\n";

if (_request('attach_case')) {
	echo '<input type="hidden" name="attach_case" id="attach_case" value="'
		. _request('attach_case')
		. '" />' . "\n";
}

$obj_adverso = new LcmClientInfoUI($form_data['id_adverso']);
$obj_adverso->printEdit();


	//
	// Organisations this adverso represents
	//
	/* [ML] too confusing
	echo "<tr>\n";
	echo '<td colspan="2" align="center" valign="middle" class="heading">';
	echo '<h4>' . _T('adverso_subtitle_clienteanisations') . '</h4>';
	echo '</td>';
	echo "</tr>\n";
	$q = "SELECT name FROM lcm_adverso_cliente, lcm_cliente WHERE id_adverso=" . $form_data['id_adverso'] . " AND lcm_adverso_cliente.id_cliente=lcm_cliente.id_cliente";
	$result = lcm_query($q);
	$clientes = array();
	while ($row = lcm_fetch_array($result)) {
		$clientes[] = $row['name'];
	}
	echo "\t<tr><td>" . 'Representative of:' . '</td><td>' . join(', ',$clientes) . (count($clientes)>0 ? '&nbsp;' : ''); // TRAD
	$q = "SELECT lcm_cliente.id_cliente,name,id_adverso
		FROM lcm_cliente
		LEFT JOIN lcm_adverso_cliente
		ON (id_adverso=" . $form_data['id_adverso'] . "
		AND lcm_cliente.id_cliente=lcm_adverso_cliente.id_cliente)
		WHERE id_adverso IS NULL";
	$result = lcm_query($q);
	if (lcm_num_rows($result) > 0) {
		echo "\t\t<select name=\"new_cliente\">\n";
		echo "\t\t\t<option selected='selected' value=\"0\">- Select clienteanisation -</option>\n"; // TRAD
		while ($row = lcm_fetch_array($result)) {
			echo "\t\t\t<option value=\"" . $row['id_cliente'] . '">' . $row['name'] . "</option>\n";
		}
		echo "\t\t</select>\n";
		echo "\t\t<button name=\"submit\" type=\"submit\" value=\"add_cliente\" class=\"simple_form_btn\">" . 'Add' . "</button>\n"; // TRAD
	}
	echo "</td>\n</tr>\n";
	*/

?>

	<p><button name="submit" type="submit" value="submit" class="simple_form_btn"><?php echo _T('button_validate') ?></button></p>
	<input type="hidden" name="ref_edit_adverso" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" />
</form>

<?php
	lcm_page_end();

	// Reset error messages
	$_SESSION['errors'] = array();
	$_SESSION['form_data'] = array();
	$_SESSION['adverso'] = array(); // DEPRECATED
?>
