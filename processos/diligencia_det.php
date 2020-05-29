<?php

include('inc/inc.php');
include_lcm('inc_contacts');
include_lcm('inc_acc');

function get_date_range_fields() {
	$ret = array();

	$link = new Link();
	$link->delVar('date_start_day');
	$link->delVar('date_start_month');
	$link->delVar('date_start_year');
	$link->delVar('date_end_day');
	$link->delVar('date_end_month');
	$link->delVar('date_end_year');
	$ret['html'] =  $link->getForm();

	// By default, show from "now() - 1 month" to NOW().
	// Unlike in case_details, we cannot show all, since it would return
	// too many results.
	$ret['html'] .= "<p class=\"normal_text\">\n";
	$ret['date_end'] = get_datetime_from_array($_REQUEST, 'date_end', 'end', "-1");

	$ret['date_start'] = get_datetime_from_array($_REQUEST, 'date_start', 'start',
					date('Y-m-d H:i:s', strtotime("-1 month" . ($ret['date_end'] != "-1" ? $ret['date_end'] : date('Y-m-d H:i:s')))));

	$ret['html'] .= '<span class="label3">' . _Ti('time_input_date_start') . '</span>';
	$ret['html'] .= get_date_inputs('date_start', $ret['date_start']);

	$ret['html'] .= _Ti('time_input_date_end');
	if ($ret['date_end'] == "-1")
		$ret['html'] .= get_date_inputs('date_end');
	else
		$ret['html'] .= get_date_inputs('date_end', $ret['date_end']);

	$ret['html'] .= _T('app_input_title');
	
	// Get author data
	$q = "SELECT * FROM lcm_keyword where id_group = '27' ";
	$result = lcm_query($q);
	$arr_app = array();
	while($app_data = lcm_fetch_array($result)){
		$arr_app[$app_data['name']] = $app_data['title'];
	}
	$ret['html'] .= '<select name="app_title" >';
	$ret['html'] .= '<option value="">Todos</option>';
					foreach($arr_app as $app => $tt){
						$rel = ($_GET['app_title']==$app ? 'selected' : '');
						$ret['html'] .= "<option value=" . $tt . " " . $rel ." >" . $tt . "</option>";
						$app_u .= $tt . "', '";
					}
					$app_all = " AND ap.title in ('".$app_u."') ";
					
	$ret['html'] .= "</select>\n";
	
	$ret['html'] .= "</p>\n";
	$ret['html'] .= "<p>\n";
	$ret['html'] .= '<span class="label3">' . _T('case_input_comarca') . '</span>';
	$ret['html'] .= '<input type="text" name="app_comar" value="' . $_GET['app_comar'] . '"/>';
	$ret['html'] .= _Ti('input_filter_case_condicao') . '
					<select id="condicao_f" name="condicao_f" style="width:10%" >
					<option value="" ></option>';
					foreach(array('Autor'=>'70','R&eacute;u'=>'71') as $app => $ap){
						$rel = ($_GET['condicao_f']==$ap ? 'selected' : '');
						$ret['html'] .= "<option value=" . $ap . " " . $rel ." >" . $app . "</option>";
					}
	$ret['html'] .=	'</select>';
	
	//FT insrindo o usuário
	$ret['html'] .= _T('case_input_author');
	$q = "SELECT * FROM lcm_author ORDER BY name_first";
	$result = lcm_query($q);
	$ret['html'] .= ' <select name="author" >';
	$ret['html'] .= '<option value="all">Todos</option>';
					while($app_data = lcm_fetch_array($result)){
						$rel = ($_GET['author']==$app_data['id_author'] ? 'selected' : '');
						$ret['html'] .= "<option value=" . $app_data['id_author'] . " " . $rel ." >" . $app_data['name_first'] . " " . $app_data['name_middle'] . " " . $app_data['name_last'] . "</option>";
					}
					
	$ret['html'] .= "</select>\n";
	//
	$ret['html'] .= ' <button name="submit" type="submit" value="submit" class="simple_form_btn" style="float:right">'
				. _T('button_validate') 
				. "</button>\n";
	
	$ret['html'] .= "</p>\n";
	$ret['html'] .= "</form>\n";

	return $ret;
}

