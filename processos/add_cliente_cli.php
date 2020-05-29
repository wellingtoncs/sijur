<?php

include('inc/inc.php');

// Clean the POST values
$adverso = intval($_POST['adverso']);

if ($adverso > 0) {
	if ( isset($_POST['clientes']) && (count($_POST['clientes']) > 0) ) {
		$values = array();
		foreach($_POST['clientes'] as $cliente) {
			$cliente = intval($cliente);
			if ($cliente > 0) $values[] = "($adverso,$cliente)";
		}

		if (count($values) > 0) {
			// Prepare and do the query
			$q = "INSERT INTO lcm_adverso_cliente (id_adverso,id_cliente) VALUES " . join(',',$values);
			if (!($result = lcm_query($q))) die("$q<br>\n" . _T('title_error') . " " . lcm_errno() . ": " . lcm_error());
		}
	} else if ( isset($_POST['rem_clientes']) && (count($_POST['rem_clientes']) > 0) ) {
		$values = array();
		foreach($_POST['rem_clientes'] as $cliente) {
			$cliente = intval($cliente);
			if ($cliente > 0) $values[] = $cliente;
		}

		if (count($values) > 0) {
			// Remove relation adverso-clienteanization from database
			$q = "DELETE FROM lcm_adverso_cliente WHERE id_adverso=$adverso AND id_cliente IN (" . join(',',$values) . ")";
			if (!($result = lcm_query($q))) die("$q<br>\n" . _T('title_error') . " " . lcm_errno() . ": " . lcm_error());
		}
	}
}

//header("Location: $ref_sel_cliente_cli");
header("Location: adverso_det.php?adverso=$adverso&tab=clienteanisations");

?>
