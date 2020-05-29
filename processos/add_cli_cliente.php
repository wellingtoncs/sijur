<?php

include('inc/inc.php');

// Clean the POST values
$cliente = intval($_POST['cliente']);

if ($cliente > 0) {
	if ( isset($_POST['adversos']) && (count($_POST['adversos']) > 0) ) {
		//
		// Add clienteanization representatives
		//
		$values = array();
		foreach($_POST['adversos'] as $adverso) {
			$adverso = intval($adverso);
			if ($adverso > 0) $values[] = "($cliente,$adverso)";
		}

		if (count($values) > 0) {
			// Prepare and do the query
			$q="INSERT INTO lcm_adverso_cliente (id_cliente,id_adverso) VALUES " . join(',',$values);
			if (!($result = lcm_query($q))) die("$q<br>\n" . _T('title_error') . " " . lcm_errno() . ": " . lcm_error());
		}
	} else if ( isset($_POST['rem_adversos']) && (count($_POST['rem_adversos']) > 0) ) {
		//
		// Remove clienteanization representatives
		//
		$values = array();
		foreach($_POST['rem_adversos'] as $adverso) {
			$adverso = intval($adverso);
			if ($adverso > 0) $values[] = $adverso;
		}

		if (count($values) > 0) {
			// Prepare and do the query
			$q="DELETE FROM lcm_adverso_cliente WHERE id_cliente=$cliente AND id_adverso IN (" . join(',',$values) . ")";
			if (!($result = lcm_query($q))) die("$q<br>\n" . _T('title_error') . " " . lcm_errno() . ": " . lcm_error());
		}
	}
}

//header("Location: $ref_sel_cli_cliente");
header("Location: cliente_det.php?cliente=$cliente&tab=representatives");

?>