global $prefs;
global $author_session;

$author = intval(_request('author'));

//FT criando condição para o filtro
if ($_GET['author']=="all") {
	$author = 1;
}

if (! ($author > 0)) {
	lcm_header("Location: listauthors.php");
	exit;
}

// Get author data
$q = "SELECT * FROM lcm_author ORDER BY name_first";
$result = lcm_query($q);

if (! ($author_data = lcm_fetch_array($result))) {
	lcm_header("Location: listauthors.php");
	exit;
}

$tit_app = _T("kw_appointments_" . $_GET['app_title']);

$fullname = get_person_name($author_data);
lcm_page_start(_T('title_author_view_dil') . ' ' . $tit_app, '', '', 'authors_intro');

		// Show tabs
			$groups = array(
				'appointments'=> array('name' => _T('generic_tab_dilig_geral'), 'tooltip' => _T('author_subtitle_appointments', array('author' => $fullname))),
				'app_served'  => array('name' => _T('generic_tab_dilig_served'), 'tooltip' => _T('author_subtitle_appointments_served', array('author' => $fullname))),
				'app_pending' => array('name' => _T('generic_tab_dilig_pending'), 'tooltip' => _T('author_subtitle_appointments_pending', array('author' => $fullname))),
			);

		$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'appointments' );

		show_tabs($groups,$tab, "diligencia_det.php?author=" . $_GET['author'] . ""); 

		echo '<fieldset class="info_box">';
		
		switch ($tab) {
			
			case 'appointments' :

				/* //FT liberando o acesso a todos os usuários
				if (! allowed_author($author, 'r'))
					die("Access denied");
				*/
				
				show_page_subtitle(_T('author_subtitle_appointments_dil', array('author' => get_person_name($author_data))), 'tools_agenda');

				$foo = get_date_range_fields();

				$date_start = $foo['date_start'];
				$date_end   = $foo['date_end'];
				$app_title  = $_GET['app_title'];

				echo $foo['html'];

				echo "<p class=\"normal_text\">\n";

				$q = "SELECT * FROM lcm_case as c 
						JOIN lcm_app as ap on c.id_case = ap.id_case 
						LEFT JOIN lcm_case_adverso_cliente as cco on cco.id_case = c.id_case 
						LEFT JOIN lcm_cliente as o on o.id_cliente = cco.id_cliente 
						LEFT JOIN lcm_author as a on a.id_author = ap.id_author ";
						//LEFT JOIN lcm_author as a on a.id_author = ap.id_author ";
				
				if ( $_GET['condicao_f'] != ""){
					$q .= " LEFT JOIN lcm_keyword_case as kc on kc.id_case = c.id_case ";
				}
				$q .= "	WHERE UNIX_TIMESTAMP(start_time) >= UNIX_TIMESTAMP('" . $date_start . "') ";

				if ($date_end != "-1") 
					$q .= " AND UNIX_TIMESTAMP(end_time) <= UNIX_TIMESTAMP('" . $date_end . "') ";

				$q .= " AND ap.type in ('appointments09') ";
					
				if ( $_GET['app_title'] == ""){
					$q .= $app_all;
				} else {
					$q .= " AND ap.title in ('".$_GET['app_title']."') ";
				}
				if ( $_GET['app_comar'] != ""){
					$q .= " AND c.comarca like '%".$_GET['app_comar']."%' ";
				}
				if ( $_GET['condicao_f'] != ""){
					$q .= " AND kc.id_keyword =  ".$_GET['condicao_f']." ";
				}
					$q .= " AND ap.hidden = 'N' ";
					$q .= " AND ap.performed = 'N' ";
					$q .= " AND datediff(ap.start_time, curdate()) >= 0 ";
				if ($_GET['author']!="all") {
					$q .= " AND ap.id_author = " . $_GET['author'] . " ";
					//$q .= " AND a.id_author = " . $_GET['author'] . " ";
				}
				// Sort agenda by date/time of the appointments
				$order = 'ASC';
				if (isset($_REQUEST['order']))
					if ($_REQUEST['order'] == 'ASC' || $_REQUEST['order'] == 'DESC')
						$order = $_REQUEST['order'];
				
				$q .= " GROUP BY id_app ";
				//$q .= " GROUP BY ap.id_app ";
				$q .= " ORDER BY start_time " . $order;
				
				$result = lcm_query($q);
					
				// Get the number of rows in the result
				$number_of_rows = lcm_num_rows($result);
				if ($number_of_rows) {
					$headers =  array( 
								array( 'title' => _Th('case_input_p_adverso'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_processo_vara'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_comarca'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_type'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_title'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_description'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_status_id'),  'order' => 'no_order'),
								array( 'title' => _Th('time_input_date_start_agenda'), 'order' => 'order', 'default' => 'DESC')
								);
					
					show_list_start($headers);
				
					// Check for correct start position of the list
					$list_pos = 0;
					
					if (isset($_REQUEST['list_pos']))
						$list_pos = $_REQUEST['list_pos'];
					
					if ($list_pos>=$number_of_rows) $list_pos = 0;
					
					// Position to the page info start
					if ($list_pos>0)
						if (!lcm_data_seek($result,$list_pos))
							lcm_panic("Error seeking position $list_pos in the result");
					
					for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
						
						$css = ' class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '"';
						
						echo "\t<tr>";
						echo '<td '.$css.'>' . '<a href="case_det.php?case=' . $row['id_case'] . '" class="content_link" style="text-decoration: none;"><span style="color:#999;">' . $row['name'] . ' x <br></span>' . $row['p_adverso'] . '</a></td>';
						echo '<td '.$css.'>' . $row['processo'] . '<br>' . $row['vara'] . '</td>';
						echo '<td '.$css.'>' . $row['comarca'] . ' - ' . $row['state'] . '</td>';
						echo '<td '.$css.'>' . _Tkw('appointments', $row['type']) . '</td>';
						echo '<td '.$css.'>' . '<a href="app_det.php?app=' . $row['id_app'] . '" class="content_link">' . $row['title'] . '</a></td>';
						echo '<td '.$css.'>' . limitarTexto($row['description'],100) . '</td>';
						echo '<td '.$css.'>' . ($row['performed']=='Y'?'<span style=color:green>âœ”</span>':'<span style=color:red>âœ±</span>') . '</td>';
						echo '<td '.$css.'>' . format_date($row['start_time'], 'short') . '</td>';
						
						echo "</tr>\n";
					}
				
					show_list_end($list_pos, $number_of_rows);
				}
				
				echo "</p>\n";
			break;

			case 'app_served' :
				
				/* //FT liberando o acesso a todos os usuários
				if (! allowed_author($author, 'r'))
					die("Access denied");
				*/

				show_page_subtitle(_T('author_subtitle_appointments_dil', array('author' => get_person_name($author_data))), 'tools_agenda');

				$foo = get_date_range_fields();

				$date_start = $foo['date_start'];
				$date_end   = $foo['date_end'];
				$app_title   = $_GET['app_title'];

				echo $foo['html'];

				echo "<p class=\"normal_text\">\n";

				$q = "SELECT *, DATEDIFF(CURDATE(), ap.start_time) AS data_cp FROM lcm_case as c 
					  JOIN lcm_app as ap on c.id_case = ap.id_case 
					  LEFT JOIN lcm_case_adverso_cliente as cco on cco.id_case = c.id_case 
					  LEFT JOIN lcm_cliente as o on o.id_cliente = cco.id_cliente ";
					  
				if ( $_GET['condicao_f'] != ""){
					$q .= " LEFT JOIN lcm_keyword_case as kc on kc.id_case = c.id_case ";
				}
				$q .= " WHERE UNIX_TIMESTAMP(start_time) >= UNIX_TIMESTAMP('" . $date_start . "') ";
					
				$q .= " AND ap.type in ('appointments09') ";
				
				if ( $_GET['app_title'] == ""){
					$q .= $app_all;
				} else {
					$q .= " AND ap.type in ('".$_GET['app_title']."') ";
				}
				if ( $_GET['app_comar'] != ""){
					$q .= " AND c.comarca like '%".$_GET['app_comar']."%' ";
				}
				if ( $_GET['condicao_f'] != ""){
					$q .= " AND kc.id_keyword =  ".$_GET['condicao_f']." ";
				}
				$q .= " AND DATEDIFF(curdate(), ap.start_time) <= '180' ";
				$q .= " AND performed = 'Y' ";
				if ($_GET['author']!="all") {
					$q .= " AND ap.id_author = " . $_GET['author'] . " ";
				}
				// Sort agenda by date/time of the appointments
				$order = 'DESC';
				if (isset($_REQUEST['order']))
					if ($_REQUEST['order'] == 'ASC' || $_REQUEST['order'] == 'DESC')
						$order = $_REQUEST['order'];
				
				$q .= " GROUP BY id_app ";
				$q .= " ORDER BY start_time " . $order;
				
				$result = lcm_query($q);
				
				// Get the number of rows in the result
				$number_of_rows = lcm_num_rows($result);
				if ($number_of_rows) {
					$headers =  array( 
								array( 'title' => _Th('case_input_p_adverso'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_processo_vara'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_comarca'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_type'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_title'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_description'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_status_id'),  'order' => 'no_order'),
								array( 'title' => _Th('time_input_date_start_agenda'), 'order' => 'order', 'default' => 'DESC')
								);
					
					show_list_start($headers);
				
					// Check for correct start position of the list
					$list_pos = 0;
					
					if (isset($_REQUEST['list_pos']))
						$list_pos = $_REQUEST['list_pos'];
					
					if ($list_pos>=$number_of_rows) $list_pos = 0;
					
					// Position to the page info start
					if ($list_pos>0)
						if (!lcm_data_seek($result,$list_pos))
							lcm_panic("Error seeking position $list_pos in the result");
					
					for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
						echo "\t<tr>";
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . '<a href="case_det.php?case=' . $row['id_case'] . '" class="content_link" style="text-decoration: none;"><span style="color:#999;">' . $row['name'] . ' x <br></span>' . $row['p_adverso'] . '</a></td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['processo'] . '<br>' . $row['vara'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['comarca'] . ' - ' . $row['state'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . _Tkw('appointments', $row['type']) . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . '<a href="app_det.php?app=' . $row['id_app'] . '" class="content_link">' . $row['title'] . '</a></td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['description'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . ($row['performed']=='Y'?'<span style=color:green>âœ”</span>':'<span style=color:red>âœ±</span>') . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . format_date($row['start_time'], 'short') . '</td>';
						
						echo "</tr>\n";
					}
				
					show_list_end($list_pos, $number_of_rows);
				}
				
				echo "</p>\n";
			break;

			case 'app_pending' :
				
				/* //FT liberando o acesso a todos os usuários
				if (! allowed_author($author, 'r'))
					die("Access denied");
				*/

				show_page_subtitle(_T('author_subtitle_appointments_dil', array('author' => get_person_name($author_data))), 'tools_agenda');

				$foo = get_date_range_fields();

				$date_start = $foo['date_start'];
				$date_end   = $foo['date_end'];
				$app_title   = $_GET['app_title'];

				echo $foo['html'];

				echo "<p class=\"normal_text\">\n";

				$q = "SELECT * FROM lcm_case as c 
						JOIN lcm_app as ap on c.id_case = ap.id_case 
						left JOIN lcm_author_app as aap on aap.id_app = ap.id_app 
						LEFT JOIN lcm_case_adverso_cliente as cco on cco.id_case = c.id_case 
						LEFT JOIN lcm_cliente as o on o.id_cliente = cco.id_cliente ";
						
				if ( $_GET['condicao_f'] != ""){
					$q .= " LEFT JOIN lcm_keyword_case as kc on kc.id_case = c.id_case ";
				}
				$q .= "	WHERE 1 = 1 ";
						//WHERE UNIX_TIMESTAMP(start_time) >= UNIX_TIMESTAMP('" . $date_start . "') ";
					
				$q .= " AND ap.type in ('appointments09') ";

				if ( $_GET['app_title'] == ""){
					$q .= $app_all;
				} else {
					$q .= " AND ap.type in ('".$_GET['app_title']."') ";
				}
				if ( $_GET['app_comar'] != ""){
					$q .= " AND c.comarca like '%".$_GET['app_comar']."%' ";
				}
				if ( $_GET['condicao_f'] != ""){
					$q .= " AND kc.id_keyword =  ".$_GET['condicao_f']." ";
				}
				$q .= " AND DATEDIFF(start_time, CURDATE() ) < 0 ";
				$q .= " AND performed = 'N' ";
				if ($_GET['author']!="all") {
					$q .= " AND aap.id_author = " . $_GET['author'] . " ";
				}

				// Sort agenda by date/time of the appointments
				$order = 'DESC';
				if (isset($_REQUEST['order']))
					if ($_REQUEST['order'] == 'ASC' || $_REQUEST['order'] == 'DESC')
						$order = $_REQUEST['order'];
						
				$q .= " GROUP BY ap.id_app ";
				$q .= " ORDER BY start_time " . $order;
		
				$result = lcm_query($q);

				// Get the number of rows in the result
				$number_of_rows = lcm_num_rows($result);
				if ($number_of_rows) {
					$headers =  array( 
								array( 'title' => _Th('case_input_p_adverso'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_processo_vara'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_comarca'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_type'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_title'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_description'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_status_id'),  'order' => 'no_order'),
								array( 'title' => _Th('time_input_date_start_agenda'), 'order' => 'order', 'default' => 'DESC')
								);
					
					show_list_start($headers);
				
					// Check for correct start position of the list
					$list_pos = 0;
					
					if (isset($_REQUEST['list_pos']))
						$list_pos = $_REQUEST['list_pos'];
					
					if ($list_pos>=$number_of_rows) $list_pos = 0;
					
					// Position to the page info start
					if ($list_pos>0)
						if (!lcm_data_seek($result,$list_pos))
							lcm_panic("Error seeking position $list_pos in the result");
					
					for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))) ; $i++) {
						echo "\t<tr>";
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . '<a href="case_det.php?case=' . $row['id_case'] . '" class="content_link" style="text-decoration: none;" ><span style="color:#999;">' . $row['name'] . ' x <br></span>' . $row['p_adverso'] . '</a></td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['processo'] . '<br>' . $row['vara'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['comarca'] . ' - ' . $row['state'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . _Tkw('appointments', $row['type']) . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . '<a href="app_det.php?app=' . $row['id_app'] . '" class="content_link">' . $row['title'] . '</a></td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['description'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . ($row['performed']=='Y'?'<span style=color:green>âœ”</span>':'<span style=color:red>âœ±</span>') . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . format_date($row['start_time'], 'short') . '</td>';
						
						echo "</tr>\n";
					}
				
					show_list_end($list_pos, $number_of_rows);
				}
				
				echo "</p>\n";
			break;
			
		}
		
echo "</fieldset>\n";
//FT Criando a exportação da agenda==========
?>
<script type="text/javascript" src="js/highslide-with-html.js"></script>
<link rel="stylesheet" type="text/css" href="styles/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = 'images/graphics/';
	hs.outlineType = 'rounded-white';
	hs.wrapperClassName = 'draggable-header';
</script>
<script language="javascript">
function fc_cad_dados()
{
	document.form.action = "aj_externo.php";
	document.form.submit();
}
</script>
<form name="form" action="aj_externo.php" method="post" target="_blank" style="margin:0px;">
	<input type="hidden" name="id_dados"  value="<?php echo $q; ?>">
	<input type="hidden" name="flag"  value="exp_agenda">
</form>
<p>
	<a href="#" class="create_new_lnk" onclick="fc_cad_dados();" style="float: right; margin-top: -5px; "><?php echo _T('adverso_button_export'); ?></a>
	<a href="form.php?exp=agenda" onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="create_new_lnk" style="float: right; margin-top: -5px; "><?php echo _T('adverso_button_sendmail'); ?></a>
</p><br/><br/>
<?php

//FT=========================================
lcm_page_end();

?>
