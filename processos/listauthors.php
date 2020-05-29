<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');

$find_author_string = '';
if (isset($_REQUEST['find_author_string']))
	$find_author_string = $_REQUEST['find_author_string'];

lcm_page_start(_T('title_author_list'), '', '', 'authors_intro');
//FT Oculta a Informa��o de ajuda
//lcm_bubble('author_list');
show_find_box('author', $find_author_string);

$q = "SELECT id_author,name_first,name_middle,name_last,status
		FROM lcm_author
		WHERE (1=1 ";

// Add search criteria if any
if (strlen($find_author_string)>1) {
	$q .= " AND ((name_first LIKE '%$find_author_string%')"
		. " OR (name_middle LIKE '%$find_author_string%')"
		. " OR (name_last LIKE '%$find_author_string%'))";
}

$q .= ")";

// Sort authors by status
$order_set = false;
$order_status = '';
if (isset($_REQUEST['order_status']))
	if ($_REQUEST['order_status'] == 'ASC' || $_REQUEST['order_status'] == 'DESC') {
		$order_status = $_REQUEST['order_status'];
		$q .= " ORDER BY status " . $order_status;
		$order_set = true;
	}

// Sort authors by name_first
// [ML] I know, problably more logical by last name, but we do not split the columns
// later we can sort by any column if we need to
// [ML] 2006-03-07: Sorts using last name if siteconfig has name_order to Last, First Middle
$person_name_format = read_meta('person_name_format');
$order_name_first = 'ASC';
if (isset($_REQUEST['order_name_first']))
	if ($_REQUEST['order_name_first'] == 'ASC' || $_REQUEST['order_name_first'] == 'DESC')
		$order_name_first = $_REQUEST['order_name_first'];

$q .= ($order_set ? " , " : " ORDER BY ");

if ($person_name_format == '10')
	$q .= " name_last " . $order_name_first;
else
	$q .= " name_first " . $order_name_first;

$result = lcm_query($q);
$number_of_rows = lcm_num_rows($result);

// Check for correct start position of the list
if (isset($_REQUEST['list_pos']))
	$list_pos = $_REQUEST['list_pos'];
else
	$list_pos = 0;

if ($list_pos>=$number_of_rows) $list_pos = 0;

// Position to the page info start
if ($list_pos>0)
	if (!lcm_data_seek($result,$list_pos))
		lcm_panic("Error seeking position $list_pos in the result");


$headers = array();
$headers[0]['title'] = _Th('person_input_name');
$headers[0]['order'] = 'order_name_first';
$headers[0]['default'] = 'ASC';
$headers[1]['title'] = _Th('authoredit_input_status');
$headers[1]['order'] = 'order_status';
$headers[1]['default'] = '';

show_list_start($headers);

// Process the output of the query
for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
	// Author name
	echo "<tr>\n";
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo '<a href="author_det.php?author=' . $row['id_author'] . '" class="content_link">';
	echo highlight_matches(get_person_name($row), $find_author_string);
	echo "</a></td>\n";

	// Author status
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo _T('authoredit_input_status_' . $row['status']);
	echo "</td>\n";

	echo "</tr>\n";
}

show_list_end($list_pos, $number_of_rows);

// New author button
if ($GLOBALS['author_session']['status'] == 'admin' || $GLOBALS['author_session']['status'] == 'manager')
	echo '<p><a href="edit_author.php?author=0" class="create_new_lnk">'. _T('authoredit_button_new') . "</a></p>\n";
	
lcm_page_end();

?>