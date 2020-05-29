<?php

include('inc/inc.php');
include_lcm('inc_impex');

$find_cliente_string = '';
if (isset($_REQUEST['find_cliente_string']))
	$find_cliente_string = $_REQUEST['find_cliente_string'];

if (!empty($_REQUEST['export']) && ($GLOBALS['author_session']['status'] == 'admin')) {
	export('cliente', $_REQUEST['exp_format'], $find_cliente_string);
	exit;
}

lcm_page_start(_T('title_cliente_list'), '', '', 'adversos_intro');

///FT ocultando a Informação de Ajuda\/\/\/\/
//lcm_bubble('cliente_list');
show_find_box('cliente', $find_cliente_string, '', (string)($GLOBALS['author_session']['status'] == 'admin') );

// List all clienteanisations in the system + search criterion if any
$q = "SELECT id_cliente,name
		FROM lcm_cliente";

if (strlen($find_cliente_string) > 1)
	$q .= " WHERE (name LIKE '%$find_cliente_string%')";

// Sort clientes by ID
$order_set = false;
$order_id = '';
if (isset($_REQUEST['order_id']))
	if ($_REQUEST['order_id'] == 'ASC' || $_REQUEST['order_id'] == 'DESC') {
		$order_id = $_REQUEST['order_id'];
		$q .= " ORDER BY id_cliente " . $order_id;
		$order_set = true;
	}

// Sort clienteanisations by name
$order_name = 'ASC';
if (isset($_REQUEST['order_name']))
	if ($_REQUEST['order_name'] == 'ASC' || $_REQUEST['order_name'] == 'DESC')
		$order_name = $_REQUEST['order_name'];

$q .= ($order_set ? " , " : " ORDER BY ");
$q .= " name " . $order_name;

$result = lcm_query($q);
$number_of_rows = lcm_num_rows($result);

// Check for correct start position of the list
if (isset($_REQUEST['list_pos']))
	$list_pos = $_REQUEST['list_pos'];
else
	$list_pos = 0;

if ($list_pos >= $number_of_rows)
	$list_pos = 0;

// Position to the page info start
if ($list_pos > 0)
	if (!lcm_data_seek($result,$list_pos))
		lcm_panic("Error seeking position $list_pos in the result");

// Output table tags
// Not worth creating show_listclientes_*() for now
$cpt = 0;
$headers = array();

$headers[0]['title'] = "#";
$headers[0]['order'] = 'order_id';
$headers[0]['default'] = '';

$headers[1]['title'] = _Th('cliente_input_name');
$headers[1]['order'] = 'order_name';
$headers[1]['default'] = 'ASC';
$headers[1]['width'] = '99%';

show_list_start($headers);

for ($i = 0 ; (($i < $prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
	echo "<tr>\n";
	echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">'
		. $row['id_cliente']
		. "</td>\n";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo '<a href="cliente_det.php?cliente=' . $row['id_cliente'] . '" class="content_link">';
	echo highlight_matches(clean_output($row['name']), $find_cliente_string);
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";
}

show_list_end($list_pos, $number_of_rows);

echo '<p><a href="edit_cliente.php" class="create_new_lnk">' .  _T('cliente_button_new') . "</a></p>\n";
echo "<br />\n";

lcm_page_end();

?>