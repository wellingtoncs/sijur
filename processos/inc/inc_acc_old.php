<?php

// Execute this file only once
if (defined('_INC_ACC')) return;
define('_INC_ACC', '1');

// c.f. http://www.direito2010.com.br
// or http://www.direito2010.com.br
function allowed($case, $access) {
	// By default, do not allow access
	$allow = false;

	// Admins can access everything
	if ($GLOBALS['author_session']['status'] == 'admin' || $GLOBALS['author_session']['status'] == 'manager')
		return true;

	// Check if the case number is present
	if ($case > 0) {

		// Left join is used to fallback on 'public' values if the user is not
		// assigned to the case.
		$q = "SELECT ca.*, c.status, c.public, c.pub_write
				FROM lcm_case as c
				LEFT JOIN lcm_case_author as ca 
					ON (ca.id_case = c.id_case 
						AND id_author = " . $GLOBALS['author_session']['id_author'] . ")
				WHERE c.id_case = " . intval($case);

		$result = lcm_query($q);

		if ($row = lcm_fetch_array($result)) {

			// Set initial value to true, if $access parameter is set
			$allow = (bool) $access;
			$open = true;

			if ($row['status'] == 'deleted' || $row['status'] == 'closed')
				$open = false;

			// Walk each character in the required access rights list
			for($i = 0; $i < strlen($access); $i++) {
				switch ($access{$i}) {
					case "r":
						$allow &= ($row['ac_read'] || ($row['ac_read'] != '0' && $row['public']));
						break;
					case "w":
						$allow &= (($row['ac_write'] || ($row['ac_write'] != '0' && $row['pub_write'])) && $open);
						break;
					case "e":
						$allow &= ($row['ac_edit'] && $open);
						break;
					case "a":
						$allow &= ($row['ac_admin'] && $open);
						break;
					case "A":
						// bypass 'closed' or 'deleted' (ex: case status for admin)
						$allow &= ($row['ac_admin']);
						break;
					default:
						// At any unknown character, disallow access
						$allow = 0;
				}
			}
		}
	}

	return $allow;
}

function allowed_author($author, $access) {
	global $author_session;

	// Admins can access everything
	if ($author_session['status'] == 'admin' || $author_session['status'] == 'manager')
		return true;

	// Check if the author ID is present
	if (! (intval($author) > 0))
		return false;

	// We're not checking for various access rights at the moment,
	// since for read/write it is the same test (author = self or admin)
	if ($author_session['id_author'] == $author)
		return true;
	
	return false;
}

