<?php

include('inc/inc.php');
include_lcm('inc_obj_case');
global $author_session;
global $prefs;

lcm_page_start(_T('title_my_cases'), '', '', 'cases_intro');

///FT Informação de Ajuda\/\/\/\/
//lcm_bubble('case_list');

//
// For "find case"
//
$find_case_string = '';

if (_request('find_case_string')) 
{
	$find_case_string = _request('find_case_string');

	// remove useless spaces
	$find_case_string = trim($find_case_string);
	$find_case_string = preg_replace('/ +/', ' ', $find_case_string);

	show_find_box('case', $find_case_string);
}

//
// For "Filter case owner"
//
$prefs_change = false;

$types_owner = array('my' => 1, 'public' => 1);
$types_period = array('m1' => 30, 'm3' => 91, 'm6' => 182, 'y1' => 365); // 30 days, 3 months, 6 months, 1 year

if ($author_session['status'] == 'admin')
	$types_owner['all'] = 1;

if (($v = _request('case_owner'))) 
{
	if ($prefs['case_owner'] != $v) 
	{
		if (! array_key_exists($v, $types_owner))
			lcm_panic("Valor para o proprietário caso não permitidos: " . htmlspecialchars($v));
		
		$prefs['case_owner'] = _request('case_owner');
		$prefs_change = true;
	}
}

// always include 'my' cases

$q_owner = " (a.id_author = " . $author_session['id_author'];

if ($prefs['case_owner'] == 'public')
	$q_owner .= " OR c.public = 1";

if ($author_session['status'] == 'admin' && $prefs['case_owner'] == 'all')
	$q_owner .= " OR 1=1 ";
	$q_owner .= " ) ";

//
// For "Filter case date_creation"
//
if (($v = intval(_request('case_period'))))
{
	if ($prefs['case_period'] != $v) 
	{
		// [ML] Ignoring filter, since case period may be 1,5,50 days, but also v = 2005, 2006, etc.
		// if (! array_search($v, $types_period))
		//	lcm_panic("Value for case period not permitted: " . htmlspecialchars($v));

		$prefs['case_period'] = $v;
		$prefs_change = true;
	}
}

if ($prefs_change) {
	lcm_query("UPDATE lcm_author
				SET   prefs = '" . addslashes(serialize($prefs)) . "'
				WHERE id_author = " . $author_session['id_author']);
}

//
// Show filters form
//

include 'filtros.php';

//	//diligências do apoio ma tela inicial
//	echo '<div class="nav_column_menu_head">'; 
//	echo '<div class="mm_agenda">'. _T('menu_dilig') . '<a href="diligencia_det.php?author=all"  style="float:right">Painel</a></div>';
//	echo "</div>\n";
//	// Show appointments for today
//		$q = "SELECT app.id_app, app.start_time, app.type, app.title, app.description, app.performed, 
//			date_format(app.start_time, '%d/%m/%Y %H:%i:%s') as start_data, c.processo, c.legal_reason, c.p_cliente, 
//			c.p_adverso, c.comarca, c.state, c.vara, c.id_case
//			FROM lcm_app as app 
//			LEFT JOIN lcm_author_app as aut on aut.id_app = app.id_app  
//			LEFT JOIN lcm_case as c on c.id_case = app.id_case 
//			WHERE 1 = 1 
//			" . ($GLOBALS['author_session']['status']== 'admin' || $GLOBALS['author_session']['status']== 'manager' ? '' : 'AND aut.id_author='. $GLOBALS['author_session']['id_author']) . "
//			AND app.`type`='appointments09'
//			AND app.id_app = aut.id_app 
//			AND app.performed not in ('Y') 
//			AND " . lcm_query_trunc_field('app.start_time', 'day') . "
//			= " . lcm_query_trunc_field('NOW()', 'day') . "
//			GROUP BY app.id_app 
//			ORDER BY app.reminder ASC";
//			//AND app.type in ('court_session','appointments04','appointments05') 
//	
//		$result = lcm_query($q);
//	
//		if (lcm_num_rows($result) > 0) {
//			$events = true;
//			$today = getdate(time());
//	
//			echo "<p class=\"nav_column_text\" >\n"
//				. '<strong><a class="content_link" href="diligencia_det.php?author=all" style="color:red">'
//				. _Th('calendar_button_now') . "</a></strong><br />\n";
//			echo "</p>\n";
//			echo "<ul class=\"small_agenda\">\n";
//			
//			$arr_today = array();
//			while ($row=lcm_fetch_array($result)) {
//				//FT criando o envio de e-mail dos agendamentos de hoje
//				$q_send = "SELECT * from lcm_sendmail where id_app = '". $row['id_app'] ."' AND nivel = 2 ";
//				$r_send = lcm_query($q_send);
//				if(date('H') == $send_hora) {
//					if (lcm_num_rows($r_send) == 0) {
//						$arr_today [] = $row;
//						mysql_query("INSERT INTO lcm_sendmail (id_app, nivel, date_send) VALUES('". $row['id_app'] ."', '2', '". date('Y-m-d H:i:s')."')");
//						$num=1;
//					}
//				}
//			//
//				echo "<li><a href=\"app_det.php?app=" . $row['id_app'] . "\" style='color:red'>"
//					. heures($row['start_time']) . ':' . minutes($row['start_time']) . " - " . _T('kw_appointments_' . $row['type']) . " : " . $row['title'] . " - " . $row['description'] . "</a></li>\n";
//			}			
//			echo "</ul>\n";
//			echo "<hr class=\"hair_line\" />\n";
//			//FT Incluindo a condição para envio do e-mail
//			if(date('H') == $send_hora) {
//				if($num==1){ 
//					sendmail($arr_today);
//					$num="";
//				}
//			}
//			//
//		}
//	// Show next diligences
//		$q = "SELECT a.id_app, a.start_time, a.type, a.title, a.performed, a.description 
//			FROM lcm_app as a, lcm_author_app as aa
//			WHERE 1 = 1 
//			" . ($GLOBALS['author_session']['status']== 'admin' || $GLOBALS['author_session']['status']== 'manager' ? '' : 'AND aa.id_author='. $GLOBALS['author_session']['id_author']) . " 
//			AND a.`type`='appointments09' 
//			AND a.id_app = aa.id_app 
//			AND a.performed not in ('Y') 
//			AND a.start_time >= '" . date('Y-m-d H:i:s',((int) ceil(time()/86400)) * 86400) ."' 
//			ORDER BY a.reminder ASC
//			LIMIT 5";
//		
//		$result = lcm_query($q);
//		
//		if (lcm_num_rows($result)>0) {
//			$events = true;
//			echo "<p class=\"nav_column_text\">\n";
//			echo "<strong>" . _T('calendar_button_nextdilig') . "</strong><br />\n";
//			echo "</p>\n";
//		
//			echo "<ul class=\"small_agenda\">\n";
//			while ($row=lcm_fetch_array($result)) {
//				echo "<li><a href=\"app_det.php?app=" . $row['id_app'] . "\">"
//					. format_date($row['start_time'],'short') . " - " . _T('kw_appointments_' . $row['type']) . " : " . $row['title'] . " - " . $row['description'] . "</a></li>\n";
//			}
//			echo "</ul>\n";
//		}
///////////////////

lcm_page_end();
?>