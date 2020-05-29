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
//FT criação e inclusão do arquivo de filtro e exclusão do script padrão de filtro

include 'filtros.php';

//
// Mostrar a lista de casos
//

?>
<p class="normal_text">
	<?php
	$case_list = new LcmCaseListUI();
	$case_list->setSearchTerm($find_case_string);
	$case_list->start();
	$case_list->printList();
	$case_list->finish();
	?>
</p>
<!--//FT Crindo o iframe para envio do relatório -->
<script type="text/javascript" src="js/highslide-with-html.js"></script>
<link rel="stylesheet" type="text/css" href="styles/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = 'images/graphics/';
	hs.outlineType = 'rounded-white';
	hs.wrapperClassName = 'draggable-header';
</script>
<p>
	<a href="edit_case.php?case=0" class="create_new_lnk"><?php echo _T('case_button_new'); ?></a>
	<a href="edit_adverso.php" class="create_new_lnk"><?php echo _T('adverso_button_new'); ?></a>
	<a href="#" class="create_new_lnk" onclick="fc_cad_dados();" style="float: right; margin-top: -5px; "><?php echo _T('adverso_button_export'); ?></a>
	<a href="form.php?exp=proc" onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="create_new_lnk" style="float: right; margin-top: -5px; "><?php echo _T('adverso_button_sendmail'); ?></a>