// NOTE: Unlike other allowed() functions, we return an array of rights
// This avoids making many SQL calls on the DB to get edit/write/admin..
function get_ac_app($app, $case = 0) {
	global $author_session;

	// Basic rights
	$allow = array('r' => false, 'w' => false, 'e' => false, 'a' => false);

	// Check if the app ID is present
	$app = intval($app);
	if ($app < 0) // internal error
		return $allow;

	// Admins can access everything
	if ($author_session['status'] == 'admin' || $author_session['status'] == 'manager')
		return array('r' => true, 'w' => true, 'e' => true, 'a' => true);
	
	// This gets set later, if appropriate
	$id_case = 0;
	$id_author = 0;
	$case_open = true;

	if ($app) {
		// Existing appointment

		//
		// Check right on case associated with app, if any
		// + fetch case access rights. Do not trust the adverso
		// provided $case
		//
		$query = "SELECT *, p.id_author as p_id_author
			FROM lcm_app as p
			LEFT JOIN lcm_case_author as ca ON p.id_case = ca.id_case
			LEFT JOIN lcm_case as c ON p.id_case = c.id_case
			WHERE id_app = " . $app;

		$result = lcm_query($query);

		if (! ($row_app = lcm_fetch_array($result)))
			return $allow; // Case does not exist, should not happen

		// Using p_id_author because lcm_case_author also has an id_author
		$id_author = $row_app['p_id_author'];
		$id_case = $row_app['id_case'];

		if ($row_app['status'] == 'deleted' || $row_app['status'] == 'closed')
			$case_open = false;
	} else {
		// New appointment
		$id_author = $author_session['id_author'];

		if ($case) {
			$id_case = intval($case);

			if (! ($id_case > 0))
				return $allow;

			// Get AC for case
			$query = "SELECT *
				FROM lcm_case as c 
				LEFT JOIN lcm_case_author as ca ON c.id_case = ca.id_case
				WHERE c.id_case = " . $id_case;

			$result = lcm_query($query);

			if (! ($row_app = lcm_fetch_array($result)))
				return $allow; // Case does not exist, should not happen

			if ($row_app['status'] == 'deleted' || $row_app['status'] == 'closed')
				$case_open = false;
		}
	}

	//
	// General idea:
	// If case: use case access rights
	// Else, check if user is the creator of the app
	//

	// READ ac
	if ($id_case) {
		$allow['r'] = ($row_app['ac_read'] || ($row_app['ac_read'] != '0' && $row_app['public']));
	} else {
		$allow['r'] = ($id_author == $author_session['id_author']);
	}

	// WRITE ac
	if ($id_case) {
		$allow['w'] = ($row_app['ac_write'] || ($row_app['ac_write'] != '0' && $row_app['pub_write']));
		$allow['w'] &= $case_open;
	} else {
		$allow['w'] = ($id_author == $author_session['id_author']);
	}

	// EDIT ac
	if ($id_case) {
		$allow['e'] = $row_app['ac_edit'];
		$allow['e'] &= $case_open;
	} else {
		$allow['e'] = ($id_author == $author_session['id_author']);
	}

	// ADMIN ac
	if ($id_case) {
		$allow['a'] = $row_app['ac_admin'];
		$allow['a'] &= $case_open;
	} else {
		$allow['a'] = ($id_author == $author_session['id_author']);
	}

	return $allow;
}
function get_ac_pub($pub, $case = 0) {
	global $author_session;

	// Basic rights
	$allow = array('r' => false, 'w' => false, 'e' => false, 'a' => false);

	// Check if the pub ID is present
	$pub = intval($pub);
	if ($pub < 0) // internal error
		return $allow;

	// Admins can access everything
	if ($author_session['status'] == 'admin' || $author_session['status'] == 'manager')
		return array('r' => true, 'w' => true, 'e' => true, 'a' => true);
	
	// This gets set later, if appropriate
	$id_case = 0;
	$id_author = 0;
	$case_open = true;

	if ($pub) {
		// Existing appointment

		//
		// Check right on case associated with app, if any
		// + fetch case access rights. Do not trust the adverso
		// provided $case
		//
		$query = " SELECT *, p.id_author as p_id_author
			FROM lcm_pub as p
			LEFT JOIN lcm_case_author as ca ON p.id_case = ca.id_case
			LEFT JOIN lcm_case as c ON p.id_case = c.id_case
			WHERE id_pub = " . $pub;

		$result = lcm_query($query);

		if (! ($row_pub = lcm_fetch_array($result)))
			return $allow; // Case does not exist, should not happen

		// Using p_id_author because lcm_case_author also has an id_author
		$id_author = $row_pub['p_id_author'];
		$id_case = $row_pub['id_case'];

		if ($row_pub['status'] == 'deleted' || $row_pub['status'] == 'closed')
			$case_open = false;
	} else {
		// New appointment
		$id_author = $author_session['id_author'];

		if ($case) {
			$id_case = intval($case);

			if (! ($id_case > 0))
				return $allow;

			// Get AC for case
			$query = "SELECT *
				FROM lcm_case as c 
				LEFT JOIN lcm_case_author as ca ON c.id_case = ca.id_case
				WHERE c.id_case = " . $id_case;

			$result = lcm_query($query);

			if (! ($row_pub = lcm_fetch_array($result)))
				return $allow; // Case does not exist, should not happen

			if ($row_pub['status'] == 'deleted' || $row_pub['status'] == 'closed')
				$case_open = false;
		}
	}

	//
	// General idea:
	// If case: use case access rights
	// Else, check if user is the creator of the app
	//

	// READ ac
	if ($id_case) {
		$allow['r'] = ($row_pub['ac_read'] || ($row_pub['ac_read'] != '0' && $row_pub['public']));
	} else {
		$allow['r'] = ($id_author == $author_session['id_author']);
	}

	// WRITE ac
	if ($id_case) {
		$allow['w'] = ($row_pub['ac_write'] || ($row_pub['ac_write'] != '0' && $row_pub['pub_write']));
		$allow['w'] &= $case_open;
	} else {
		$allow['w'] = ($id_author == $author_session['id_author']);
	}

	// EDIT ac
	if ($id_case) {
		$allow['e'] = $row_pub['ac_edit'];
		$allow['e'] &= $case_open;
	} else {
		$allow['e'] = ($id_author == $author_session['id_author']);
	}

	// ADMIN ac
	if ($id_case) {
		$allow['a'] = $row_pub['ac_admin'];
		$allow['a'] &= $case_open;
	} else {
		$allow['a'] = ($id_author == $author_session['id_author']);
	}

	return $allow;
}
function get_ac_adverso($adverso) {
	global $author_session;

	// direitos básicos
	$allow = array('r' => false, 'w' => false, 'e' => false, 'a' => false);
	
	// Verifique se o ID app está presente
	$adverso = intval($adverso);
	if ($app < 0) // internal error
		return $allow;

	// Os administradores podem acessar tudo
	if ($author_session['status'] == 'admin' || $author_session['status'] == 'manager')
		return array('r' => true, 'w' => true, 'e' => true, 'a' => true);

	// Procedimento de controle de acesso:
	// 1- Verifique a configuração do site global (para ver se é necessário AC)
	// 2- Verifique se o Cliente já está em um caso para o qual o usuário está trabalhando em
	// NOTA: "editar" e "admin" ainda não estão definidos nem implementada.
	$meta_adverso_read  = read_meta('adverso_share_read');
	$meta_adverso_write = read_meta('adverso_share_write');

	if (! ($meta_adverso_read == 'no')) {
		$allow['r'] = true;

		if (! ($meta_adverso_write == 'no'))
			$allow['w'] = $allow['e'] = true;
	}

	if ($allow['r'] == false || $allow['w'] == false) {
		// Verifique se o autor é associado a um caso com este Cliente
		$q = "SELECT count(*) as cpt, sum(ac_read) as ac_read, 
					sum(ac_write) as ac_write, sum(ac_edit) as ac_edit, sum(ac_admin) as ac_admin
				FROM lcm_case_adverso_cliente as cco, lcm_case_author as ca
				WHERE ca.id_author = " . $author_session['id_author'] . "
				  AND cco.id_adverso = " . $adverso . "
				  AND ca.id_case = cco.id_case";

		$result = lcm_query($q);

		if ($row = lcm_fetch_array($result)) {
			$allow['r'] = ($row['ac_read'] > 0);
			$allow['w'] = ($row['ac_write'] > 0);
			$allow['e'] = ($row['ac_edit'] > 0);
			$allow['a'] = ($row['ac_admin'] > 0);
		}
	}

	return $allow;
}

