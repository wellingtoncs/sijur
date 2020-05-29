<?php

include('inc/inc.php');
include_lcm('inc_filters');
include_lcm('inc_impex');

$find_adverso_string = trim(_request('find_adverso_string'));

if (!empty($_REQUEST['export']) && ($GLOBALS['author_session']['status'] == 'admin')) {
	export('adverso', $_REQUEST['exp_format'], $find_adverso_string);
	exit;
}

lcm_page_start(_T('title_adverso_list'), '', '', 'adversos_intro');
//FT Ocultando a informação de ajuda
//lcm_bubble('adverso_list');
show_find_box('adverso', $find_adverso_string, '', (string)($GLOBALS['author_session']['status'] == 'admin') );

// List all adversos in the system + search criterion if any
$q = "SELECT id_adverso,name_first,name_middle,name_last
		FROM lcm_adverso";

//
// Add search criteria
//
if ($find_adverso_string) {
	// remove useless spaces
	$find_adverso_string = preg_replace('/ +/', ' ', $find_adverso_string);

	$q .= " WHERE ((name_first LIKE '%$find_adverso_string%')
			OR (name_middle LIKE '%$find_adverso_string%')
			OR (name_last LIKE '%$find_adverso_string%')
			OR (CONCAT(name_first, ' ', name_middle, ' ', name_last) LIKE '%$find_adverso_string%')
			OR (CONCAT(name_first, ' ', name_last) LIKE '%$find_adverso_string%')
		) ";
}

// Sort adversos by ID
$order_set = false;
$order_id = '';
if (isset($_REQUEST['order_id']))
	if ($_REQUEST['order_id'] == 'ASC' || $_REQUEST['order_id'] == 'DESC') {
		$order_id = $_REQUEST['order_id'];
		$q .= " ORDER BY id_adverso " . $order_id;
		$order_set = true;
	}

// Sort adversos by first name
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

if ($list_pos >= $number_of_rows)
	$list_pos = 0;

// Position to the page info start
if ($list_pos > 0)
	if (!lcm_data_seek($result,$list_pos))
		lcm_panic("Error seeking position $list_pos in the result");

// Output table tags
show_listadverso_start();

for ($i = 0 ; (($i < $prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
	echo "<tr>\n";
	echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">' . str_pad($row['id_adverso'], 4, "0", srt_pad_left) . "</td>\n";
	echo '<td class="tbl_cont_' . ($i % 2 ? "dark" : "light") . '">';
	echo '<a href="adverso_det.php?adverso=' . $row['id_adverso'] . '" class="content_link">';
	$fullname = clean_output(get_person_name($row));
	echo highlight_matches($fullname, $find_adverso_string);
	echo "</a>\n";
	echo "</td>\n";
	echo "</tr>\n";
}

show_listadverso_end($list_pos, $number_of_rows);

?>
<p><a href="edit_adverso.php" class="create_new_lnk"><?php echo _T('adverso_button_new'); ?></a></p>
<br /><br />
<?php
lcm_page_end();
?>