</p>
<?php
//
// Lista dos últimos acompanhamentos
//
?>
<a name="fu"></a>
<?php echo show_page_subtitle(_T('case_subtitle_recent_followups')); ?>
<p class="normal_text"><?php echo show_listfu_start('general'); ?>
	<?php
	//FT criando novos filtros
	$pasta_int = (int) $_GET['pasta_f'];
	$_GET['pasta_f']    != "" ? $pasta_fil  = "AND c.id_case   	   =  ".$pasta_int."		   " : "";
	$_GET['state_f']    != "" ? $state_f 	= "AND c.state 		   = '".$_GET['state_f']."'    " : "";
	$_GET['type_f']     != "" ? $type_f 	= "AND fu.type 		   = '".$_GET['type_f']."'     " : "";
	$_GET['cliente_f']  != "" ? $cliente_f 	= "AND cco.id_cliente  = '".$_GET['cliente_f']."'  " : "";
	$_GET['condicao_f'] != "" ? $condicao_f = "AND kc.id_keyword   =  ".$_GET['condicao_f']."  " : "";
	$_GET['diversos_f'] != "" ? $diversos_f = "AND kc.id_keyword    =  ".$_GET['diversos_f']."  " : "";
	$_GET['processo_f'] != "" ? $processo_f = "AND c.processo  like '%".$_GET['processo_f']."%'" : "";
	$_GET['parte_f']    != "" ? $parte_f 	= "AND c.p_adverso like '%".$_GET['parte_f']."%'   " : "";
	$_GET['comar_f']    != "" ? $comar_f 	= "AND c.comarca   like '%".$_GET['comar_f']."%'   " : "";
	$_GET['status_f']   != "" ? $status_f   = "AND c.status   	in   ('".$_GET['status_f']."') " : $status_f = "AND c.status in ('open','closed','suspended') ";
	$_GET['stopday_f']  != "" ? $stopday_f 	= "AND DATEDIFF(curdate(), fu.date_start) >= '".$_GET['stopday_f']."' " : "";
	$_GET['vara_f'] 	!= "" ? $vara_f 	= "AND c.vara 	   		= '".str_replace('_|_', ' ', $_GET['vara_f'])."' " : "";
	$_GET['acao_f'] 	!= "" ? $acao_f 	= "AND c.legal_reason	= '".str_replace('_|_', ' ', $_GET['acao_f'])."' " : "";
	if($_GET['last_f'] == "on")
	{ 
		$last_f = "AND fu.date_start in (SELECT max(ff.date_start) FROM lcm_followup AS ff where ff.id_case = fu.id_case) ";
		//$type_f = "AND fl.type_followup = '".$_GET['type_f']."' ";
	}
	$q  = "	SELECT";
	$q .= " fu.id_case,";
	$q .= " fu.id_followup,"; 
	$q .= " fu.date_start,"; 
	$q .= " fu.date_end,";
	$q .= " fu.type,"; 
	$q .= " fu.description,"; 
	$q .= " fu.case_stage,";
	$q .= " fu.hidden,";
	$q .= " a.name_first,"; 
	$q .= " a.name_middle,"; 
	$q .= " a.name_last,"; 
	$q .= " c.processo,"; 
	$q .= " c.vara,";
	$q .= " c.p_adverso,"; 
	$q .= " c.status,"; 
	$q .= " c.comarca,"; 
	$q .= " c.vara,";
	$q .= " DATEDIFF(curdate(), fu.date_start) as stopday,"; 
	$q .= " state";
	$q .= " FROM lcm_followup as fu, lcm_author as a, lcm_case as c";
	$q .= " LEFT JOIN lcm_case_adverso_cliente AS cco ON cco.id_case = c.id_case";

	//if($_GET['last_f']	 == "on" || $_GET['stopday_f'] != "")
		//$q .= " JOIN lcm_followup_last AS fl ON c.id_case = fl.id_case";
	
	//if($_GET['condicao_f'] != "")
	if ($_GET['condicao_f']!= "" || $_GET['diversos_f']!= "")
		$q .="	LEFT JOIN lcm_keyword_case AS kc on kc.id_case = c.id_case ";
	
	$q .="	WHERE fu.id_author = a.id_author 
			$pasta_fil $processo_f $status_f $parte_f $comar_f $type_f $stopday_f $state_f $cliente_f $vara_f $acao_f $condicao_f $last_f $diversos_f
			AND  c.id_case = fu.id_case ";
	// Autor do acompanhamento

		// START - Get lista de casos em que autor é atribuído
		$q_temp = "	SELECT c.id_case
					FROM lcm_case_author as ca, lcm_case as c
					WHERE ca.id_case = c.id_case
					AND ca.id_author = " . $author_session['id_author'];
		//FT Criando o "all" para todos os anos
		if($_GET['case_period']!="all"){
			if($prefs['case_period'] < 1900) // since X days
				// $q_temp .= " AND TO_DAYS(NOW()) - TO_DAYS(c.date_creation) < " . $prefs['case_period'];
				$q_temp .= " AND " . lcm_query_subst_time('c.date_creation', 'NOW()') . ' < ' . $prefs['case_period'] * 3600 * 24;
			
			else // for year X
				// $q_temp .= " AND YEAR(date_creation) = " . $prefs['case_period'];
				$q_temp .= " AND " . lcm_query_trunc_field('c.date_creation', 'year') . ' = ' . $prefs['case_period'];
		}
		
		
		$r_temp = lcm_query($q_temp);
		$list_cases = array();

		while ($row = lcm_fetch_array($r_temp))
			$list_cases[] = $row['id_case'];
		// END - Get list of cases on which author is assigned

			if (! ($prefs['case_owner'] == 'all' && $author_session['status'] == 'admin')) 
			{
				$q .= " AND ( ";

				if ($prefs['case_owner'] == 'public')
					$q .= " c.public = 1 OR ";

				// [ML] XXX FIXME TEMPORARY PATCH
				// if user and no cases + no follow-ups...
				if (count($list_cases))
					$q .= " fu.id_case IN (" . implode(",", $list_cases) . "))";
				else
					$q .= " fu.id_case IN ( 0 ))";
				
			}

	// Period (date_creation) to show
	//FT Criando o "all" para todos os anos
	if($_GET['case_period']!="all") {
		if ($prefs['case_period'] < 1900) // since X days
			// $q .= " AND TO_DAYS(NOW()) - TO_DAYS(date_start) < " . $prefs['case_period'];
			$q .= " AND " . lcm_query_subst_time('date_start', 'NOW()') . ' < ' . $prefs['case_period'] * 3600 * 24;
		else // for year X
			// $q .= " AND YEAR(date_start) = " . $prefs['case_period'];
			$q .= " AND " . lcm_query_trunc_field('date_start', 'year') . ' = ' . $prefs['case_period'];
	}
	// Add ordering
	$fu_order = "DESC";
	if (isset($_REQUEST['fu_order']))
		if ($_REQUEST['fu_order'] == 'ASC' || $_REQUEST['fu_order'] == 'DESC')
			$fu_order = $_REQUEST['fu_order'];
	
	$q .= " GROUP BY id_followup ";
	$q .= " ORDER BY date_start $fu_order, id_followup $fu_order";
	
	$result = lcm_query($q);
	
	// Check for correct start position of the list
	$number_of_rows = lcm_num_rows($result);
	$fu_list_pos = 0;
					
	if (isset($_REQUEST['fu_list_pos']))
		$fu_list_pos = $_REQUEST['fu_list_pos'];
					
	if ($fu_list_pos >= $number_of_rows)
		$fu_list_pos = 0;
					
	// Position to the page info start
	if ($fu_list_pos > 0)
		if (!lcm_data_seek($result,$fu_list_pos))
			lcm_panic("Error seeking position $fu_list_pos in the result");
				
	// Process the output of the query
	for ($i = 0 ; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))); $i++)
		show_listfu_item($row, $i, 'general');

	show_list_end($fu_list_pos, $number_of_rows, false, 'fu');
	?>