function get_ac_cliente($cliente) {
	global $author_session;

	// Basic rights
	$allow = array('r' => false, 'w' => false, 'e' => false, 'a' => false);
	
	// Check if the app ID is present
	$cliente = intval($cliente);
	if ($app < 0) // internal error
		return $allow;

	// Admins can access everything
	if ($author_session['status'] == 'admin' || $author_session['status'] == 'manager')
		return array('r' => true, 'w' => true, 'e' => true, 'a' => true);

	// Access control procedure:
	// 1- Check global site configuration (to see if AC is required)
	// 2- Check if cliente is already on a case for which the user is working on
	// NOTE: "edit" and "admin" are not yet defined nor implemented.
	$meta_cliente_read  = read_meta('cliente_share_read');
	$meta_cliente_write = read_meta('cliente_share_write');

	// Use double-negation to avoid problems if meta not up-to-date
	if (! ($meta_cliente_read == 'no')) {
		$allow['r'] = true;

		if (! ($meta_cliente_write == 'no'))
			$allow['w'] = $allow['e'] = true;
	}

	if ($allow['r'] == false || $allow['w'] == false) {
		// Check if author is associated to a case with this cliente
		$q = "SELECT count(*) as cpt, sum(ac_read) as ac_read, 
					sum(ac_write) as ac_write, sum(ac_edit) as ac_edit, sum(ac_admin) as ac_admin
				FROM lcm_case_adverso_cliente as cco, lcm_case_author as ca
				WHERE ca.id_author = " . $author_session['id_author'] . "
				  AND cco.id_cliente = " . $cliente . "
				  AND ca.id_case = cco.id_case";

		$result = lcm_query($q);

		if ($row = lcm_fetch_array($result)) {
			$allow['r'] = ($row['ac_read'] > 0);
			$allow['w'] = ($row['ac_write'] > 0);
			$allow['e'] = ($row['ac_edit'] > 0);
			$allow['a'] = ($row['ac_admin'] > 0);
		}
	}

	return $allow;
}

// Returns an array with the possible case statuses
// c.f. http://www.direito2010.com.br
function get_possible_case_statuses($status = '') {
	$statuses = array();

	if ($status == 'draft') {
		$statuses = array('draft' => 'draft', 
				'open' => 'opening',
				// 'suspended' => 'suspension',
				'closed' => 'conclusion',
				'merged' => 'merge', 
				'deleted' => 'deletion');
	} elseif ($status == 'open') {
		$statuses = array( // 'draft' => 'draft', 
				'open' => 'opening',
				'suspended' => 'suspension',
				'closed' => 'conclusion',
				'merged' => 'merge', 
				'deleted' => 'deletion');
	} elseif ($status == 'suspended') {
		$statuses = array( // 'draft' => 'draft', 
				'open' => 'opening',
				'suspended' => 'suspension',
				'closed' => 'conclusion',
				'merged' => 'merge', 
				'deleted' => 'deletion');
	} elseif ($status == 'closed') {
		$statuses = array( // 'draft' => 'draft', 
				'open' => 'opening',
				// 'suspended' => 'suspension',
				'closed' => 'conclusion',
				// 'merged' => 'merge', 
				'deleted' => 'deletion');
	} elseif ($status == 'merged') {
		$statuses = array( // 'draft' => 'draft', 
				// 'open' => 'opening',
				// 'suspended' => 'suspension',
				// 'closed' => 'conclusion',
				'merged' => 'merge', 
				'deleted' => 'deletion');
	} elseif ($status == 'deleted') {
		$statuses = array( // 'draft' => 'draft', 
				'open' => 'opening',
				// 'suspended' => 'suspension',
				// 'closed' => 'conclusion',
				// 'merged' => 'merge', 
				'deleted' => 'deletion');
	} else {
		// Send back all
		$statuses = array('draft' => 'draft', 
				'open' => 'opening',
				'suspended' => 'suspension',
				'closed' => 'conclusion',
				'merged' => 'merge', 
				'deleted' => 'deletion');
	}

	return $statuses;
}

function is_status_change($type) {
	$statuses = get_possible_case_statuses();

	foreach($statuses as $key => $val)
		if ($key == $type || $val == $type)
			return true;
	
	return false;
}

?>
