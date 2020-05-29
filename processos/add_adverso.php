<?php

include('inc/inc.php');
include_lcm('inc_acc');

$case = intval(_request('case'));
$_SESSION['errors'] = array();

$destination = "case_det.php?case=$case";

// Test access rights (unlikely to happen, unless hack attempt)
if (! ($case && allowed($case, 'a'))) {
	$_SESSION['errors']['generic'] = "Access denied"; // TRAD
	header("Location: " . $destination);
	exit;
}

// Add adverso to case
if (isset($_REQUEST['adversos'])) {
	foreach ($_REQUEST['adversos'] as $key=>$value) 
		$adversos[$key] = intval($value);

	if ($adversos) {
		foreach($adversos as $adverso) {
			$q="INSERT INTO lcm_case_adverso_cliente
				SET id_case=$case,id_adverso=$adverso";

			$result = lcm_query($q);
		}
	}
}

// Add clienteanisation to case
if (isset($_REQUEST['clientes'])) {
	foreach ($_REQUEST['clientes'] as $key => $value) 
		$clientes[$key] = intval($value);

	if ($clientes) {
		foreach($clientes as $cliente) {
			$q = "INSERT INTO lcm_case_adverso_cliente
					SET id_case = $case,
						id_cliente = $cliente";

			lcm_query($q);
		}
	}
}

// Remove adverso from case
if (isset($_REQUEST['id_del_adverso'])) {
	foreach ($_REQUEST['id_del_adverso'] as $id_adverso) {
		$q="DELETE FROM lcm_case_adverso_cliente
			WHERE id_case = $case
			AND id_adverso = $id_adverso";

		$result = lcm_query($q);
	}
}

// Remove clienteanisation from case
if (isset($_REQUEST['id_del_cliente'])) {
	foreach ($_REQUEST['id_del_cliente'] as $id_cliente) {
		$q="DELETE FROM lcm_case_adverso_cliente
			WHERE id_case = $case
			AND id_cliente = $id_cliente";

		$result = lcm_query($q);
	}
}

lcm_header("Location: " . $destination . "#adversos");

?>
