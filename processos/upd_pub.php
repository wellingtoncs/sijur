<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');

// Clear all previous errors
$_SESSION['errors'] = array();

$id_pub = _request('id_pub', 0);

// Get form data from POST fields
foreach($_POST as $key => $value)
    $_SESSION['form_data'][$key]=$value;

//
// Check access rights
//

$ac = get_ac_pub($id_pub);

// XXX FIXME make better check?
if (! $ac['w'])
	die("access denied");

// Convert day, month, year, hour, minute to date/time
// Check submitted information

// XXX for some reason (bad memory), date_start doesn't allow the user to leave
// some fields empty, but date_end (in absolute more) does. Hence extra validation.

//
// Start date
//
$_SESSION['form_data']['start_time'] = get_datetime_from_array($_SESSION['form_data'], 'start', 'start', '', false);
$unix_start_time = strtotime($_SESSION['form_data']['start_time']);

if (($unix_start_time < 0) || ! checkdate_sql($_SESSION['form_data']['start_time']))
	$_SESSION['errors']['start_time'] = _Ti('time_input_date_start') . 'Invalid date'; // TRAD

//
// End date
//
if ($prefs['time_intervals'] == 'absolute') {
	$_SESSION['form_data']['end_time'] = get_datetime_from_array($_SESSION['form_data'], 'end', 'start', '', false);

	// Set to default empty date if all fields empty
	if (! isset_datetime_from_array($_SESSION['form_data'], 'end', 'date_only')) { 
		$_SESSION['errors']['end_time'] = _Ti('time_input_date_end') . 'Invalid date'; // TRAD
	} else {
		$unix_end_time = strtotime($_SESSION['form_data']['end_time']);

		if (($unix_end_time < 0) || !checkdate_sql($_SESSION['form_data']['end_time'])) 
			$_SESSION['errors']['end_time'] = _Ti('time_input_date_end') . 'Invalid date'; // TRAD
	}
} else {
	if ( ! (isset($_SESSION['form_data']['delta_days']) && (!is_numeric($_SESSION['form_data']['delta_days']) || $_SESSION['form_data']['delta_days'] < 0) ||
		isset($_SESSION['form_data']['delta_hours']) && (!is_numeric($_SESSION['form_data']['delta_hours']) || $_SESSION['form_data']['delta_hours'] < 0) ||
		isset($_SESSION['form_data']['delta_minutes']) && (!is_numeric($_SESSION['form_data']['delta_minutes']) || $_SESSION['form_data']['delta_minutes'] < 0) ) ) {
		$unix_end_time = $unix_start_time
				+ $_SESSION['form_data']['delta_days'] * 86400
				+ $_SESSION['form_data']['delta_hours'] * 3600
				+ $_SESSION['form_data']['delta_minutes'] * 60;
		$_SESSION['form_data']['end_time'] = date('Y-m-d H:i:s', $unix_end_time);
	} else {
		$_SESSION['errors']['end_time'] = _Ti('app_input_time_length') . _T('time_warning_invalid_format') . ' (' . $_SESSION['form_data']['delta_hours'] . ')'; // XXX
		$_SESSION['form_data']['end_time'] = $_SESSION['form_data']['start_time'];
	}
}

if (!count($_SESSION['errors']) && $unix_end_time < $unix_start_time)
	$_SESSION['errors']['end_time'] = "The date interval is not valid (end before start)"; // TRAD

