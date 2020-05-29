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

	$ret['html'] .= _T('time_input_app_type');
	
	// Get author data
	$q = "SELECT * FROM lcm_keyword where id_group = '8'";
	$result = lcm_query($q);
	$arr_app = array();
	while($app_data = lcm_fetch_array($result)){
		$arr_app[$app_data['name']] = $app_data['name'];
	}
	$ret['html'] .= '<select name="app_type" >';
	$ret['html'] .= '<option value="">Todos</option>';
					foreach($arr_app as $app){
						$rel = ($_GET['app_type']==$app ? 'selected' : '');
						$ret['html'] .= "<option value=" . $app . " " . $rel ." >" . _T('kw_appointments_' . $app) . "</option>";
						$app_u .= $app . "', '";
						$app_all = " AND ap.type in ('".$app_u."') ";
					}
					
	$ret['html'] .= "</select>\n";
	
	$ret['html'] .= "</p>\n";
	$ret['html'] .= "<p>\n";
	$ret['html'] .= '<span class="label3">' . _T('case_input_comarca') . '</span>';
	$ret['html'] .= '<input type="text" name="app_comar" value="' . $_GET['app_comar'] . '"/>';
	$ret['html'] .= _Ti('input_filter_case_condicao') . '
					<select id="estado_f" name="estado_f" style="width:10%" >
					<option value="" ></option>';
					$estQ = mysql_query("SELECT l.jornal FROM lcm_pub as l group by l.jornal");
					while($estW = mysql_fetch_array($estQ)){
						$rel = ($_GET['estado_f']==$estW['jornal'] ? 'selected' : '');
						$ret['html'] .= "<option value=" . $estW['jornal'] . " " . $rel ." >" . $estW['jornal'] . "</option>";
					}
	$ret['html'] .=	'</select>';
	$ret['html'] .= ' <button name="submit" type="submit" value="submit" class="simple_form_btn" style="float:right">'
				. _T('button_validate') 
				. "</button>\n";
	
	//FT insrindo o usuário
	$ret['html'] .= _T('case_input_author');
	$q = "SELECT * FROM lcm_author";
	$result = lcm_query($q);
	$ret['html'] .= ' <select name="author" >';
	$ret['html'] .= '<option value="all">Todos</option>';
					while($app_data = lcm_fetch_array($result)){
						$rel = ($_GET['author']==$app_data['id_author'] ? 'selected' : '');
						$ret['html'] .= "<option value=" . $app_data['id_author'] . " " . $rel ." >" . $app_data['name_first'] . " " . $app_data['name_middle'] . " " . $app_data['name_last'] . "</option>";
					}
					
	$ret['html'] .= "</select>\n";
	//
	
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
	//lcm_header("Location: listauthors.php");
	//exit;
}

// Get author data
$q = "SELECT * FROM lcm_author";
$result = lcm_query($q);

if (! ($author_data = lcm_fetch_array($result))) {
	//lcm_header("Location: listauthors.php");
	//exit;
}

$tit_app = _T("kw_appointments_" . $_GET['app_type']);

