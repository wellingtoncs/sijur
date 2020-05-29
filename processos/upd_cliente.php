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

	$Id: upd_cliente.php,v 1.16 2007/01/12 17:34:19 mlutfy Exp $
*/

include('inc/inc.php');
include_lcm('inc_filters');
include_lcm('inc_obj_cliente');

// Clear all previous errors
$_SESSION['errors'] = array();
$_SESSION['form_data'] = array();

// Get form data from POST fields
foreach($_POST as $key => $value)
	$_SESSION['form_data'][$key] = $value;

$_SESSION['form_data']['id_cliente'] = intval(_session('id_cliente', 0));

$ref_upd_cliente = 'edit_cliente.php?cliente=' . _session('id_cliente');
if ($_SERVER['HTTP_REFERER'])
	$ref_upd_cliente = $_SERVER['HTTP_REFERER'];

//
// Update data
//

$obj_cliente = new LcmOrg(_session('id_cliente'));
$errs = $obj_cliente->save();

if (count($errs)) {
	$_SESSION['errors'] = array_merge($_SESSION['errors'], $errs);
	lcm_header("Location: " . $ref_upd_cliente);
	exit;
}

//
// Anexar ao processo
//
if (_session('attach_case')) {
	lcm_query("INSERT INTO lcm_case_adverso_cliente
				SET id_case = " . _session('attach_case') . ",
					id_cliente = " . $obj_cliente->getDataInt('id_cliente'));
}

//
// Go to the 'view details' page of the clienteanisation
//

// small reminder, if the adverso was created from the "add adverso to case" (Case details)
$attach = "";
if (_session('attach_case'))
	$attach = "&attach_case=" . _session('attach_case');

lcm_header('Location: cliente_det.php?cliente=' . $obj_cliente->getDataInt('id_cliente', '__ASSERT__') . $attach);

?>
