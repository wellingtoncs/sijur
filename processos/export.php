<?php

include('inc/inc_version.php');
include_lcm('inc_auth');
include_lcm('inc_filters');
include_lcm('inc_impex');
include_lcm('inc_xml');

if ($GLOBALS['author_session']['status'] != 'admin')
	lcm_panic("You don't have permission to export!");

$item = clean_input($_REQUEST['item']);
if (!empty($_REQUEST['id']))
	$id = intval($_REQUEST['id']);

$data = array();
switch ($item) {
	case 'case' :
		load_case($id, $data, _LOAD_ALL);
		break;
	case 'followup' :
		$data = load_followup($id, $data, _LOAD_ALL);
		break;
	case 'adverso' :
		$data = load_adverso($id, $data, _LOAD_ALL);
		break;
	case 'cliente' :
		$data = load_cliente($id, $data, _LOAD_ALL);
		break;
	default :
		lcm_panic("Incorrect export item type!");
		exit;
}

// Send proper headers to browser
header("Content-Type: text/xml");
header("Content-Disposition: filename={$item}_{$id}.xml");
header("Content-Description: " . "Export of {$item} ID{$id}");

echo '<?xml version="1.0"?>' . "\n";
echo xml_encode("{$item}_{$id}",$data);

?>