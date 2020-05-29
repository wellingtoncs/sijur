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

	$Id: inc_impex.php,v 1.8 2006/02/20 03:44:09 mlutfy Exp $
*/

// Execute this file only once
if (defined('_INC_IMPEX')) return;
define('_INC_IMPEX', '1');

include('inc/inc_db.php');

function export($type, $format, $search = '') {
	switch ($type) {
		case 'case' :
			// List cases in the system + search criterion if any
			$q = "SELECT id_case,p_adverso,legal_reason,p_cliente,comarca,notes,status,stage
					FROM lcm_case";

			if (strlen($search)>1) {
				// Add search criteria
				$q .= " WHERE ((title LIKE '%$search%')
						OR (status LIKE '%$search%')
						OR (stage LIKE '%$search%'))";
			}

			break;

		case 'adverso' :
			// List adversos in the system + search criterion if any
			$q = "SELECT id_adverso,name_first,name_middle,name_last,citizen_number,cpfcnpj,civil_status,income,gender,notes
					FROM lcm_adverso";

			if (strlen($search)>1) {
				// Add search criteria
				$q .= " WHERE ((name_first LIKE '%$search%')
						OR (name_middle LIKE '%$search%')
						OR (name_last LIKE '%$search%'))";
			}

			break;

		case 'cliente' :
			// List clienteanizations in the system + search criterion if any
			$q = "SELECT id_cliente,name,notes,court_reg,tax_number,stat_number
					FROM lcm_cliente";

			if (strlen($search)>1) {
				// Add search criteria
				$q .= " WHERE (name LIKE '%$search%')";
			}

			break;
			
		case 'contrato' :
			// List contracts in the system + search criterion if any - WL
			$q = "select k.id_case, c.p_adverso, k.value from lcm_keyword_case k 
				join lcm_case c on c.id_case = k.id_case ";

			if (strlen($search)>1) {
				// Add search criteria
				$q .= " WHERE (k.value LIKE '%$search%')";
			}

		break;

		default:
			lcm_panic("invalid type: $type");
			return 0;

	}

	$mime_types = array(	'csv' => 'text/comma-separated-values',
				'xml' => 'text/xml');
	if (!($mime_type = $mime_types[$format])) {
		lcm_panic("invalid type: $type");
		return 0;
	}

	$result = lcm_query($q);
	if (lcm_num_rows($result) > 0) {
		// Send proper headers to browser
		header("Content-Type: " . $mime_type);
		header("Content-Disposition: filename=$type.$format");
		header("Content-Description: " . "Export of {$type}s");
		header("Content-Transfer-Encoding: binary");
//		echo ( get_magic_quotes_runtime() ? stripslashes($row['content']) : $row['content'] );

		// Document start
		switch ($format) {
			case 'csv' :
				// Export columns headers
				break;
			case 'xml' :
				echo "<document>\r\n";
				break;
		}

		// Document contents
		while ($row = lcm_fetch_assoc($result)) {
			// Export row start
			switch ($format) {
				case 'csv' :
					break;
				case 'xml' :
					echo "\t<row>\r\n";
					break;
			}
			// Prepare row fields
			$fields = array();
			foreach($row as $key => $value) {
				// Remove escaping if any
				$value = ( get_magic_quotes_runtime() ? stripslashes($value) : $value );
				switch ($format) {
					case 'csv' :
						if (is_string($value)) {
							// Escape double quote in CVS style
							$value = str_replace('"', '""', $value);
							// Add double quotes
							$value = "\"$value\"";
						}
						
						break;
					case 'xml' :
						$value = (is_string($value) ? htmlspecialchars($value) : $value);
						$value = "\t\t<$key>$value</$key>\r\n";
						break;
				}
				$fields[] = $value;
			}
			// Export row end
			switch ($format) {
				case 'csv' :
					echo join(',',$fields) . "\r\n";
					break;
				case 'xml' :
					echo join('',$fields);
					echo "\t</row>\r\n";
					break;
			}
		}

		// Document end
		switch ($format) {
			case 'csv' :
				break;
			case 'xml' :
				echo "</document>\r\n";
				break;
		}
	}
}

//---------------------------------------------------------------------------------------
// Load/Put item functions
//---------------------------------------------------------------------------------------
// The following functions read from/write to database various items