// reminder
if ($prefs['time_intervals']=='absolute') {
	// Set to default empty date if all fields empty
	if (!($_SESSION['form_data']['reminder_year'] || $_SESSION['form_data']['reminder_month'] || $_SESSION['form_data']['reminder_day']))
		$_SESSION['form_data']['reminder'] = '0000-00-00 00:00:00';
		// Report error if some of the fields empty
	elseif (!$_SESSION['form_data']['reminder_year'] || !$_SESSION['form_data']['reminder_month'] || !$_SESSION['form_data']['reminder_day']) {
		$_SESSION['errors']['reminder'] = 'Incomplete reminder time'; // TRAD
		$_SESSION['form_data']['reminder'] = get_datetime_from_array($_SESSION['form_data'], 'reminder', 'start', '', false);
	} else {
		// Join fields and check resulting time
		$_SESSION['form_data']['reminder'] = get_datetime_from_array($_SESSION['form_data'], 'reminder', 'start', '', false);
		$unix_reminder_time = strtotime($_SESSION['form_data']['reminder']);

		if ( ($unix_reminder_time<0) || !checkdate($_SESSION['form_data']['reminder_month'],$_SESSION['form_data']['reminder_day'],$_SESSION['form_data']['reminder_year']) )
			$_SESSION['errors']['reminder'] = 'Invalid reminder time!'; // TRAD
	}
} else {
	if ( ! (isset($_SESSION['form_data']['rem_offset_days']) && (!is_numeric($_SESSION['form_data']['rem_offset_days']) || $_SESSION['form_data']['rem_offset_days'] < 0) ||
		isset($_SESSION['form_data']['rem_offset_hours']) && (!is_numeric($_SESSION['form_data']['rem_offset_hours']) || $_SESSION['form_data']['rem_offset_hours'] < 0) ||
		isset($_SESSION['form_data']['rem_offset_minutes']) && (!is_numeric($_SESSION['form_data']['rem_offset_minutes']) || $_SESSION['form_data']['rem_offset_minutes'] < 0) ) ) {
		$unix_reminder_time = $unix_start_time
				- $_SESSION['form_data']['rem_offset_days'] * 86400
				- $_SESSION['form_data']['rem_offset_hours'] * 3600
				- $_SESSION['form_data']['rem_offset_minutes'] * 60;
		$_SESSION['form_data']['reminder'] = date('Y-m-d H:i:s', $unix_reminder_time);
	} else {
		$_SESSION['errors']['reminder'] = _Ti('app_input_reminder') . _T('time_warning_invalid_format') . ' (' . $_SESSION['form_data']['rem_offset_hours'] . ')'; // XXX
		$_SESSION['form_data']['reminder'] = $_SESSION['form_data']['start_time'];
	}
}

// title
if (!(strlen($_SESSION['form_data']['tribunal'])>0)) $_SESSION['errors']['title'] = 'Appointment title should not be empty!';	// TRAD

