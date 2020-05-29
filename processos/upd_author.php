<?php

include('inc/inc.php');
include_lcm('inc_filters');

function force_session_restart($id_author) {
	include_lcm('inc_session');
	global $author_session, $lcm_session;

	zap_sessions($id_author, true);
	if ($author_session['id_author'] == $id_author) {
		lcm_debug("lcm_session = " . $lcm_session);
		delete_session($lcm_session);
	} else {
		lcm_debug("I am ID = " . $author_session['id_author']);
	}
}

function change_password() {
	global $author_session;
	//FT icluído: && $_SESSION['form_data']['status'] != 'manager'
	if ($_SESSION['form_data']['status'] != 'admin' 
		&& $_SESSION['form_data']['status'] != 'normal'
		&& $_SESSION['form_data']['status'] != 'manager'
		&& empty($_SESSION['form_data']['username']))
	{
		return;
	}

	// FIXME: include auth type according to 'auth_type' field in DB
	// default on 'db' if field not present/set.
	$class_auth = 'Auth_db';
	include_lcm('inc_auth_db');

	$auth = new $class_auth;

	if (! $auth->init()) {
		lcm_log("pass change: failed auth init: " . $auth->error);
		$_SESSION['errors']['password_generic'] = $auth->error;
		return;
	}
	
	// Is user allowed to change the password?
	if (! $auth->is_newpass_allowed(_session('id_author'), _session('username'), $author_session)) {
		$_SESSION['errors']['password_generic'] = $auth->error;
		return;
	}

	// Confirm current password only if user is not admin
	// (this also applies to the creation of new authors, only admins can do that)
	if ($author_session['status'] != 'admin' && $author_session['status'] != 'manager') {
		$valid_oldpass = false;

		// Try to validate with the MD5s
		if (_request('session_password_md5') && _request('next_session_password_md5')) {
			$valid_oldpass = $auth->validate_md5_challenge(_session('session_password_md5'), _session('next_session_password_md5'));
		}

		// If it didn't work, fallback on cleartext
		if (! $valid_oldpass) {
			$valid_oldpass =
			$auth->validate_pass_cleartext(_session('username'), _session('usr_old_passwd'));
		}

		if (! $valid_oldpass) {
			$_SESSION['errors']['password_current'] = _T('pass_warning_incorrect');
			return;
		}
	}

	// Confirm matching passwords
	if (_session('usr_new_passwd') != _session('usr_retype_passwd')) {
		$_SESSION['errors']['password_confirm'] = _T('login_warning_password_dont_match');
		return;
	}

	// Change the password
	$ok = $auth->newpass(_session('id_author'), _session('username'), _session('usr_new_passwd'), $author_session);

	if (! $ok) {
		lcm_log("New pass failed: " . $auth->error);
		$_SESSION['errors']['password_confirm'] = $auth->error;
		return;
	}
}

function change_username($id_author, $old_username, $new_username) {
	global $author_session;
	//FT incluindo: && $_SESSION['form_data']['status'] != 'manager'
	if ($_SESSION['form_data']['status'] != 'admin' 
		&& $_SESSION['form_data']['status'] != 'normal'
		&& $_SESSION['form_data']['status'] != 'manager'
		&& empty($_SESSION['form_data']['username']))
	{
		return;
	}

	include_lcm('inc_auth_db');
	$class_auth = 'Auth_db'; // FIXME, take from author_session
	$auth = new $class_auth;

	if (! $auth->init()) {
		lcm_log("username change: failed auth init, signal 'internal error'.");
		$_SESSION['errors']['password_generic'] = $auth->error;
		return;
	}

	// Change the username
	$ok = $auth->newusername($id_author, $old_username, $new_username, $author_session);

	if (! $ok) {
		lcm_log("New username failed: " . $auth->error);
		$_SESSION['errors']['username'] = _T('login_warning_password_change_failed') . ' ' . $auth->error;
		$_SESSION['form_data']['username'] = $new_username;

		return;
	}

	force_session_restart($id_author);
}

// Clear all previous errors
$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();

// Get form data from POST fields
foreach($_POST as $key => $value)
    $_SESSION['form_data'][$key] = $value;

//
// Basic security check: Non-admins can only edit themselves.
// Admins can edit any author.
////FT dando permissão para o gestor
if (_session('id_author') != $author_session['id_author'])
	if ($author_session['status'] != 'admin' && $author_session['status'] != 'manager')
		die(htmlentities("Apenas administradores podem editar/criar outros usuários"));

//
// Start SQL query
//
$fl = "date_update = NOW()";

// First name must have at least one character
if (strlen(lcm_utf8_decode(_session('name_first'))) < 1) {
	$_SESSION['errors']['name_first'] = _T('person_input_name_first') . ' ' . _T('warning_field_mandatory');
} else {
	$fl .= ", name_first = '" . _session('name_first')  . "'";
}

// Middle name can be empty
$fl .= ", name_middle = '" . _session('name_middle') . "'";

// Last name must have at least one character
if (! strlen(lcm_utf8_decode(_session('name_last')))) {
	$_SESSION['errors']['name_last'] = _T('person_input_name_last') . ' ' . _T('warning_field_mandatory');
} else {
	$fl .= ", name_last = '" . _session('name_last') . "'";
}

// Author status can only be changed by admins
//FT dando permissão para o gestor
if ($author_session['status'] == 'admin' || $author_session['status'] == 'manager')
	$fl .= ", status = '" . _session('status') . "'";

if (_session('id_author') > 0) {
	$q = "UPDATE lcm_author 
			SET $fl 
			WHERE id_author = " . _session('id_author');
	$result = lcm_query($q);
} else {
	if (count($errors)) {
    	header("Location: edit_author.php?author=0");
		exit;
	}

	$q = "INSERT INTO lcm_author SET date_creation = NOW(), username = '', password = '', $fl";
	$result = lcm_query($q);
	$_SESSION['form_data']['id_author'] = lcm_insert_id('lcm_author', 'id_author');
	$_SESSION['form_data']['id_author'] = _session('id_author');
}

//
// Change password (if requested)
//

if (_session('usr_new_passwd') || (! _session('username_old')))
	change_password();

//
// Change username
//

if (_session('username') != _session('username_old') || (!  _session('username_old')))
	change_username(_session('id_author'), _session('username_old'), _session('username'));

//
// Insert/update author contacts
//

include_lcm('inc_contacts');
update_contacts_request('author', _session('id_author'));

if (count($_SESSION['errors'])) {
	lcm_header("Location: edit_author.php?author=" . _session('id_author'));
	exit;
}

$dest_link = new Link('author_det.php');
$dest_link->addVar('author', _session('id_author'));

// [ML] Not used at the moment, but could be useful eventually to send user
// back to where he was (but as a choice, not automatically, see author_det.php).
if (_session('ref_edit_author'))
	$dest_link->addVar('ref', _session('ref_edit_author'));

// Delete session (of form data will become ghosts)
$_SESSION['form_data'] = array();

lcm_header('Location: ' . $dest_link->getUrlForHeader());

?>
