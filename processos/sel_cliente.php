<?php

/*
	This file is part of the Legal Case Management System (LCM).
	(C) 2004-2007 Free Software Foundation, Inc.

	This program is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published by the
	Free Software Foundation; either version 2 of the License, or (at your
	option) any later version.

	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
	or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA  02111-1307, USA

	$Id: sel_cliente.php,v 1.12 2007/03/20 18:33:30 mlutfy Exp $
*/

include('inc/inc.php');

$case = intval(_request('case'));

if (! $case > 0)
	die("ERROR: There is no such case.");

$q = "SELECT *
	FROM lcm_case_adverso_cliente
	WHERE id_case=$case";

$result = lcm_query($q);

$q = "SELECT id_cliente,name
	  FROM lcm_cliente
	  WHERE (id_cliente NOT IN (0";

// Add cliente in NOT IN list
while ($row = lcm_fetch_array($result)) 
	$q .= ',' . $row['id_cliente'];

$q .= ')';

// Add search criteria if any
$find_cliente_string = _request('find_cliente_string');

if ($find_cliente_string) {
	// XXX add more criteria ? (id, tax num, etc.)
	// should be centralised with function, i.e. get_sql_find_cliente($string)
	$q .= " AND (name LIKE '%$find_cliente_string%')";
}

$q .= ")";

// Sort clienteanisations by name
$order_name = 'ASC';
if (_request('order_name') == 'ASC' || _request('order_name') == 'DESC')
	$order_name = _request('order_name');

$q .= " ORDER BY name " . $order_name;

$result = lcm_query($q);

lcm_page_start(_T('title_case_add_cliente'));

show_context_start();
show_context_case_title($case);
show_context_case_involving($case);
show_context_end();

// Get the number of rows in the result
$number_of_rows = lcm_num_rows($result);

// Check for correct start position of the list
$list_pos = intval(_request('list_pos', 0));
if ($list_pos >= $number_of_rows) $list_pos = 0;

// Position to the page info start
if ($list_pos > 0)
	if (!lcm_data_seek($result, $list_pos))
		die("Error seeking position $list_pos in the result");

show_find_box('cliente', $find_cliente_string, '__self__');
echo '<form action="add_adverso.php" method="post">' . "\n";

$headers[0]['title'] = "";
$headers[0]['order'] = 'no_order';
$headers[1]['title'] = _Th('cliente_input_name');
$headers[1]['order'] = 'order_name';
$headers[1]['default'] = 'ASC';

show_list_start($headers);

for ($i = 0 ; (($i < $prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
	echo "<tr>\n";

	// Show checkbox
	echo "<td width='1%' class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo "<input type='checkbox' name='clientes[]' value='" . $row['id_cliente'] . "'>";
	echo "</td>\n";

	// Show cliente name
	echo "<td class='tbl_cont_" . ($i % 2 ? "dark" : "light") . "'>";
	echo '<a href="cliente_det.php?cliente=' . $row['id_cliente'] . '" class="content_link">';
	echo highlight_matches(clean_output($row['name']), $find_cliente_string);
	echo "</a>";
	echo "</td>\n";

	echo "</tr>\n";
}

echo "<tr>\n";
echo '<td colspan="2"><p><a href="edit_cliente.php?attach_case=' . $case . '" class="create_new_lnk">' 
	. _T('cliente_button_new_for_case')
	.  '</a></p></td>' . "\n";
echo "</tr>\n";

show_list_end($list_pos, $number_of_rows);

?>

	<input type="hidden" name="case" value="<?php echo $case; ?>">
	<input type="hidden" name="ref_sel_cliente" value="<?php echo $_SERVER['HTTP_REFERER']; ?>">
	<p><button name="submit" type="submit" value="submit" class="simple_form_btn"><?php echo _T('button_validate'); ?></button></p>

</form>
<?php

lcm_page_end();

?>