//
// Check if errors found
//
if (count($_SESSION['errors'])) {
	// Errors, return to editing page
	lcm_header("Location: " . $_SERVER['HTTP_REFERER']);
	exit;
} else {
	// No errors, proceed with database update
	//$fl .= " nome_pesquisado 	= '" . _session('nome_pesquisado') . "', ";
	//$fl .= " secretaria 		= '" . _session('secretaria') . "', ";
	
	$fl = "";
	//$fl .= " id_case 		= '" . (_session('pasta')?_session('pasta'):_session('id_case')). "', ";
	if(_session('new')=='yes'){
		$fl .= " n_recorte   		= '0', ";
		$fl .= " start_pub  		= '" . _session('start_year'). "-"._session('start_month')."-"._session('start_day')."', ";
		$fl .= " end_pub    		= '0000-00-00 00:00:00', ";
		$fl .= " jornal     		= '" . _session('jornal') . "', ";
		$fl .= " tribunal   		= '" . _session('tribunal') . "', ";
		$fl .= " numero_processo	= '" . _session('numero_processo') . "', ";
		$fl .= " publicacao 		= '" . _session('publicacao') . "', ";
		$fl .= " performed   		= 'Y', ";
	} else {
		$fl .= " performed   		= '" . (_session('performed') ? 'Y' : 'N') . "', ";
	}
	//$fl .= " hidden      		= '" . (_session('hidden', '') ? 'Y' : 'N') . "', ";
	$fl .= " perf_desc   		= '" . _session('perf_desc') . "', ";
	$fl .= " upd_author  		=  " . $GLOBALS['author_session']['id_author'];
	
	// Insert/update appointment
	if ($id_pub>0) {
		// Update existing appointment
		$fl .= (_session('pasta') ? ', id_case=' . _session('pasta') : '');
		$q="UPDATE lcm_pub SET $fl, date_update=NOW() WHERE id_pub = $id_pub ";
		// Only admin or appointment author itself could change it
		if ( !($GLOBALS['author_session']['status'] === 'admin' || $GLOBALS['author_session']['status'] === 'manager' ) ) {
		
			//FT Criando a condição de cumprimento para os usuários envolvidos
			$qpub = "select * from lcm_author_pub WHERE id_pub = " . $id_pub . " AND id_author = " . $GLOBALS['author_session']['id_author'];
			$row_pub = lcm_query($qpub);
			if( lcm_num_rows($row_pub)==0 ){
				$q .= " AND id_author = " . $GLOBALS['author_session']['id_author'];
			}
		}

		$result = lcm_query($q);
		
	} else {
		// Add the new appointment
		$q = "INSERT INTO lcm_pub SET ";
		// Add case ID if available
		$q .= (_session('id_case') ? 'id_case=' . _session('id_case') . ',' : '');
		// Add ID of the creator
		$q .= 'id_author = ' . $GLOBALS['author_session']['id_author'] . ',';
		// Add the rest of the fields
		$q .= "$fl, date_update = NOW(), date_creation = NOW()";

		$result = lcm_query($q);

		$id_pub = lcm_insert_id('lcm_pub', 'id_pub');
		$_SESSION['form_data']['id_pub'] = $id_pub;

		// Add relationship with the creator
		lcm_query("INSERT INTO lcm_author_pub SET id_pub=$id_pub,id_author=" . $GLOBALS['author_session']['id_author']);

		// Add relationship with the parent followup (if any)
		if (!empty($_SESSION['form_data']['id_followup']))
			lcm_query("INSERT INTO lcm_pub_fu SET id_pub=$id_pub,id_followup=" . $_SESSION['form_data']['id_followup'] . ",relation='parent'");

	}

	// Add/update appointment participants (authors)
	if (_session('author')) {
		$q = "INSERT INTO lcm_author_pub SET id_pub = $id_pub, id_author = " . _session('author');

		lcm_query($q, true); // ignore errors
		$_SESSION['errors']['author_added'] = htmlentities("Um usuário foi adicionado aos participantes deste agendamento."); // TRAD
		// FIXME use $_SESSION['info'] instead
	}

	// Remove appointment participants (authors)
	if (_session('rem_author')) {
		//FT Substituíndo (não estava funcionando) -> $q = "DELETE FROM lcm_author_app WHERE id_app=$id_app AND id_author IN (" . join(',', _session('rem_author')) . ")";
		$q = "DELETE FROM lcm_author_pub WHERE id_pub=$id_pub AND id_author IN (" . join(',', $_POST['rem_author']) . ")";
		if ( ($result = lcm_query($q)) && (mysql_affected_rows() > 0) ) // XXX MySQL SPECIFIC
			$_SESSION['errors']['author_removed'] = htmlentities("Usuário(s) foi/foram retirado(s) da participação deste agendamento.");
		// Clean author removal list
		unset($_SESSION['form_data']['rem_author']);
	}

	// Add/update appointment adversos/clienteanisations
	if (_session('adverso')) {
		$adverso_cliente = explode(':', _session('adverso'));
		$q = "INSERT INTO lcm_pub_adverso_cliente SET id_pub=$id_pub";
		$q .= ',id_adverso=' . $adverso_cliente[0];

		if ($adverso_cliente[1])
			$q .= ',id_cliente=' . $adverso_cliente[1];

		lcm_query($q, true); // ignore errors
		$_SESSION['errors']['adverso_added'] = "Um cliente/adverso foi adicionado aos participantes desta nomeação.";
	}

	// Remove appointment participants (adversos/clienteanisations)
	if (_session('rem_adverso')) {
		$q = "DELETE FROM lcm_pub_adverso_cliente WHERE id_pub=$id_pub AND (0";
		foreach($_SESSION['form_data']['rem_adverso'] as $rem_cli) {
			$adverso_cliente = explode(':',$rem_cli);
			$co .= 'id_adverso=' . $adverso_cliente[0];
			if ($adverso_cliente[1])
				$co = "($co AND id_cliente=" . $adverso_cliente[1] . ')';
			$q .= " OR $co";
		}
		$q .= ")";
		if ( ($result = lcm_query($q)) && (mysql_affected_rows() > 0) ) // XXX MySQL SPECIFIC
			$_SESSION['errors']['adverso_added'] = htmlentities("Um cliente/adverso foi adicionado aos participantes deste agendamento.");
		// Clean adverso removal list
		unset($_SESSION['form_data']['rem_adverso']);
	}

	// Check if author or adverso/clienteanisation was added
	if (count($_SESSION['errors'])) {
		$ref_url = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($ref_url['query'],$params);
		$params['pub'] = $id_pub;

		foreach ($params as $k => $v)
			$params[$k] = $k . '=' . urlencode($v);

		lcm_header('Location: edit_pub.php?' . join('&',$params) );
		exit;
	}
	
	// Send user back to add/edit page's referer or (default) to appointment detail page
	switch (_session('submit')) {
		case 'add_author':
		case 'add_adverso':
			// Go back to edit the same appointment. Save the original referer
			lcm_header('Location: ' . $_SERVER['HTTP_REFERER']);
			break;
		case 'add' :
			// Go back to the edit page's referer
			unset($_SESSION['errors']);
			lcm_header('Location: ' . _session('ref_edit_pub', "pub_det.php?pub=$id_pub"));
			break;
		case 'addnew' :
			// Open new appointment. Save the original referer
			unset($_SESSION['errors']);
			lcm_header('Location: edit_pub.php?pub=0&ref=' . _session('ref_edit_pub', "pub_det.php?pub=$id_pub"));
			break;
		case 'adddet' :
		case 'submit' :
		default :
			// Go to pubointment details
			unset($_SESSION['errors']);
			lcm_header("Location: pub_det.php?pub=$id_pub");
	}	
	exit;
}

?>
