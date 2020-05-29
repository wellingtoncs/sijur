<?php

// XXX
// [ML] WARNING: I think this should all go into upd_rep_field.php
// Since we are having thousands of small .php files!
// XXX

include('inc/inc.php');
include_lcm('inc_lang');

// Clean the POST values
$rep = intval($_POST['rep']);
$order = intval($_POST['order']);
$header = clean_input($_POST['header']);
$field = intval($_POST['field']);
$sort = clean_input($_POST['sort']);

if (($rep>0) && ($field)) {
	// Change order of the columns to be left behind the new one
	$q = "UPDATE lcm_rep_col
			SET lcm_rep_col.col_order=lcm_rep_col.col_order+1
			WHERE (id_report=$rep
				AND lcm_rep_col.col_order>=$order)";
	$result = lcm_query($q);

	// Insert new column info
	$q = "INSERT INTO lcm_rep_col
			SET id_report=$rep,id_field=$field,lcm_rep_col.col_order=$order,header='$header',sort='$sort'";
	$result = lcm_query($q);
}

header("Location: " . $GLOBALS['HTTP_REFERER']);

?>
