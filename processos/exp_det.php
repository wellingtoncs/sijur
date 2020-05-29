<?php

include('inc/inc.php');
include_lcm('inc_obj_exp');

$expense = intval(_request('expense'));

if (! ($expense > 0))
	die("Missing id expense.");

lcm_page_start(_T('title_expense_view') . ' ' . _request('expense'), '', '', 'expenses_intro');

//
// Show general information
//
echo '<fieldset class="info_box">';

$obj_expense = new LcmExpenseInfoUI($expense);
$obj_expense->printGeneral();

$obj_exp_ac = new LcmExpenseAccess(0, 0, $obj_expense);
$obj_expense->printComments();

echo "</fieldset>\n";

// Clear session info
$_SESSION['form_data'] = array();
$_SESSION['errors'] = array();

lcm_page_end();

?>
