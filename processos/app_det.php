<?php

include('inc/inc.php');

$admin 	 = ($GLOBALS['author_session']['status']=='admin');
$manager = ($GLOBALS['author_session']['status']=='manager');

$app = intval(_request('app'));

$ac = get_ac_app($app);

if (! $ac['r'])
	die("Acesso negado");
	
// Get the authors participating in the appointment
$q = "SELECT p.*, a.name_first, a.name_middle, a.name_last, c.p_adverso AS case_title, p.date_creation
	FROM lcm_app as p
	LEFT JOIN lcm_case as c ON (c.id_case = p.id_case)
	LEFT JOIN lcm_author as a ON (a.id_author = p.id_author)
	WHERE p.id_app = $app";

$result = lcm_query($q);

if (! ($row = lcm_fetch_array($result)))
	die("There is no such appointment.");

lcm_page_start(_T('title_app_view') . ' ' . $row['title'], '', '', 'tools_agenda');

echo '<fieldset class="info_box">' . "\n";
echo '<p class="normal_text">' . "\n";
	
	echo _Ti('app_input_type') . _Tkw('appointments', $row['type']) . "<br />\n";
	echo _Ti('app_input_title') . $row['title'] . "<br />\n";

	if ($row['hidden'] == 'Y') {
		echo '<p class="normal_text"><strong>' . _T('app_info_is_deleted') . "</strong>";

		if ($ac['a'])
			echo " " . _T('app_info_is_deleted2');

		echo "</p>\n";
	}

	show_page_subtitle(_T('generic_subtitle_general'), 'tools_agenda');

	echo _Ti('app_input_description') . nl2br($row['description']) . "<br />\n";

	echo "<br />\n";
	echo _Ti('time_input_date_start') . format_date($row['start_time'], 'short');
	$year_for_cal = "&annee=" . annee($row['start_time'])  // year
		. "&mois=" . mois($row['start_time'])  // month
		. "&jour=" . journum($row['start_time']); // day

	echo ' ' . http_href_img("calendar.php?type=jour" . $year_for_cal, 'cal-today.gif', '', _T('app_info_see_cal_for_day_tooltip'));
	echo "<br />\n";


	/* [ML] removing: not really useful for now 
	$end_time = vider_date($row['end_time']);
	$reminder = vider_date($row['reminder']);
	if ($prefs['time_intervals'] == 'absolute') {
		echo _Ti('time_input_date_end') . format_date($row['end_time'], 'short') . "<br />\n";
		echo _Ti('app_input_reminder') . format_date($row['reminder'], 'short') . "<br />\n";
	} else {
		$duration = ($end_time ? strtotime($row['end_time']) - strtotime($row['start_time']) : 0);
		echo _Ti('app_input_time_length') . format_time_interval($duration,($prefs['time_intervals_notation'] == 'hours_only')) . "<br />\n";
		$reminder_offset = ($reminder ? strtotime($row['start_time']) - strtotime($row['reminder']) : 0);
		echo _Ti('app_input_reminder')
			. format_time_interval($reminder_offset,($prefs['time_intervals_notation'] == 'hours_only'))
			. " " . _T('time_info_before_start') . "<br />\n";
	}
	*/

	echo "<br />\n";

	echo _Ti('app_input_created_by') . get_person_name($row) . ". Em: " . format_date($row['date_creation'], 'short') . "<br />\n";

	if ($row['case_title'])
		echo _Ti('app_input_related_to_case') 
			. '<a href="case_det.php?case=' .  $row['id_case'] . '" class="content_link">' . $row['case_title']
			. "</a><br />\n";

	//
	// Show appointment participants
	//
	$q = "SELECT ap.*, a.name_first, a.name_middle, a.name_last
		FROM lcm_author_app as ap, lcm_author as a
		WHERE (ap.id_app=" . $app . "
			AND ap.id_author = a.id_author)";

	$res_author = lcm_query($q);

	if (lcm_num_rows($res_author)>0) {
		echo "Participantes: "; // TRAD
		$participants = array();

		while ($author = lcm_fetch_array($res_author)) {
			$participants[] = get_person_name($author);
		}

		echo join(', ',$participants);
		echo "<br />\n";
	}
	
	// Show appointment adversos
	$q = "SELECT aco.*, c.name_first, c.name_middle, c.name_last, o.name
		FROM lcm_app_adverso_cliente as aco
		LEFT JOIN lcm_cliente as o ON (aco.id_cliente = o.id_cliente)
		LEFT JOIN lcm_adverso as c ON (aco.id_adverso = c.id_adverso)
		WHERE id_app = " . $row['id_app'];

	$res_adverso = lcm_query($q);

	if (lcm_num_rows($res_adverso)>0) {
		echo _Ti('app_input_adversos');
		$adversos = array();
		while ($adverso = lcm_fetch_array($res_adverso))
			$adversos[] = get_person_name($adverso)
				. ( ($adverso['id_cliente'] > 0) ? " of " . $adverso['name'] : ''); // TRAD
		echo join(', ',$adversos);
		echo "<br />\n";
	}
	
		// Show autores
	$q = "SELECT * FROM lcm_author";
	$res_autores = lcm_query($q);
	while ($author = lcm_fetch_array($res_autores)) {
		$list_author[$author['id_author']] = $author['name_first'] . " " . $author['name_last'];
	}
	
	//Ocultando o bot�o editar e cumprir caso tenha sido cumprido e exibindo a descri��o
	if ($row['performed'] == 'Y') {
		echo "<br />\n";
		echo "<div class='simple_btn'>Cumprido</div>";
		echo "<br />\n";
		echo "Em " . format_date($row['date_update'], 'short') . ($list_author[$row['upd_author']] ? ", por " . $list_author[$row['upd_author']] : ".");
		echo "<br />\n";
		echo "<br />\n";
		echo _Ti('app_input_perf_desc') . ' ' . $row['perf_desc'];
		echo "<br />\n";
	} else {
		// Show edit appointment button
		//FT criando o bot�o "cumprir"
		if ($ac['e']){
			//Ocultando o bot�o editar para os usu�rios normais
			echo '<br />';
			if($admin){
				echo '<a href="edit_app.php?app=' . $row['id_app'] . '" class="create_new_lnk">' . _T('app_button_edit') . '</a> ';
			}
			echo '<a href="edit_app.php?app=' . $row['id_app'] . '&performed=1" class="create_new_lnk">' . _T('app_button_cumprir') . "</a><br />\n";
		}
	}
	
	if ($row['id_case'] > 0) {
		// Show parent followup ([ML] fu.type necessary for short-desc)
		$q = "SELECT a.id_followup, fu.description, fu.type
				FROM lcm_app_fu as a, lcm_followup as fu
				WHERE a.id_app = " . $row['id_app'] . "
			  	AND a.id_followup = fu.id_followup
				AND a.relation = 'parent'";

		$res_fu = lcm_query($q);

		if (lcm_num_rows($res_fu) > 0) {
			// Show parent followup title
			$fu = lcm_fetch_array($res_fu);

			$short_description = get_fu_description($fu);
			echo '<br />Vinculado ao andamento:' . ' <a class="content_link" href="fu_det.php?followup=' . $fu['id_followup'] . '">' . $short_description . "</a><br />\n"; // TRAD
		}

		// Show child followup
		$q = "SELECT lcm_app_fu.id_followup,lcm_followup.description FROM lcm_app_fu,lcm_followup
			WHERE lcm_app_fu.id_app=" . $row['id_app'] . "
				AND lcm_app_fu.id_followup=lcm_followup.id_followup
				AND lcm_app_fu.relation='child'";

		$res_fu = lcm_query($q);

		if (lcm_num_rows($res_fu) > 0) {
			// Show child followup title
			$fu = lcm_fetch_array($res_fu);
			$title_length = (($prefs['screen'] == "wide") ? 48 : 115);
			if (strlen(lcm_utf8_decode($fu['description'])) < $title_length)
				$short_description = $fu['description'];
			else
				$short_description = substr($fu['description'],0,$title_length) . '...';
			echo '<br />Consequete do andamento:' . ' <a href="fu_det.php?followup=' . $fu['id_followup'] . '">' . $short_description; // TRAD
		} else {
			if ($ac['w']) {
				// Show create followup from appointment
				echo '<br /><a href="edit_fu.php?case=' . $row['id_case'] . '&amp;app=' . $row['id_app']
					. '" class="create_new_lnk">Criar novos andamento';	// TRAD
			}
		}

		echo "</a><br />\n";

		// Show link back to the case details
		echo '<br /><a href="case_det.php?case=' . $row['id_case'] . '&amp;tab=appointments" class="back_lnk">' . 'Acompanhamentos' . "</a><br />\n"; // TRAD
	}

	echo "<br /></p>";
	echo "</fieldset>\n";

	lcm_page_end();

	$_SESSION['form_data'] = array();
	$_SESSION['errors'] = array();

?>