// Load scope constants
define("_LOAD_ALL",65535);	// Temporary, allows 16 flags
define("_LOAD_CASE",1);		// Load case(s) data
define("_LOAD_FU",2);		// Load followup(s) data
define("_LOAD_CLIENT",4);	// Load adverso(s) data
define("_LOAD_ORG",8);		// Load clienteanization(s) data
define("_LOAD_ATTACHMENT",16);	// Load attachment(s)
define("_LOAD_CONTACTS",32);	// Load contacts information

// Loads case from database; $id - case ID; $scope - what information to load
function load_case($id, &$case_data, $scope = 0) {
	// Load case data
	$result = lcm_query("SELECT * FROM lcm_case WHERE id_case=$id");
	$case_data['case']["ID$id"] = lcm_fetch_assoc($result);

	// Load the associated items - followups, adversos, clientes, attachnments
	if ($scope & _LOAD_FU) {
		$result = lcm_query("SELECT * FROM lcm_followup WHERE id_case=$id");
		while ($row = lcm_fetch_assoc($result)) {
			load_followup($row['id_followup'], $case_data);
		}
	}
	if ($scope & _LOAD_CLIENT) {
		$result = lcm_query("SELECT * FROM lcm_case_adverso_cliente WHERE id_case=$id AND id_adverso>0");
		while ($row = lcm_fetch_assoc($result)) {
			$case_data['relation']['case-adverso-cliente']['ID' . join('-',$row)] = $row;
			load_adverso($row['id_adverso'], $case_data, $scope & (_LOAD_ATTACHMENT | _LOAD_CONTACTS));
		}
	}
	if ($scope & _LOAD_ORG) {
		$result = lcm_query("SELECT * FROM lcm_case_adverso_cliente WHERE id_case=$id AND id_cliente>0");
		while ($row = lcm_fetch_assoc($result)) {
			$case_data['relation']['case-adverso-cliente']['ID' . join('-',$row)] = $row;
			load_cliente($row['id_cliente'], $case_data, $scope & (_LOAD_ATTACHMENT | _LOAD_CONTACTS));
		}
	}
	if ($scope & _LOAD_ATTACHMENT) {
		$result = lcm_query("SELECT * FROM lcm_case_attachment WHERE id_case=$id");
		while ($row = lcm_fetch_assoc($result)) {
			$row['content'] = base64_encode($row['content']);
			$case_data['case']["ID$id"]['attachment']['ID' . $row['id_attachment']] = $row;
		}
	}
}

// Loads followup from database; $id - followup ID; $scope - what information to load
function load_followup($id, &$fu_data, $scope = 0) {
	// Load followup data
	$result = lcm_query("SELECT * FROM lcm_followup WHERE id_followup=$id");
	$fu_data['followup']["ID$id"] = lcm_fetch_assoc($result);

	// Load the associated items - cases
	if ($scope & _LOAD_CASE) {
		load_case($fu_data['followup']["ID$id"]['id_case'], $fu_data);
	}
}

// Loads keyword from database; $id - keyword ID
function load_kw($id, &$kw_data, $scope = 0) {
	// Load keyword data
	$result = lcm_query("SELECT * FROM lcm_keyword WHERE id_keyword=$id");
	$kw_data['keyword']["ID$id"] = lcm_fetch_assoc($result);

	// Load the associated keyword group
	if ($kw_data['keyword']["ID$id"]['id_group']>0 && $scope>0)
		load_kwg($kw_data['keyword']["ID$id"]['id_group'], $kw_data);
}

// Loads keyword group from database; $id - keyword ID; $scope - what information to load
function load_kwg($id, &$kwg_data, $scope = 0) {
	// Load keyword group data
	$result = lcm_query("SELECT * FROM lcm_keyword_group WHERE id_group=$id");
	$kwg_data['keyword_group']["ID$id"] = lcm_fetch_assoc($result);

	// Load the group member keyword(s)
	if ($scope>0) {
		$res_kw = lcm_query("SELECT * FROM lcm_keyword WHERE id_group=" . $kwg_data['keyword_group']["ID$id"]['id_group']);
		while ($row = lcm_fetch_assoc($res_kw)) {
			$kwg_data['keyword']["ID" . $row['id_keyword']] = $row;
		}
	}
}