</p>
<?php
#WL - Integração Kurier 30/09/2019 ------------------------------------------------------------------------

	$sel = mysql_query("select processo from lcm_case where id_case = {$_GET['pasta_f']} limit 1");
	
	if($rs = mysql_fetch_row($sel))
	{
		$processo_k = trim($rs[0]);
	}
	else
	{
		$processo_k = $_GET['processo_f'];
	}
	
	$query_k = "select * from lcm_followups_kurier where processo = '{$processo_k}' order by data_and desc";
	
	$sel_k = mysql_query($query_k);
	
	if(mysql_num_rows($sel_k) > 0)
	{
		echo show_page_subtitle(_T('Andamentos Kurier')); 
		?>
		<p class="normal_text">	
			<div style="height: 400px; overflow: auto">
				<table class="tbl_usr_dtl" width="99%" border="0" align="center">
					<tr>
						<th class="heading">Id</th>
						<th class="heading">Processo</th>
						<th class="heading">Descri&ccedil;&atilde;o</th>
						<th class="heading">Data And.</th>
						<th class="heading">Data Disp. And.</th>
						<th class="heading">Data Cad. Proc.</th>
					</tr>
			<?php
				
				$tot_and_k = mysql_num_rows($sel_k);
				
				while($rs_k = mysql_fetch_assoc($sel_k))
				{
					$data_and	   = substr($rs_k['data_and'], 8, 2) .'/'. substr($rs_k['data_and'], 5, 2) .'/'. substr($rs_k['data_and'], 0, 4);
					$data_disp_and = substr($rs_k['data_disp_and'], 8, 2) .'/'. substr($rs_k['data_disp_and'], 5, 2) .'/'. substr($rs_k['data_disp_and'], 0, 4);
					$data_cad_proc = substr($rs_k['data_cad_proc'], 8, 2) .'/'. substr($rs_k['data_cad_proc'], 5, 2) .'/'. substr($rs_k['data_cad_proc'], 0, 4);
					
					?>
						<tr>
							<td><?php echo $rs_k['id_and'];?></td>
							<td><?php echo $rs_k['num_proc_cli'];?></td>
							<td><?php echo $rs_k['descricao'];?></td>
							<td><?php echo $data_and;?></td>
							<td><?php echo $data_disp_and;?></td>
							<td><?php echo $data_cad_proc;?></td>
						</tr>
					<?php
				}
			?>
				</table>
			</div>
			<br>
			<p style="font-family: arial; font-size: 11px;">&nbsp;&nbsp;&nbsp;<?php echo "Total: " . $tot_and_k;?></p>
		</p>
	<?php
	}

lcm_page_end();
?>