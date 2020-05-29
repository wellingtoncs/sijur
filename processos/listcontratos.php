<?php

include('inc/inc.php');
include_lcm('inc_impex');

$find_contrato_string = '';
if (isset($_REQUEST['find_contrato_string']))
	$find_contrato_string = $_REQUEST['find_contrato_string'];

if (!empty($_REQUEST['export']) && ($GLOBALS['author_session']['status'] == 'admin')) {
	export('contrato', $_REQUEST['exp_format'], $find_contrato_string);
	exit;
}

lcm_page_start(_T('Contratos'), '', '', 'adversos_intro');

///FT ocultando a Informação de Ajuda\/\/\/\/
//lcm_bubble('contrato_list');
show_find_box('contrato', $find_contrato_string, '', (string)($GLOBALS['author_session']['status'] == 'admin') );

// List all contratoanisations in the system + search criterion if any
$q = "select k.id_case, c.p_adverso, k.value from lcm_keyword_case k 
	  join lcm_case c on c.id_case = k.id_case ";

if (strlen($find_contrato_string) > 1)
	$q .= " WHERE (k.value LIKE '%$find_contrato_string%')";

// Sort contratos by ID
$order_set = false;
$order_id = '';
if (isset($_REQUEST['order_id']))
	if ($_REQUEST['order_id'] == 'ASC' || $_REQUEST['order_id'] == 'DESC') {
		$order_id = $_REQUEST['order_id'];
		$q .= " ORDER BY id_case " . $order_id;
		$order_set = true;
	}

// Sort contratoanisations by name
$order_name = 'ASC';
if (isset($_REQUEST['order_name']))
	if ($_REQUEST['order_name'] == 'ASC' || $_REQUEST['order_name'] == 'DESC')
		$order_name = $_REQUEST['order_name'];

$q .= ($order_set ? " , " : " ORDER BY ");
$q .= " p_adverso " . $order_name;

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
// Not worth creating show_listcontratos_*() for now
$cpt = 0;
$headers = array();

$headers[0]['title'] = "Pasta";
$headers[0]['order'] = 'order_id';
$headers[0]['default'] = '';

$headers[1]['title'] = _Th('Adverso');
$headers[1]['order'] = 'order_name';
$headers[1]['default'] = 'ASC';
$headers[1]['width'] = '99%';

show_list_start($headers);

$compl_url = "parte_f=&status_f=&case_owner=&processo_f=&comar_f=&state_f=&case_period=all&cliente_f=&condicao_f=&type_f=&acao_f=&jus=0&vara_f=&stopday_f=&diversos_f=&submit=submit";

for ($i = 0 ; (($i < $prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
	echo "<tr>\n";
	echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">';
	echo '<a href="listcases.php?pasta_f=' . $row['id_case'] . $compl_url . '" class="content_link">';
	echo $row['id_case'];
	echo "</td>\n";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo highlight_matches(clean_output($row['p_adverso']), $find_contrato_string);
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";
}

show_list_end($list_pos, $number_of_rows);

#echo '<p><a href="edit_contrato.php" class="create_new_lnk">' .  _T('contrato_button_new') . "</a></p>\n";
#echo "<br />\n";

lcm_page_end();

?>