// Loads adverso from database; $id - adverso ID; $scope - what information to load
function load_adverso($id, &$adverso_data, $scope = 0) {
	// Load adverso data
	$result = lcm_query("SELECT * FROM lcm_adverso WHERE id_adverso=$id");
	$adverso_data['adverso']["ID$id"] = lcm_fetch_assoc($result);

	// Load the associated items - cases, clientes, attachnments
	if ($scope & _LOAD_CASE) {
		$result = lcm_query("SELECT * FROM lcm_case_adverso_cliente WHERE id_adverso=$id");
		while ($row = lcm_fetch_assoc($result)) {
			$adverso_data['relation']['case-adverso-cliente']['ID' . join('-',$row)] = $row;
			load_case($row['id_case'], $adverso_data, $scope & (_LOAD_ATTACHMENT | _LOAD_CONTACTS));
		}
	}
	if ($scope & _LOAD_ORG) {
		$result = lcm_query("SELECT * FROM lcm_adverso_cliente WHERE id_adverso=$id AND id_cliente>0");
		while ($row = lcm_fetch_assoc($result)) {
			$adverso_data['relation']['adverso-cliente']['ID' . join('-',$row)] = $row;
			load_cliente($row['id_cliente'], $adverso_data, $scope & (_LOAD_ATTACHMENT | _LOAD_CONTACTS));
		}
	}
	if ($scope & _LOAD_ATTACHMENT) {
		$result = lcm_query("SELECT * FROM lcm_adverso_attachment WHERE id_adverso=$id");
		while ($row = lcm_fetch_assoc($result)) {
			$row['content'] = base64_encode($row['content']);
			$adverso_data['adverso']["ID$id"]['attachment']['ID' . $row['id_attachment']] = $row;
		}
	}

	if ($scope & _LOAD_CONTACTS) {
		$result = lcm_query("	SELECT * FROM lcm_contact WHERE type_person='adverso' AND id_of_person=$id");
		while ($row = lcm_fetch_assoc($result)) {
			$adverso_data['adverso']["ID$id"]['contact']['ID' . $row['id_contact']] = $row;
			load_kw($row['type_contact'], $adverso_data, _LOAD_ALL);
		}
	}
}

// Loads clienteanization from database; $id - cliente ID; $scope - what information to load
function load_cliente($id, &$cliente_data, $scope = 0) {
	// Load clienteanization data
	$result = lcm_query("SELECT * FROM lcm_cliente WHERE id_cliente=$id");
	$cliente_data['clienteanization']["ID$id"] = lcm_fetch_assoc($result);

	// Load the associated items - cases, adversos, attachnments
	if ($scope & _LOAD_CASE) {
		$result = lcm_query("SELECT * FROM lcm_case_adverso_cliente WHERE id_cliente=$id");
		while ($row = lcm_fetch_assoc($result)) {
			$cliente_data['relation']['case-adverso-cliente']['ID' . join('-',$row)] = $row;
			load_case($row['id_case'], $cliente_data, $scope & (_LOAD_ATTACHMENT | _LOAD_CONTACTS));
		}
	}
	if ($scope & _LOAD_CLIENT) {
		$result = lcm_query("SELECT * FROM lcm_adverso_cliente WHERE id_cliente=$id AND id_adverso>0");
		while ($row = lcm_fetch_assoc($result)) {
			$cliente_data['relation']['adverso-cliente']['ID' . join('-',$row)] = $row;
			load_adverso($row['id_adverso'], $cliente_data, $scope & (_LOAD_ATTACHMENT | _LOAD_CONTACTS));
		}
	}
	if ($scope & _LOAD_ATTACHMENT) {
		$result = lcm_query("SELECT * FROM lcm_cliente_attachment WHERE id_cliente=$id");
		while ($row = lcm_fetch_assoc($result)) {
			$row['content'] = base64_encode($row['content']);
			$cliente_data['clienteanization']["ID$id"]['attachment']['ID' . $row['id_attachment']] = $row;
		}
	}

	if ($scope & _LOAD_CONTACTS) {
		$result = lcm_query("	SELECT * FROM lcm_contact WHERE type_person='cliente' AND id_of_person=$id");
		while ($row = lcm_fetch_assoc($result)) {
			$cliente_data['clienteanization']["ID$id"]['contact']['ID' . $row['id_contact']] = $row;
			load_kw($row['type_contact'], $cliente_data, _LOAD_ALL);
		}
	}
}

?>