$fullname = get_person_name($author_data);
lcm_page_start(_T('title_author_view_pub') . ' ' . $tit_app, '', '', 'authors_intro');

		// Show tabs
			$groups = array(
				'publication' => array('name' => _T('generic_tab_public_geral'), 
								'tooltip' => _T('author_subtitle_appointments', array('author' => $fullname))),
				'pub_served' => array('name' => _T('generic_tab_public_served'), 
								'tooltip' => _T('author_subtitle_appointments_served', array('author' => $fullname))),
				'pub_pending' => array('name' => _T('generic_tab_public_pending'), 
								'tooltip' => _T('author_subtitle_appointments_pending', array('author' => $fullname))),
				'pub_update' => array('name' => _T('generic_tab_public_update'), 
								'tooltip' => _T('author_subtitle_appointments_update', array('author' => $fullname)))

			);

		$tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'publication' );

		show_tabs($groups,$tab, "publicacao.php?author=" . $_GET['author'] . ""); 

		echo '<fieldset class="info_box">';
		
		switch ($tab) {
			
			case 'publication' :
			
				show_page_subtitle(_T('author_subtitle_appointments_pub', array('author' => get_person_name($author_data))), 'tools_agenda');

				$foo = get_date_range_fields();

				$date_start = $foo['date_start'];
				$date_end   = $foo['date_end'];
				$app_type   = $_GET['app_type'];

				echo $foo['html'];

				echo "<p class=\"normal_text\">\n";

				$q = " SELECT * from lcm_pub as p ";
				
				
				
				
				$q .= "	WHERE UNIX_TIMESTAMP(start_pub) >= UNIX_TIMESTAMP('" . $date_start . "') ";

				if ( $_GET['estado_f'] != ""){
					$q .= " AND p.jornal = '".$_GET['estado_f']."' ";
				}
				if ($date_end != "-1") 
					$q .= " AND UNIX_TIMESTAMP(end_pub) <= UNIX_TIMESTAMP('" . $date_end . "') ";

					
				//if ( $_GET['app_type'] == ""){
				//	$q .= $app_all;
				//} else {
				//	$q .= " AND ap.type in ('".$_GET['app_type']."') ";
				//}
				//if ( $_GET['app_comar'] != ""){
				//	$q .= " AND c.comarca like '%".$_GET['app_comar']."%' ";
				//}
				//if ( $_GET['condicao_f'] != ""){
				//	$q .= " AND kc.id_keyword =  ".$_GET['condicao_f']." ";
				//}
				//	$q .= " AND ap.hidden = 'N' ";
				//	$q .= " AND ap.performed = 'N' ";
				//	$q .= " AND datediff(ap.start_time, curdate()) >= 0 ";
				//if ($_GET['author']!="all") {
				//	$q .= " AND ap.id_author = " . $_GET['author'] . " ";
				//}
				
				// Sort agenda by date/time of the appointments
				$order = 'ASC';
				if (isset($_REQUEST['order']))
					if ($_REQUEST['order'] == 'ASC' || $_REQUEST['order'] == 'DESC')
						$order = $_REQUEST['order'];
				
				$q .= " GROUP BY id_pub ";
				$q .= " ORDER BY start_pub " . $order;
				
				$result = lcm_query($q); 
					
				// Get the number of rows in the result
				$number_of_rows = lcm_num_rows($result);
				if ($number_of_rows) {
					$headers =  array( 
								array( 'title' => _Th('pub_input_id'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_id'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_processo_vara'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_journal'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_namep'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_description'), 'order' => 'no_order'),
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
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . '<a href="pub_det.php?pub=' . $row['id_pub'] . '" class="content_link">' . $row['id_pub'] . '</a></td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . ($row['id_case']==0?'':'<a href="case_det.php?case=' . $row['id_case'] . '" class="content_link" style="text-decoration: none;">' . $row['id_case'] . '</a>').'</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['numero_processo'] . '<br>' . $row['tribunal'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['jornal'] . ' - ' . $row['state'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['nome_pesquisado'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . substr($row['publicacao'],0,30) . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . format_date($row['start_pub'], 'short') . '</td>';
						
						echo "</tr>\n";
					}
				
					show_list_end($list_pos, $number_of_rows);
				}
				
				echo "</p>\n";
			break;

			case 'pub_served' :
				
				show_page_subtitle(_T('author_subtitle_appointments_pub', array('author' => get_person_name($author_data))), 'tools_agenda');

				$foo = get_date_range_fields();

				$date_start = $foo['date_start'];
				$date_end   = $foo['date_end'];
				$app_type   = $_GET['app_type'];

				echo $foo['html'];

				echo "<p class=\"normal_text\">\n";

				$q = " SELECT * from lcm_pub as p ";
				// Sort agenda by date/time of the appointments
				$order = 'ASC';
				if (isset($_REQUEST['order']))
					if ($_REQUEST['order'] == 'ASC' || $_REQUEST['order'] == 'DESC')
						$order = $_REQUEST['order'];
				
				$q .= " where p.performed='Y' ";
				
				if ( $_GET['estado_f'] != ""){
					$q .= " AND p.jornal = '".$_GET['estado_f']."' ";
				}
				
				$q .= " GROUP BY id_pub ";
				$q .= " ORDER BY start_pub " . $order;
				
				$result = lcm_query($q);
					
				// Get the number of rows in the result
				$number_of_rows = lcm_num_rows($result);
				if ($number_of_rows) {
					$headers =  array( 
								array( 'title' => _Th('pub_input_id'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_id'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_processo_vara'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_journal'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_namep'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_description'), 'order' => 'no_order'),
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
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . '<a href="pub_det.php?pub=' . $row['id_pub'] . '" class="content_link">' . $row['id_pub'] . '</a></td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . ($row['id_case']==0?'':'<a href="case_det.php?case=' . $row['id_case'] . '" class="content_link" style="text-decoration: none;">' . $row['id_case'] . '</a>').'</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['numero_processo'] . '<br>' . $row['tribunal'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['jornal'] . ' - ' . $row['state'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['nome_pesquisado'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . substr($row['publicacao'],0,30) . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . format_date($row['start_pub'], 'short') . '</td>';
						
						echo "</tr>\n";
					}
				
					show_list_end($list_pos, $number_of_rows);
				}
			
				echo "</p>\n";
			break;

			case 'pub_pending' :
				
				show_page_subtitle(_T('author_subtitle_appointments_pub', array('author' => get_person_name($author_data))), 'tools_agenda');

				$foo = get_date_range_fields();

				$date_start = $foo['date_start'];
				$date_end   = $foo['date_end'];
				$app_type   = $_GET['app_type'];

				echo $foo['html'];

				echo "<p class=\"normal_text\">\n";

				$q = " SELECT * from lcm_pub as p ";
				// Sort agenda by date/time of the appointments
				$order = 'ASC';
				if (isset($_REQUEST['order']))
					if ($_REQUEST['order'] == 'ASC' || $_REQUEST['order'] == 'DESC')
						$order = $_REQUEST['order'];
				
				$q .= " where p.performed='N' ";
				
				if ( $_GET['estado_f'] != ""){
					$q .= " AND p.jornal = '".$_GET['estado_f']."' ";
				}
				
				$q .= " GROUP BY id_pub ";
				$q .= " ORDER BY start_pub " . $order;
				
				$result = lcm_query($q);
					
				// Get the number of rows in the result
				$number_of_rows = lcm_num_rows($result);
				if ($number_of_rows) {
					$headers =  array( 
								array( 'title' => _Th('pub_input_id'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_id'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_processo_vara'), 'order' => 'no_order'),
								array( 'title' => _Th('case_input_journal'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_namep'), 'order' => 'no_order'),
								array( 'title' => _Th('app_input_description'), 'order' => 'no_order'),
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
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . '<a href="pub_det.php?pub=' . $row['id_pub'] . '" class="content_link">' . $row['id_pub'] . '</a></td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . ($row['id_case']==0?'':'<a href="case_det.php?case=' . $row['id_case'] . '" class="content_link" style="text-decoration: none;">' . $row['id_case'] . '</a>').'</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['numero_processo'] . '<br>' . $row['tribunal'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['jornal'] . ' - ' . $row['state'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . $row['nome_pesquisado'] . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . substr($row['publicacao'],0,30) . '</td>';
						echo '<td class="tbl_cont_' . ($i % 2 ? 'dark' : 'light') . '">' . format_date($row['start_pub'], 'short') . '</td>';
						
						echo "</tr>\n";
					}
				
					show_list_end($list_pos, $number_of_rows);
				}
			
				echo "</p>\n";
			break;
			
			case 'pub_update' :
			
			?>
				<script type='text/javascript'>
					function carregar_pub(valor1){
						//alert($('#int_pub').val());
						//if( $('#int_pub').val()==''){
						//	$('#mydiv1').html('Preencha o campo com a data');
						//}else{						
							$('#mydiv1').html("<img src='images/loader.gif'/>");
							//$('#carregar_pub').html('<?php echo _T('title_upgrade_publications'); ?>...');
							//$('#carregar_pub').attr('disabled',true);
							$.ajax({
								type: 'POST',
								url:  'ajax_pub.php',
								data: 'data1='+valor1,
								success: function(retorno_ajax){
									$('#mydiv1').html(retorno_ajax);
									//$('#carregar_pub').html('<?php echo _T('title_upgrade_publications'); ?>');
									//$('#carregar_pub').attr('disabled',false);
								}
							});
						//}
					}
				</script>
				<table align='center' height='300px' width='100%' border='0' style='border-collapse:collapse'> 
					<tr height='100px'>
						<td align='left' colspan='2'>
							<?php
							$diasemana = array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabado');
							$L    = -4;
							$nL   = $L - ($_GET['lista']);
							$pagA = $_GET['lista'] +1;
							$pagP = $_GET['lista'] -1;
						
							$data_usada = date('Y-m-d', strtotime($nL." days",strtotime(date('y-m-d'))));
							$data2 = date('Y-m-d', strtotime("+7 days",strtotime($data_usada)));
							//verificando as publicações já baixadas
							$Qdatapub = mysql_query("SELECT DATE_FORMAT(p.start_pub,'%Y%m%d') AS 'datapub', count(p.start_pub) as 'qtd'
										FROM lcm_pub AS p where p.start_pub > '".$data_usada."'	and p.start_pub <= '".$data2."'
										GROUP BY p.start_pub ORDER BY p.start_pub ASC");
							$arr_data_pub[] = array();
							$arr_qtd_pub[] = array();
							while($Wdatapub = mysql_fetch_array($Qdatapub)){
								$arr_data_pub[] = $Wdatapub['datapub'];
								$arr_qtd_pub[$Wdatapub['datapub']] = $Wdatapub['qtd'];
							}
							echo _T('title_upgrade_publications') . " - <span><b>Lig Contato</b></span><br/><br/>"; 
							$dta = (int) date('Ymd');
							echo "<div style='height:20px'><span style='height:10px; background:#00D96D; border:1px solid #A8A8A8;'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:8pt'>". utf8_encode(" Carregadas")			."</span></div>";
							echo "<div style='height:20px'><span style='height:10px; background:#FF2626; border:1px solid #A8A8A8;'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:8pt'>". utf8_encode(" Não Carregadas")		."</span></div>";
							echo "<div style='height:20px'><span style='height:10px; background:#FFFF4F; border:1px solid #CCCCCC;'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:8pt'>". utf8_encode(" Publicações de Hoje") ."</span></div>";
							echo "<div style='height:20px'><span style='height:10px; background:#FCE0C9; border:1px solid #F8B681;'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:8pt'>". utf8_encode(" Final de Semana")		."</span></div>";
							echo "<div style='height:20px'><span style='height:10px; background:#ECECFB; border:1px solid #CCCCCC;'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:8pt'>". utf8_encode(" Datas Futuras")		."</span></div>";
							echo "<br/>";
							?>
						</td>
					</tr>
					<tr height='100px'>
						<td align='center' colspan='2'>		
							<?php
							$i = 1;
							while ($i <= 7){
								$datas = date('Y-m-d', strtotime("+".$i." days",strtotime($data_usada)));
								$dt = (int) str_replace("-","",$datas);
								$diasemana_numero = date('w', strtotime($datas));
								$diasemana[$diasemana_numero];
								if($diasemana_numero==0 || $diasemana_numero==6){
									$backg = "background:#FCE0C9; border:1px solid #F8B681; margin:3px; color: #F48F3E";
								}else{
									if($dt==$dta){
										$backg = "cursor:pointer; background:#FFFF4F; border:1px solid #BBBB00; margin:3px; color: #9F9F00";
									}elseif($dt<$dta){
										if(in_array($dt,$arr_data_pub)){
											$backg = "cursor:pointer;background:#00D96D; border:1px solid #008844; margin:3px; color: #005E2F";											
										}else{
											$backg = "cursor:pointer;background:#FF2626; border:1px solid #A8A8A8; margin:3px; color: #F7AC6F";											
										}
									}else{
										$backg = "background:#ECECFB; border:1px solid #D7D7F7; margin:3px; color: #B7B7F0";
									}
								}
								
								$param = "height:80px; width:9%;" . $backg;
								$exdata = explode("-",$datas);
								$dt_pub = $exdata[2]."/".$exdata[1]."/".$exdata[0];
								echo "<button style='$param' onclick='carregar_pub(\"$dt_pub\")'>".$exdata[2]."/".$exdata[1]."<br>".$exdata[0]."<br>".utf8_encode($diasemana[$diasemana_numero])."<br>-".($arr_qtd_pub[$dt]?$arr_qtd_pub[$dt]:'0')."-</button>";
								$i++;
							}
							echo "<br/>";
							echo "<span><a href='publicacao.php?author=&tab=pub_update&lista=$pagA'><<< </a></span>";
							echo "<span style='margin-left:60%'><a href='publicacao.php?author=&tab=pub_update&lista=$pagP'>>>> </a></span>";
							echo "<div id='mydiv1'></div>";
							?>		
						</td>
					</tr>
					<!--tr height='200px'>
						<td valign='top' align='right' width='50%'>Data: <input type='text' id='int_pub' value=''><br><br><br><div id='mydiv1'></div></td>
						<td valign='top' align='left' width='50%'>
						<button type='button' id='carregar_pub' class='simple_form_btn' style='height:23px' onclick='carregar_pub()'><?php //echo _T('title_upgrade_publications'); ?></button>
						</td>
					</tr-->
				</table>
			<?php 
			break;
			
		}
		
echo "</fieldset>\n";
//FT Criando a exportação da agenda==========
?>
<!--script type="text/javascript" src="js/highslide-with-html.js"></script>
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
</p><br/><br/-->
<?php

//FT=========================================
lcm_page_end();

?>
