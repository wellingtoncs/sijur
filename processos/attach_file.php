<?php

include("inc/inc.php");
include_lcm('inc_filters');

// Get POST values
$case = intval(_request('case', 0));
$adverso = intval(_request('adverso', 0));
$cliente = intval(_request('cliente', 0));

if ($case > 0) {
	$type = 'case';
	$id_type = $case;
} else if ($adverso > 0) {
	$type = 'adverso';
	$id_type = $adverso;
} else if ($cliente > 0) {
	$type = 'cliente';
	$id_type = $cliente;
} else 
	lcm_panic("Missing object type for attachment.");

$_SESSION['errors'] = array();

if (isset($_POST['rem_file']) && is_array($_POST['rem_file']) && (count($_POST['rem_file']) > 0) ) {
	$rem_files = join(',',$_POST['rem_file']);
	$result = lcm_query("UPDATE lcm_{$type}_attachment
				SET date_removed=NOW(),content=NULL
				WHERE id_$type=$id_type
				AND id_attachment IN ($rem_files)");
}
if (strlen($_FILES['filename']['name']) > 0) {
	$_SESSION['user_file'] = $_FILES['filename'];
	$_SESSION['user_file']['description'] = _request('description');
	$filename = $_SESSION['user_file']['tmp_name'];

	if (is_uploaded_file($filename) && $_SESSION['user_file']['size'] > 0) {
		$file = fopen($filename,"r");
		$file_contents = fread($file, filesize($filename));
		$file_contents = addslashes($file_contents);

		$q = "INSERT INTO lcm_{$type}_attachment
			SET	id_$type=$id_type,
				id_author=" . $GLOBALS['author_session']['id_author'] . ",
				filename='" . $_SESSION['user_file']['name'] . "',
				type='" . $_SESSION['user_file']['type'] . "',
				size=" . $_SESSION['user_file']['size'] . ",
				description='" . $_SESSION['user_file']['description'] . "',
				content='$file_contents',
				date_attached=NOW()
			";

		$result = lcm_query($q);

		$user_file = array();

	} else {
		// Handle errors
		if ($_SESSION['user_file']['error'] > 0) {
			$cause = array(	UPLOAD_ERR_OK		=> 'The file was uploaded successfully!',
					UPLOAD_ERR_INI_SIZE	=> 'The file size exceeds the "upload_max_filesize" directive in php.ini.',
					UPLOAD_ERR_FORM_SIZE	=> 'The file size exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
					UPLOAD_ERR_PARTIAL	=> 'The file was uploaded only partially.',
					UPLOAD_ERR_NO_FILE	=> 'No file was uploaded!',
					UPLOAD_ERR_NO_TMP_DIR	=> 'Missing a temporary folder.');	// TRAD
			$_SESSION['errors']['file'] = $cause[$_SESSION['user_file']['error']];
		} else {
			$_SESSION['errors']['file'] = 'Empty file or access denied!';	// TRAD
		}
	}
}

lcm_header("Location: " . $_SERVER['HTTP_REFERER']);

?>
