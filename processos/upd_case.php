<?php

/*
	This file is part of the Legal Case Management System (LCM).
	(C) 2004-2005 Free Software Foundation, Inc.

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

	$Id: upd_case.php,v 1.60 2006/08/22 21:11:48 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');
include_lcm('inc_obj_case');

// Clear all previous errors
$_SESSION['errors'] = array();

// Get form data from POST fields
foreach($_POST as $key => $value)
    $_SESSION['form_data'][$key] = $value;

//
// Clean (most of the) input
//

$id_case = _request('id_case', 0);

//
// Create adverso, if requested
//
if (_request('add_adverso')) {
	include_lcm('inc_obj_adverso');

	$adverso = new LcmClient();
	$errs = $adverso->save();

	if (count($errs)) {
		$_SESSION['errors'] = array_merge($_SESSION['errors'], $errs);
	} else {
		$_SESSION['form_data']['attach_adverso'] = $adverso->getDataInt('id_adverso', '__ASSERT__');
	}
}

//
// Create clienteanisation, if requested
//
if (_request('add_cliente')) {
	include_lcm('inc_obj_cliente');

	$cliente = new LcmOrg();
	$errs = $cliente->save();

	if (count($errs)) {
		$_SESSION['errors'] = array_merge($_SESSION['errors'], $errs);
	} else {
		$_SESSION['form_data']['attach_cliente'] = $cliente->getDataInt('id_cliente', '__ASSERT__');
	}
}


//
// Create or update case data
//
$case = new LcmCase($id_case);
$errs = $case->save();

if (count($errs)) {
	$_SESSION['errors'] = array_merge($_SESSION['errors'], $errs);
	lcm_header("Location: ". $_SERVER['HTTP_REFERER']);
    exit;
}


//
// Create follow-up data
//
if (_request('add_fu')) {
	include_lcm('inc_obj_fu');

	$fu = new LcmFollowup(0, $case->getDataInt('id_case'));
	$errs = $fu->save();

	if (count($errs)) {
		$_SESSION['errors'] = array_merge($_SESSION['errors'], $errs);
		lcm_header("Location: ". $_SERVER['HTTP_REFERER']);
		exit;
	}
}

$send_to = _request('ref_edit_case', "case_det.php?case=" . $case->getDataInt('id_case'));

// Send to add_adverso if any adverso/cliente to attach
if (_session('attach_adverso') || _session('attach_cliente')) {
	lcm_header("Location: add_adverso.php?case=" . $case->getDataInt('id_case')
		. (_session('attach_adverso') ? "&adversos[]=" . _session('attach_adverso') : '')
		. (_session('attach_cliente') ? "&clientes[]=" . _session('attach_cliente') : '')
		. "&ref_sel_adverso=" . rawurlencode($send_to));
	exit;
}

lcm_header("Location: " . $send_to);

?>
