<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');
include_lcm('inc_lang');

// Clear all previous errors
$_SESSION['errors'] = array();

// Clean input variables
$case = intval($_POST['case']);
$ref_sel_auth = ($_POST['ref_sel_auth']);
$authors = array();

if (isset($_POST['authors']) && is_array($_POST['authors'])) 
	foreach ($_POST['authors'] as $key => $value)
		$authors[$key] = $value;

if (! ($case > 0)) {
	header("Location: $ref_sel_auth");
	exit;
}

if (! $authors) {
	header("Location: $ref_sel_auth");
	exit;
}

// Check for admin rights on case
if (! allowed($case, 'a')) {
	$_SESSION['errors']['generic'] = _T('error_add_auth_no_rights');
	header("Location: $ref_sel_auth");
	exit;
}

// Get the current case stage for the FU entry
$case_stage = '';
$q = "SELECT stage, id_stage FROM lcm_case where id_case = " . $case;
$result = lcm_query($q);

if (($row = lcm_fetch_array($result))) {
	$case_stage = $row['stage'];
	$id_stage = $row['id_stage'];
} else {
	$_SESSION['errors']['generic'] = _T('error_add_auth_no_rights');
	header("Location: $ref_sel_auth");
	exit;
}

			foreach($authors as $author) {
				$q="INSERT INTO lcm_case_author
					SET id_case=$case,id_author=$author";

				$result = lcm_query($q);

				// Get author information
				$q = "SELECT *
						FROM lcm_author
						WHERE id_author=$author";
				$result = lcm_query($q);
				$author_data = lcm_fetch_array($result);

				// Add 'assigned' followup to the case
				$q = "INSERT INTO lcm_followup
						SET date_start = NOW(), 
							date_end = NOW(),
							id_stage = $id_stage,
							id_followup = 0, id_case = $case, 
							id_author = " . $GLOBALS['author_session']['id_author'] . ",
							type = 'assignment', 
							description = '" . $author_data['id_author'] . "',
							case_stage = '$case_stage'";

				$result = lcm_query($q);

				// Set case date_assigned to NOW()
				$q = "UPDATE lcm_case
						SET date_assignment = NOW()
						WHERE id_case = $case";
				$result = lcm_query($q);
			}

header("Location: $ref_sel_auth");

?>
