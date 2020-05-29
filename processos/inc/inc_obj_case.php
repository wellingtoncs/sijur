<?php

// Execute this file only once
if (defined('_INC_OBJ_CASE')) return;
define('_INC_OBJ_CASE', '1');

include_lcm('inc_obj_generic');
include_lcm('inc_db');
include_lcm('inc_contacts');

class LcmCase extends LcmObject {
	// Note: Since PHP5 we should use "private", and generates a warning,
	// but we must support PHP >= 4.0.
	var $followups;
	var $fu_start_from;

	function LcmCase($id_case = 0) {
		$id_case = intval($id_case);
		$this->fu_start_from = 0;

		$this->LcmObject();

		if ($id_case > 0) {
			$query = "SELECT * FROM lcm_case WHERE id_case = $id_case";
			$result = lcm_query($query);

			if (($row = lcm_fetch_array($result))) 
				foreach ($row as $key => $val) 
					$this->data[$key] = $val;

			// Case stage
			$stage = get_kw_from_name('stage', $this->getDataString('stage'));
			$this->data['id_stage'] = $stage['id_keyword'];
		}

		// If any, populate form values submitted
		foreach($_REQUEST as $key => $value) {
			$nkey = $key;

			if (substr($key, 0, 5) == 'case_')
				$nkey = substr($key, 5);

			$this->data[$nkey] = clean_input(_request($key));
		}

		// If any, populate with session variables (for error reporting)
		if (isset($_SESSION['form_data'])) {
			foreach($_SESSION['form_data'] as $key => $value) {
				$nkey = $key;

				if (substr($key, 0, 5) == 'case_')
					$nkey = substr($key, 5);
				$this->data[$nkey] = clean_input(_session($key));
			}
		}

		if ((! $id_case) || get_datetime_from_array($_SESSION['form_data'], 'assignment', 'start', -1) != -1)
			$this->data['date_assignment'] = get_datetime_from_array($_SESSION['form_data'], 'assignment', 'start', date('Y-m-d H:i:s'));
	}

	/* private */
	function loadFollowups($list_pos = 0) {
		global $prefs;

		$q = "SELECT fu.id_followup, fu.date_start, fu.date_end, fu.type, fu.description, fu.case_stage, 
				fu.date_cad, fu.system_name, fu.robo_ins, fu.robo_cad,
				date_format(fu.date_start,'%d/%m/%Y') as date_start2, date_format(fu.date_cad,'%d/%m/%Y') as date_cad2,
				fu.hidden, a.name_first, a.name_middle, a.name_last
				FROM lcm_followup as fu, lcm_author as a
				WHERE id_case = " . $this->getDataInt('id_case', '__ASSERT__') . "
				AND fu.id_author = a.id_author";

		// Date filters (from interface)
		if (($date_start = get_datetime_from_array($this->data, 'date_start', 'start', -1)) != -1)
			$q .= " AND fu.date_start >= '$date_start' ";

		if (($date_end = get_datetime_from_array($this->data, 'date_end', 'start', -1)) != -1)
			$q .= " AND fu.date_end <= '$date_end' ";

		// Sort follow-ups by creation date
		$fu_order = 'DESC';
		if (_request('fu_order') == 'ASC' || _request('fu_order') == 'DESC')
			$fu_order = _request('fu_order');
		
		$q .= " ORDER BY fu.date_start " . $fu_order;

		$result = lcm_query($q);
		$number_of_rows = lcm_num_rows($result);
			
		if ($list_pos >= $number_of_rows)
			return;
				
		// Position to the page info start
		if ($list_pos > 0)
			if (!lcm_data_seek($result,$list_pos))
				lcm_panic("Error seeking position $list_pos in the result");

		if (lcm_num_rows($result)) {
			for ($cpt = 0; (($cpt < $prefs['page_rows'] || _request('list_pos') == 'all') && ($row = lcm_fetch_array($result))); $cpt++)
				array_push($this->followups, $row);
		}
	}

	function getFollowupStart() {
		global $prefs;

		$this->fu_start_from = _request('list_pos', 0);

		// just in case
		if (! ($this->fu_start_from >= 0)) $this->fu_start_from = 0;
		if (! $prefs['page_rows']) $prefs['page_rows'] = 10; 

		$this->followups = array();
		$this->loadFollowups($this->fu_start_from);
	}

	function getFollowupDone() {
		return ! (bool) (count($this->followups));
	}

	function getFollowupIterator() {
		global $prefs;

		if ($this->getFollowupDone())
			lcm_panic("LcmClient::getFollowupIterator called but getFollowupDone() returned true");

		return array_shift($this->followups);
	}

	function getFollowupTotal() {
		static $cpt_total_cache = null;

		if (is_null($cpt_total_cache)) {
			$query = "SELECT count(*) as cpt
						FROM lcm_followup as fu, lcm_author as a
						WHERE id_case = " . $this->data['id_case'] . "
						  AND fu.id_author = a.id_author ";

			$result = lcm_query($query);

			if (($row = lcm_fetch_array($result)))
				$cpt_total_cache = $row['cpt'];
			else
				$cpt_total_cache = 0;
		}

		return $cpt_total_cache;
	}

	function addClient($id_adverso) {
		// TODO: Permissions?
		// TODO: Check if adverso already on case?

		$query = "INSERT INTO lcm_case_adverso_cliente
					SET id_case = " . $this->data['id_case'] . ",
						id_adverso = $id_adverso";

		lcm_query($query);
	}

	function removeClient($id_adverso) {
		$query = "DELETE FROM lcm_case_adverso_cliente
					WHERE id_case = " . $this->data['id_case'] . "
					  AND id_adverso = $id_adverso";

		lcm_query($query);
	}

	function validate() {
		$errors = array();
		
		// * Competência
		 if ($this->getDataString('jus')!=4){
			if ($this->getDataString('jus')!=0 || $this->getDataString('select_vara_1')!=0 || $this->getDataString('select_vara_3')!=0){

				if (! $this->getDataString('jus')) 
						$errors['jus'] = "Competência: " . _T('warning_field_mandatory');
				if (! $this->getDataString('select_vara_1')) 
						$errors['select_vara_1'] = "Esfera: " . _T('warning_field_mandatory');
				if (! $this->getDataString('select_vara_3')) 
						$errors['select_vara_3'] = "Complemente: " . _T('warning_field_mandatory');
			}
		} else{
			if (! $this->getDataString('select_vara_1')) 
					$errors['select_vara_1'] = "Esfera: " . _T('warning_field_mandatory');
		}
		//if($this->getDataString('select_vara_2')!=""){
		//	if (! $this->getDataString('select_vara_2')) 
		//			$errors['select_vara_2'] = "Vara: " . _T('warning_field_mandatory');
		//}
			
				
		// * Title must be non-empty
		if (! $this->getDataString('p_adverso')) 
			$errors['p_adverso'] = _Ti('case_input_p_adverso') . _T('warning_field_mandatory');

		// * Date assignment must be a valid date
		if (! checkdate_sql($this->getDataString('date_assignment')))
			$errors['date_assignment'] = _Ti('case_input_date_assigned') . 'Invalid date.'; // TRAD

		// * Depending on site policy, legal reason may be mandatory
		if (read_meta('case_legal_reason') == 'yes_mandatory' && (!$this->getDataString('legal_reason')))
			$errors['legal_reason'] = _Ti('case_input_legal_reason') . _T('warning_field_mandatory');
		
		// * Depending on site policy, legal reason may be mandatory
		if (read_meta('case_p_cliente') == 'yes_mandatory' && (!$this->getDataString('p_cliente')))
			$errors['p_cliente'] = _Ti('case_input_p_cliente') . _T('warning_field_mandatory');

		// * Depending on site policy, alleged crime may be mandatory
		if (read_meta('case_comarca') == 'yes_mandatory' && (!$this->getDataString('comarca')))
			$errors['comarca'] = _Ti('case_input_comarca') . _T('warning_field_mandatory');

		// * TODO: Status must be a valid option (where do we have official list?)
		if (! $this->getDataString('status'))
			$errors['status'] = _Ti('case_input_status') . _T('warning_field_mandatory');

		// * TODO: Stage must be a valid keyword
		if (! $this->getDataString('stage'))
			$errors['stage'] = _Ti('case_input_stage') . _T('warning_field_mandatory');

		validate_update_keywords_request('case', $this->getDataInt('id_case'));
		validate_update_keywords_request('stage', $this->getDataInt('id_case'), $this->getDataInt('id_stage'));

		if ($_SESSION['errors'])
			$errors = array_merge($errors, $_SESSION['errors']);

		//
		// Custom validation functions
		//
		$id_case = $this->getDataInt('id_case');
		$fields = array('p_adverso' => 'CaseTitle',
					'legal_reason' => 'CaseLegalReason',
					'p_cliente' => 'CasePCliente',
					'comarca' => 'CaseAllegedCrime',
					'status' => 'CaseStatus',
					'stage' => 'CaseStage');

		foreach ($fields as $f => $func) {
			if (include_validator_exists($f)) {
				include_validator($f);
				$class = "LcmCustomValidate$func";
				$data = $this->getDataString($f);
				$v = new $class();

				if ($err = $v->validate($id_case, $data)) 
					$errors[$f] = _Ti('case_input_' . $f) . $err;
			}
		}

		return $errors;
	}

	function save() {
		global $author_session;

		$errors = $this->validate();

		if (count($errors))
			return $errors;

		//
		// Create the case in the database
		//

		/* [ML] Note: the 'case_notes' field is refered to as only 'notes'
		 * since the constructor of the class strips 'case_' prefixes
		 */
		//FT novo campo "causa" e "processo"
		$fl = "	processo='"          . $this->getDataString('processo')         . "',
				p_adverso='"         . $this->getDataString('p_adverso')        . "',
				date_assignment = '" . $this->getDataString('date_assignment')  . "',
				legal_reason='"      . $this->getDataString('legal_reason')     . "',
				p_cliente='"      	 . $this->getDataString('p_cliente')    	. "',
				comarca='"    		 . $this->getDataString('comarca')   		. "',
				state='"    		 . $this->getDataString('state')  			. "',
				vara='"    			 . $this->getDataString('vara')  			. "',
				notes = '"           . $this->getDataString('notes')            . "',
			    causa='"             . $this->getDataString('causa')            . "',
				vlr_fipe='"          . $this->getDataString('vlr_fipe')         . "',
				dep_jud='"           . $this->getDataString('dep_jud')          . "',
				pesq_bem='"          . $this->getDataString('pesq_bem')         . "',
				cultura='"           . $this->getDataString('cultura')          . "',
				qtd_mand_neg='"      . $this->getDataString('qtd_mand_neg')     . "',
				dt_contrato='"       . $this->getDataString('dt_contrato')      . "',
				vlr_tot_fin='"       . $this->getDataString('vlr_tot_fin')      . "',
				tot_parc='"      	 . $this->getDataString('tot_parc')     	. "',
				vlr_princ='"      	 . $this->getDataString('vlr_princ')        . "',
				dt_pri_parc='"       . $this->getDataString('dt_pri_parc')      . "',
				dt_ult_parc='"       . $this->getDataString('dt_ult_parc')     	. "',
				pri_parc_atra='"     . $this->getDataString('pri_parc_atra')    . "',
				dt_pri_parc_atra='"  . $this->getDataString('dt_pri_parc_atra') . "',
				dt_atz='"      		 . $this->getDataString('dt_atz')     		. "',
				status='"            . $this->getDataString('status')           . "',
				penhora='"           . $this->getDataString('penhora')          . "',
				seguimento='"		 . $this->getDataString('seguimento')       . "',
				fase='"      		 . $this->getDataString('fase')     		. "',
				alerta='"            . $this->getDataString('alerta')           . "',
			    stage='"             . $this->getDataString('stage')            . "'";

		// Put public access rights settings in a separate string
		$public_access_rights = '';

		/* 
		 * [ML] Important note: the meta 'case_*_always' defines whether the user
		 * has the choice of whether read/write should be allowed or not. If not,
		 * we take the system default value in 'case_default_*'.
		 */

		if ((read_meta('case_read_always') == 'yes') && $author_session['status'] != 'admin') {
			// impose system setting
			$public_access_rights .= "public=" . (int)(read_meta('case_default_read') == 'yes');
		} else {
			// write user selection
			$public_access_rights .= "public=" . (int)($this->getDataString('public') == 'yes');
		}

		if ((read_meta('case_write_always') == 'yes') && $author_session['status'] != 'admin') {
			// impose system setting
			$public_access_rights .= ", pub_write=" . (int)(read_meta('case_default_write') == 'yes');
		} else {
			// write user selection
			$public_access_rights .= ", pub_write=" . (int)($this->getDataString('pub_write') == 'yes');
		}

		if ($this->getDataInt('id_case') > 0) {
			// This is modification of existing case
			$id_case = $this->getDataInt('id_case');

			// Check access rights
			if (!allowed($id_case,'e'))
				lcm_panic("Você não tem permissão para alterar as informações deste processo!");

			// If admin access is allowed, set all fields
			if (allowed($id_case,'a'))
				$q = "UPDATE lcm_case SET $fl,$public_access_rights WHERE id_case=$id_case";
			else
				$q = "UPDATE lcm_case SET $fl WHERE id_case=$id_case";

			lcm_query($q);

			// Update lcm_stage entry for case creation (of first stage!)
			// [ML] This doesn't make so much sense, but better than nothing imho..
			$q = "SELECT min(id_entry) as id_entry FROM lcm_stage WHERE id_case = $id_case";
			$tmp_result = lcm_query($q);

			if (($tmp_row = lcm_fetch_array($tmp_result))) {
				$q = "UPDATE lcm_stage
					SET date_creation = '" . $this->getDataString('date_assignment') . "'
					WHERE id_entry = " . $tmp_row['id_entry'];

				lcm_query($q);
			}
		} else {
			// This is new case
			$q = "INSERT INTO lcm_case SET id_stage = 0, date_creation = NOW(), date_update = NOW(), $fl,$public_access_rights";
			$result = lcm_query($q);
			$id_case = lcm_insert_id('lcm_case', 'id_case');
			$id_author = $author_session['id_author'];

			$this->data['id_case'] = $id_case;

			// Insert new case_author relation
			// [AG] The user creating case should always have 'admin' access right, otherwise only admin could add new user(s) to the case
			$q = "INSERT INTO lcm_case_author SET
				id_case = $id_case,
				id_author = $id_author,
				ac_read=1,
				ac_write=1,
				ac_edit=" . (int)(read_meta('case_allow_modif') == 'yes') . ",
				ac_admin=1";

			$result = lcm_query($q);

			// Get author information
			$q = "SELECT *
				FROM lcm_author
				WHERE id_author=$id_author";

			$result = lcm_query($q);
			$author_data = lcm_fetch_array($result);

			// Add 'assignment' followup to the case
			$q = "INSERT INTO lcm_followup
				SET id_case = $id_case, 
					id_stage = 0,
					id_author = $id_author,
					type = 'assignment',
					case_stage = '" . $this->getDataString('stage') . "',
					date_start = NOW(),
					date_end = NOW(),
					sumbilled = 0,
					description='" . $id_author . "'";

			lcm_query($q);
			$id_followup = lcm_insert_id('lcm_followup', 'id_followup');

			//Inserindo o andamento na nova tabela lcm_followup_last
			lcm_query("INSERT INTO lcm_followup_last SET id_case=$id_case,type_followup='assignment', desc_followup='" . $id_author . "', date_creat=NOW() ");
			
			// Add lcm_stage entry
			$q = "INSERT INTO lcm_stage SET
				id_case = $id_case,
						kw_case_stage = '" . $this->getDataString('stage') . "',
						date_creation = '" . $this->getDataString('date_assignment') . "',
						id_fu_creation = $id_followup";

			lcm_query($q);
			$id_stage = lcm_insert_id('lcm_stage', 'id_entry');

			// Update the id_stage entry for lcm_case
			lcm_query("UPDATE lcm_case SET id_stage = $id_stage WHERE id_case = $id_case");
			lcm_query("UPDATE lcm_followup SET id_stage = $id_stage WHERE id_followup = $id_followup");
		}

		// Keywords
		update_keywords_request('case', $this->getDataInt('id_case'));

		$stage = get_kw_from_name('stage', $this->getDataString('stage'));
		$id_stage = $stage['id_keyword'];
		update_keywords_request('stage', $id_case, $id_stage);


		return $errors;
	}
}

class LcmCaseInfoUI extends LcmCase {
	function LcmCaseInfoUI($id_case = 0) {
		$this->LcmCase($id_case);
	}

	function printGeneral($show_subtitle = true, $allow_edit = true) {
		// Read site configuration preferences
		$case_assignment_date = read_meta('case_assignment_date');
		$case_comarca  		  = read_meta('case_comarca');
		$case_legal_reason    = read_meta('case_legal_reason');
		$case_p_cliente    	  = read_meta('case_p_cliente');

		if ($show_subtitle)
			show_page_subtitle(_T('generic_subtitle_general'), 'cases_intro');

		$add   = allowed($this->data['case'], 'w');
		$edit  = allowed($this->data['case'], 'e');
		$admin = allowed($this->data['case'], 'a');

		//
		// Show various stages info
		//
		$q = "SELECT * FROM lcm_stage WHERE id_case = '" . $this->data['case'] . "' ORDER BY date_creation DESC";
		$result = lcm_query($q);

		$query_subs = "select count(*) from lcm_followup where type = 'followups132' and id_case = " . clean_output($this->getDataString('id_case'));

		$result_subs = lcm_query($query_subs);
		$row_subs = lcm_fetch_row($result_subs);
		$flag_subs = $row_subs[0] > 0 ? "Sim" : "Não";

		echo '<div style="float: right; width: 220px;">';
		show_page_subtitle(_T('case_subtitle_stage'), 'cases_intro');
		
		echo '<ul>';
		
		if($this->data[status] == "closed")
		{
			echo '<li class="large">'
				. '<span style="color: red; font-weight: bold">'
				. "Processo Encerrado"
				. '</span>'
				. "</li>\n";	
		}

		if($flag_subs == "Sim")
		{
			echo '<li class="large">'
				. '<span style="color: #0000ff; font-weight: bold">'
				. "Processo Substabelecido"
				. '</span>'
				. "</li>\n";	
		}
		
		while (($row = lcm_fetch_array($result))) {
			echo '<li>'
				. format_date($row['date_creation'], 'date_short') .  ': '
				. _Tkw('stage', $row['kw_case_stage'])
				. '</li>';
		}

		echo "</ul>\n";
		echo "</div>\n";

		//FT alteração abaixo, inclusão de colunas
		// Show case info
		//
		echo '<ul class="info">';

		// Case ID
		echo '<li>'
			. '<span class="label1">' . _Ti('case_input_id') . '</span>'
			. '<span class="value1">' . show_case_id($this->getDataInt('id_case')) . '</span>'
			. "</li>\n";

		// processo
		echo '<li>'
			. '<span class="label1">' . _Ti('case_input_processo') . '</span>'
			. '<span class="value1">' . $this->getDataString('processo') . '</span>'
			. "</li>\n";

		// Case title
		$qc = "SELECT DISTINCT id_keyword FROM lcm_keyword_case WHERE id_keyword IN (70,71) and id_case = " . $this->getDataInt('id_case') . " ";
		$rc = lcm_query($qc);
		$cl = lcm_fetch_array($rc);
		echo '<li>'
			. '<span class="label1">' . ($cl['id_keyword']==70 ? _Ti('case_input_autor') : _Ti('case_input_reu') ) . '</span>'
			. '<span class="value1">' . $this->getDataString('p_adverso') . '</span>'
			. "</li>\n";
		//FT Incluindo o nome do cliente com a condição se vinculado
		if(clean_output($this->getDataString('p_cliente'))!=""){
			if (substr($case_p_cliente, 0, 3) == 'yes')
				echo '<li>'
					. '<span class="label2">'
					. ($cl['id_keyword']==70 ? _Ti('case_input_reu') : _Ti('case_input_autor') )
					. '</span>'
					. '<span class="value2">'
					. clean_output($this->getDataString('p_cliente'))
					. '</span>'
					. "</li>\n";
		} else {
			$qc  = " SELECT DISTINCT c.id_cliente, c.name FROM lcm_cliente AS c ";
			$qc .= " LEFT JOIN lcm_case_adverso_cliente AS cac ON cac.id_cliente = c.id_cliente ";
			$qc .= " WHERE cac.id_case = " . clean_output($this->getDataString('id_case')) . " ";
			$rcli = lcm_query($qc);
			$row_cli = lcm_fetch_array($rcli);
			if (substr($case_p_cliente, 0, 3) == 'yes')
				echo '<li>'
					. '<span class="label2">'
					. ($cl['id_keyword']==70 ? _Ti('case_input_reu') : _Ti('case_input_autor')) 
					. '</span>'
					. '<span class="value2">'
					. $row_cli['name']
					. '</span>'
					. "</li>\n";
		}
		echo '<li>'
			. '<span class="label2">'
			. _Ti('case_input_date_creation')
			. '</span>'
			. '<span class="value2">'
			. format_date($this->getDataString('date_creation'))
			. '</span>'
			. "</li>\n";
		
		if ($case_assignment_date == 'yes') {
			// [ML] Case is assigned/unassigned when authors are added/remove
			// + case is auto-assigned when created.
			if ($this->data['date_assignment'])
				echo '<li>' 
					. '<span class="label2">'
					. _Ti('case_input_date_assigned')
					. '</span>'
					. '<span class="value2">'
					. format_date($this->getDataString('date_assignment'))
					. '</span>'
					. "</li>\n";
		}

		// Tempo total gasto no caso (redundante com "relatórios / tempos")
		
		//FT Ocultando o "tempo gasto..."
		$query = "SELECT " . lcm_query_sum_time('fu.date_start', 'fu.date_end') . " as time
					FROM lcm_followup as fu 
					WHERE fu.id_case = " . $this->getDataInt('id_case', '__ASSERT__') . "
					  AND fu.hidden = 'N'";
				
		$result = lcm_query($query);
		$row_tmp = lcm_fetch_array($result);

		echo '<li>'
			. '<span class="label2">'
			. _Ti('case_input_total_time') 
			. '</span>'
			. '<span class="value2">'
			. format_time_interval_prefs($row_tmp['time']) . '&nbsp;' . _T('time_info_short_hour')
			. '</span>'
			. "</li>\n";
		
		
		if (substr($case_legal_reason, 0, 3) == 'yes')
			echo '<li>'
				. '<span class="label2">'
				. _Ti('case_input_legal_reason') 
				. '</span>'
				. '<span class="value2">'
				. clean_output($this->getDataString('legal_reason'))
				. '</span>'
				. "</li>\n";


		if (substr($case_comarca, 0, 3) == 'yes')
			echo '<li>'
				. '<span class="label2">'
				. _Ti('case_input_comarca')
				. '</span>'
				. '<span class="value2">'
				. clean_output($this->getDataString('comarca'))
				. '</span>'
				. "</li>\n";
		// Estado
		echo '<li>'
			. '<span class="label1">' . _Ti('case_input_state') . '</span>'
			. '<span class="value1">' . $this->getDataString('state') . '</span>'
			. "</li>\n";
		// Vara
		echo '<li>'
			. '<span class="label1">' . _Ti('case_input_vara') . '</span>';
                echo '<span class="value1">' . $this->getDataString('vara') . '</span></li>';
	
	
		// Keywords
		show_all_keywords('case', $this->getDataInt('id_case'));

		if ($this->data['stage']) {
			// There should always be a stage, but in early versions, < 0.6.0,
			// it might have been missing, causing a lcm_panic().
			$stage = get_kw_from_name('stage', $this->getDataString('stage', '__ASSERT__'));
			$id_stage = $stage['id_keyword'];
			show_all_keywords('stage', $this->getDataInt('id_case'), $id_stage);
		}

		// Notes
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('case_input_notes')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('notes'))
			. '</span>'
			. "</li>\n";
			
		//FT Novo campo causa
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('case_input_causa')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('causa'))
			. '</span>'
			. "</li>\n";
			
		//WL Novo campo valor fipe
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Valor FIPE')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('vlr_fipe'))
			. '</span>'
			. "</li>\n";
		
			//WL Novo campo Depósito Judicial
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Dep&oacute;sito Judicial')
			. '</span>'
			. '<span class="value2" style="color: blue; font-weight: bold;">'			. nl2br($this->getDataString('dep_jud'))
			. '</span>'
			. "</li>\n";


		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Pesquisa Bem')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('pesq_bem'))
			. '</span>'
			. "</li>\n";
		
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Cultura')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('cultura'))
			. '</span>'
			. "</li>\n";
		
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Qtd. Mand. Neg.')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('qtd_mand_neg'))
			. '</span>'
			. "</li>\n";
			
		/*echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Data do Contrato')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('dt_contrato'))
			. '</span>'
			. "</li>\n";
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Valor Total Financiado')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('vlr_tot_fin'))
			. '</span>'
			. "</li>\n";
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Total de Parcelas')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('tot_parc'))
			. '</span>'
			. "</li>\n";
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Valor Principal')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('vlr_princ'))
			. '</span>'
			. "</li>\n";
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Data Primeira Parcela')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('dt_pri_parc'))
			. '</span>'
			. "</li>\n";
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Data &Uacute;ltima Parcela')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('dt_ult_parc'))
			. '</span>'
			. "</li>\n";
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Prim. Parc. Atraso')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('pri_parc_atra'))
			. '</span>'
			. "</li>\n";
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Dt. Prim. Parc. Atraso')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('dt_pri_parc_atra'))
			. '</span>'
			. "</li>\n";
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Data Atualiza&ccedil;&atilde;o')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('dt_atz'))
			. '</span>'
			. "</li>\n";*/
		
	//	echo "</ul>\n";
	//	echo "<p class='normal_text'>";

		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Penhora')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('penhora'))
			. '</span>'
			. "</li>\n";

		/*echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Seguimento')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('seguimento'))
			. '</span>'
			. "</li>\n";
			
			
		echo '<li class="large">'
			. '<span class="label2">'
			. _Ti('Fase')
			. '</span>'
			. '<span class="value2">'
			. nl2br($this->getDataString('fase'))
			. '</span>'
			. "</li>\n";	*/
	
		if ($allow_edit && $admin) {
			// Show case status (if closed, only site admin can re-open)
			echo '<li>';


			echo "<form action='edit_fu.php' method='get'>\n";
			echo "<input type='hidden' name='case' value='" . $this->getDataInt('id_case') . "' />\n";

			echo "<span class='label1'>" . _Ti('case_input_status') . "</span>";
			echo "<span class='value1'><select name='type' class='sel_frm' onchange='lcm_show(\"submit_status\")'>\n";

			// in inc/inc_acc.php
			$statuses = get_possible_case_statuses($this->getDataString('status'));

			foreach ($statuses as $s => $futype) {
				$sel = ($s == $this->getDataString('status') ? ' selected="selected"' : '');
				echo '<option value="' . $futype . '"' . $sel . '>' . _T('case_status_option_' . $s) . "</option>\n";
			}

			echo "</select></span>\n";
			echo "<button type='submit' name='submit' id='submit_status' value='set_status' style='visibility: hidden;' class='simple_form_btn'>" . _T('button_validate') . "</button>\n";
			echo "</form>\n";
			echo '</li>';
			echo '<li>';
			// Show case stage
			echo "<form action='edit_fu.php' method='get'>\n";
			echo "<input type='hidden' name='case' value='" . $this->getDataInt('id_case') . "' />\n";
			echo "<input type='hidden' name='type' value='stage_change' />\n";

			echo "<span class='label1'>" ._Ti('case_input_stage'). "</span>";
			echo "<span class='value1'><select name='stage' class='sel_frm' onchange='lcm_show(\"submit_stage\")'>\n";

			$stage_kws = get_keywords_in_group_name('stage');
			foreach ($stage_kws as $kw) {
				$sel = ($kw['name'] == $this->data['stage'] ? ' selected="selected"' : '');
				echo "\t\t<option value='" . $kw['name'] . "'" . "$sel>" . _T(remove_number_prefix($kw['title'])) . "</option>\n";
			}

			echo "</select></span>\n";
			echo "<button type='submit' name='submit' id='submit_stage' value='set_stage' style='visibility: hidden;' class='simple_form_btn'>" . _T('button_validate') . "</button>\n";
			echo "</form>\n";

			echo "</li>\n";
		} else {
			echo '<li>' . _Ti('case_input_status') . _T('case_status_option_' . $this->getDataString('status')) . "</li>\n";
			echo '<li>' . _Ti('case_input_stage') . _Tkw('stage', $this->data['stage']) . "</li>\n";
		}

		// If case closed, show conclusion
		if ($this->data['status'] == 'closed') {
			// get the last relevant conclusion
			$q_tmp = "SELECT * 
				FROM lcm_followup
				WHERE id_case = " . $this->getDataInt('id_case') . "
				AND (type = 'conclusion'
						OR type = 'stage_change')
				ORDER BY id_followup DESC 
				LIMIT 1";
			$r_tmp = lcm_query($q_tmp);
			$row_tmp = lcm_fetch_array($r_tmp);

			if ($row_tmp) {
				echo '<li>';
				echo '<div style="background: #f0f0f0; padding: 4px; border: 1px solid #aaa;">';
				echo _Ti('fu_input_conclusion');
				echo get_fu_description($row_tmp, false);
				echo ' <a class="content_link" href="fu_det.php?followup=' . $row_tmp['id_followup'] . '">...</a>';
				echo "</div>\n";
				echo "</li>\n";
			}
		}
		
		// Show users assigned to the case
		$q = "SELECT id_case, a.id_author, name_first, name_middle, name_last
				FROM lcm_case_author as ca, lcm_author as a
				WHERE (id_case=" . $this->getDataInt('id_case') . "
				  AND ca.id_author = a.id_author)";
		
		$authors_result = lcm_query($q);
		$cpt = 0;

		if (lcm_num_rows($authors_result) > 1)
			echo '<li>' 
				. '<span class="label2">'
				. _Ti('case_input_authors')
				. '</span>';
		else
			echo '<li>'
				. '<span class="label2">'
				. _Ti('case_input_author')
				. '</span>';

		while ($author = lcm_fetch_array($authors_result)) {
			if ($cpt)
				echo "; ";

			$name = htmlspecialchars(get_person_name($author));

			echo '<span class="value2">'
				. '<a href="author_det.php?author=' . $author['id_author'] . '" class="content_link"'
				. ' title="' . _T('case_tooltip_view_author_details', array('author' => $name)) . '">'
				. $name
				. "</a>"
				. '</span>';

			if ($admin) {
				echo '<span class="noprint">';
				echo '&nbsp;<a href="edit_auth.php?case=' . $this->getDataInt('id_case') . '&amp;author=' . $author['id_author'] . '"'
					. ' title="' .
					_T('case_tooltip_view_access_rights', array('author' => $name)) . '">'
					. '<img src="images/jimmac/stock_access_rights-16.png" width="16" height="16" border="0" alt="" />'
					. '</a>';
				echo "</span>\n";
			}

			$cpt++;
		}

		// [ML] FIXME Double-check if this is OK here in all scenarios
		if ($admin) {
			echo '<span class="noprint">';
			echo '<a href="sel_auth.php?case=' . $this->getDataInt('id_case') . '" title="' . _T('add_user_case') . '">'
				. '<img src="images/jimmac/stock_attach-16.png" width="16" height="16" border="0" alt="' . _T('add_user_case') . '" />'
				. '</a>';
			echo "</span>\n";
		}
		
		echo "</li>\n";

		echo '<li>' . _Ti('case_input_collaboration');
		echo "<ul style='padding-top: 1px; margin-top: 1px;'>";
		echo "<li>" . _Ti('case_input_collaboration_read') . _T('info_' . ($this->getDataInt('public') ? 'yes' : 'no')) . "</li>\n";
		echo "<li>" . _Ti('case_input_collaboration_write') . _T('info_' . ($this->getDataInt('pub_write') ? 'yes' : 'no')) . "</li>\n";
		echo "</ul>\n";
		echo "</li>\n";
		echo "</ul>\n";

		// clear the right column with stage info
		echo "<div style='clear: right;'></div>\n";
	}

	// XXX error checking! ($_SESSION['errors'])
	function printEdit() {
		// Read site configuration preferences
		$case_assignment_date = read_meta('case_assignment_date');
		$case_comarca  		  = read_meta('case_comarca');
		$case_legal_reason    = read_meta('case_legal_reason');
		$case_p_cliente   	  = read_meta('case_p_cliente');
		$case_allow_modif     = read_meta('case_allow_modif');
		?>
		<script type="text/javascript">
		$(document).ready(function(){

			new Autocomplete("input_comarca", function() {
				this.setValue = function( id, estado, sigla ) {
					$("#id_val").val(id);
					//$("#estado_val").val(estado);
					$("#input_case_state").val(sigla);
				}
				if ( this.isModified )
					this.setValue("");
				if ( this.value.length < 1 && this.isNotClick ) 
					return ;
				return "ajax.php?q=" + this.value;
			});

		});

		$(document).ready(function(){

			new Autocomplete("input_case_vara", function() {
				this.setValue = function( id, estado ) {
					$("#id_val").val(id);
					//$("#estado_val").val(estado);
					//$("#input_case_state").val(sigla);
				}
				if ( this.isModified )
					this.setValue("");
				if ( this.value.length < 1 && this.isNotClick ) 
					return ;
				return "ajax.php?v=" + this.value;
			});

		});
		
		$(document).ready(function(){

			new Autocomplete("input_legal_reason", function() {
				this.setValue = function( id, estado ) {
					$("#id_val").val(id);
					//$("#estado_val").val(estado);
					//$("#input_case_state").val(sigla);
				}
				if ( this.isModified )
					this.setValue("");
				if ( this.value.length < 1 && this.isNotClick ) 
					return ;
				return "ajax.php?a=" + this.value;
			});

		});
		//Ajax p_cliente
		$(document).ready(function(){

			new Autocomplete("input_p_cliente", function() {
				this.setValue = function( id, estado ) {
					$("#id_val").val(id);
					//$("#estado_val").val(estado);
					//$("#input_case_state").val(sigla);
				}
				if ( this.isModified )
					this.setValue("");
				if ( this.value.length < 1 && this.isNotClick ) 
					return ;
				return "ajax.php?c=" + this.value;
			});

		});
		</script>
		<?php
		echo '<table class="tbl_usr_dtl" id="tbl_usr_dtl_upper">' . "\n";
		
		// Case ID (if editing existing case)
		if ($this->getDataInt('id_case')) {
			echo "<tr>"
				. "<td>" . _T('case_input_id') . "</td>"
				. "<td>" . str_pad($this->getDataInt('id_case'), 4,'0',str_pad_left)
				. '<input type="hidden" name="id_case" value="' . $this->getDataInt('id_case') . '" />'
				. "</td></tr>\n";
		}
		
		echo '<tr><td><label for="input_case_p_adverso">' 
			. f_err_star('p_adverso') . _Ti('case_input_p_adverso')
			. "</label></td>\n";
		echo '<td><input size="35" name="p_adverso" id="input_case_p_adverso" value="'
			. clean_output($this->getDataString('p_adverso'))
			. '" class="search_form_txt bestupper" />';
		echo "</td></tr>\n";
		
		//FT Nome diferente do Cliente
		if (substr($case_p_cliente, 0, 3) == 'yes') {
			echo '<tr><td><label for="input_p_cliente">' . f_err_star('p_cliente') . _T('case_input_p_cliente') . "</label>"
				. ($case_p_cliente == 'yes_mandatory' ? '<br/>(' . _T('keywords_input_policy_mandatory') . ')' : '')
				. "</td>\n";
			echo '<td>';
			echo '<input size="35" name="p_cliente" id="input_p_cliente" value="'
				. clean_output($this->getDataString('p_cliente'))
				. '" class="search_form_txt bestupper" />';
			echo "</td></tr>\n";
		}
		
		//FT incluindo o campo processo
		echo '<tr><td><label for="input_case_p_adverso">' 
			. f_err_star('processo') . _T('case_input_processo')
			. "</label></td>\n";
		echo '<td><input size="35" name="processo" id="input_case_processo" value="'
			. clean_output($this->getDataString('processo'))
			. '" class="search_form_txt bestupper" />';
		echo "</td></tr>\n";
		
		// Data de atribuição anterior
		if ($case_assignment_date == 'yes') {
			echo "<tr>\n";
			echo "<td>" . f_err_star('date_assignment') . _Ti('case_input_date_assigned') . "</td>\n";
			echo "<td>" 
				. get_date_inputs('assignment', $this->getDataString('date_assignment'), false)
				. "</td>\n";
			echo "</tr>\n";
		}

		// razão legal
		if (substr($case_legal_reason, 0, 3) == 'yes') {
			echo '<tr><td><label for="input_legal_reason">' . f_err_star('legal_reason') . _T('case_input_legal_reason') . "</label>"
				. ($case_legal_reason == 'yes_mandatory' ? '<br/>(' . _T('keywords_input_policy_mandatory') . ')' : '')
				. "</td>\n";
			echo '<td>';
			echo '<input size="35" name="legal_reason" id="input_legal_reason" value="'
				. clean_output($this->getDataString('legal_reason'))
				. '" class="search_form_txt bestupper" />';
			echo "</td></tr>\n";
		}
		
		// Alledged crime
		//FT alterando o textarea para input
		if (substr($case_comarca, 0, 3) == 'yes') {
			echo '<tr><td><label for="input_comarca">' . f_err_star('comarca') . _T('case_input_comarca') . "</label>"
				. ($case_comarca == 'yes_mandatory' ? '<br/>(' . _T('keywords_input_policy_mandatory') . ')' : '')
				. "</td>\n";
			echo '<td>';
			echo '<input type-"text" size="35" name="comarca" id="input_comarca" class="search_form_txt bestupper" value="' . clean_output($this->getDataString('comarca')) . '" />';
			echo "</td>\n";
			echo "</tr>\n";
		}
		//FT incluindo o campo Estado
		/*echo '<tr><td><label for="input_case_p_adverso">' 
			. f_err_star('state') . _T('case_input_state')
			. "</label></td>\n";
		echo '<td><input size="35" name="state" id="input_case_state" value="'
			. clean_output($this->getDataString('state'))
			. '" class="search_form_txt" />';
		echo "</td></tr>\n";*/
		
		//WL - Modificando o campo da UF para select
		echo '<tr><td><label for="input_case_p_adverso">' . f_err_star('state') . _Ti('case_input_state') . "</label></td>\n";
		echo '<td>';
		echo '<select name="state" id="input_case_state" class="sel_frm">' . "\n";
		$ufs = array('','AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO');
		
		foreach ($ufs as $s) {
			$sel = ($s == $this->getDataString('state') ? ' selected="selected"' : '');
			echo '<option value="' . $s . '"' . $sel . ">" 
				. _T($s)
				. "</option>\n";
		}
		echo "</tr>\n";
		//WL ------------------------
		
		// Keywords (if any)
		show_edit_keywords_form('case', $this->getDataInt('id_case'));
		
		$id_stage = 0; // new case, stage not yet known
		if ($this->getDataString('stage')) {
			$stage = get_kw_from_name('stage', $this->getDataString('stage', '__ASSERT__'));
			$id_stage = $stage['id_keyword'];
		}

		show_edit_keywords_form('stage', $this->getDataInt('id_case'), $id_stage);
		
		//FT incluindo o campo vara
		echo '<tr><td><label for="input_case_p_adverso">' . f_err_star('vara') . _T('case_input_vara') . "</label></td>\n";
		echo '<td>';
		
				selecao_competencia(clean_output($this->getDataString('vara')),'vara','text');
											
				echo "</td></tr>\n";
		
		?>
		<script language="javascript">
			//Função Meio-Mask:
			$(function() {
				$('input:text').setMask();
			});
		</script>
		<?php
		// Notes
		//echo "<tr>\n";
		//echo "<td><label for='input_case_notes'>" . f_err_star('case_notes') . _Ti('case_input_notes') . "</label></td>\n";
		//echo '<td><textarea name="case_notes" id="input_case_notes" class="frm_tarea" rows="3" cols="60">'
		//	. clean_output($this->getDataString('notes'))
		//	. "</textarea>\n"
		//	. "</td>\n";
		//echo "</tr>\n";
		
		//FT incluindo o campo veiculo
		echo '<tr><td><label for="input_case_notes">' . f_err_star('case_notes') . _T('case_input_notes') . "</label></td>\n";
		echo '<td>';

		selecao_veiculo(clean_output($this->getDataString('notes')),'notes','text');
									
		//FT incluínda a Causa
		echo "<tr>\n";
		echo "<td><label for='input_case_causa'>" . f_err_star('case_causa') . _Ti('case_input_causa') . "</label><span style='float:right;'>R$</span></td>\n";
		echo '<td><input type="text" name="case_causa" id="input_case_notes" class="frm_tarea" value="'. clean_output($this->getDataString('causa')). '" alt="decimal"/>';
		echo "</td>\n";
		echo "</tr>\n";
		
		//WL - Valor Fipe
		echo "</td></tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_vlr_fipe'>" . f_err_star('case_vlr_fipe') . _Ti('Valor FIPE') . "</label><span style='float:right;'>R$</span></td>\n";
		echo '<td><input type="text" name="case_vlr_fipe" id="input_case_vlr_fipe" class="frm_tarea" value="'. clean_output($this->getDataString('vlr_fipe')). '" alt="decimal"/>';
		echo "</td>\n";
		echo "</tr>\n";

		//WL - Depósito Judicial
		echo "</td></tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_dep_jud'>" . f_err_star('case_dep_jud') . _Ti('Dep&oacute;sito Judicial') . "</label><span style='float:right;'>R$</span></td>\n";
		echo '<td><input type="text" name="case_dep_jud" id="input_case_dep_jud" class="frm_tarea" value="'. clean_output($this->getDataString('dep_jud')). '" alt="decimal"/>';
		echo "</td>\n";
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_pesq_bem'>" . f_err_star('case_pesq_bem') . _Ti('Pesquisa Bem') . "</label><span style='float:right;'></span></td>\n";
		#echo '<td><input type="text" name="case_pesq_bem" id="input_case_pesq_bem" class="frm_tarea" value="'. clean_output($this->getDataString('pesq_bem')). '"/>';
		
		echo "<td> ";
		echo "<select name='case_pesq_bem' id='input_case_pesq_bem' class='frm_tarea'> ";
		echo "<option value=''></option>";
		echo "<option value='Positiva' ";
		echo $this->getDataString('pesq_bem') == 'Positiva' ? 'selected' : '';
		echo ">Positiva</option>";
		echo "<option value='Negativa' ";
		echo $this->getDataString('pesq_bem') == 'Negativa' ? 'selected' : '';
		echo ">Negativa</option> ";
		echo "<option value='Hipoteca' ";
		echo $this->getDataString('pesq_bem') == 'Hipoteca' ? 'selected' : '';
		echo ">Hipoteca</option> ";
		echo "</select>";
		echo "</td>\n";
		echo "</td>\n";
		echo "</tr>\n";
				
		echo "<tr>\n";
		echo "<td><label for='input_case_cultura'>" . f_err_star('case_cultura') . _Ti('Cultura') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_cultura" id="input_case_cultura" class="frm_tarea" value="'. clean_output($this->getDataString('cultura')). '"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_qtd_mand_neg'>" . f_err_star('case_qtd_mand_neg') . _Ti('Qtd. Mand. Neg.') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_qtd_mand_neg" id="input_case_qtd_mand_neg" class="frm_tarea" value="'. clean_output($this->getDataString('qtd_mand_neg') > 0 ? $this->getDataString('qtd_mand_neg') : '0'). '"/>';
		echo "</td>\n";
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_dt_contrato'>" . f_err_star('case_dt_contrato') . _Ti('Data do Contrato') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_dt_contrato" id="input_case_dt_contrato" class="frm_tarea" value="'. clean_output($this->getDataString('dt_contrato')). '"  alt="date"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_vlr_tot_fin'>" . f_err_star('case_vlr_tot_fin') . _Ti('Valor Total Financiado') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_vlr_tot_fin" id="input_case_vlr_tot_fin" class="frm_tarea" value="'. clean_output($this->getDataString('vlr_tot_fin')). '" alt="decimal"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_tot_parc'>" . f_err_star('case_tot_parc') . _Ti('Total de Parcelas') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_tot_parc" id="input_case_tot_parc" class="frm_tarea" value="'. clean_output($this->getDataString('tot_parc')). '"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_vlr_princ'>" . f_err_star('case_vlr_princ') . _Ti('Valor Principal') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_vlr_princ" id="input_case_vlr_princ" class="frm_tarea" value="'. clean_output($this->getDataString('vlr_princ')). '" alt="decimal"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_dt_pri_parc'>" . f_err_star('case_dt_pri_parc') . _Ti('Data Primeira Parcela') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_dt_pri_parc" id="input_case_dt_pri_parc" class="frm_tarea" value="'. clean_output($this->getDataString('dt_pri_parc')). '" alt="date"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_dt_ult_parc'>" . f_err_star('case_dt_ult_parc') . _Ti('Data &Uacute;ltima Parcela') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_dt_ult_parc" id="input_case_dt_ult_parc" class="frm_tarea" value="'. clean_output($this->getDataString('dt_ult_parc')). '" alt="date"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_pri_parc_atra'>" . f_err_star('case_pri_parc_atra') . _Ti('Prim. Parc. Atraso') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_pri_parc_atra" id="input_case_pri_parc_atra" class="frm_tarea" value="'. clean_output($this->getDataString('pri_parc_atra')). '"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_dt_pri_parc_atra'>" . f_err_star('case_dt_pri_parc_atra') . _Ti('Dt. Prim. Parc. Atraso') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_dt_pri_parc_atra" id="input_case_dt_pri_parc_atra" class="frm_tarea" value="'. clean_output($this->getDataString('dt_pri_parc_atra')). '" alt="date"/>';
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_dt_atz'>" . f_err_star('case_dt_atz') . _Ti('Data Atualiza&ccedil;&atilde;o') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_dt_atz" id="input_case_dt_atz" class="frm_tarea" value="'. clean_output($this->getDataString('dt_atz')). '" alt="date"/>';
		echo "</tr>\n";
		
		echo '<tr><td><label for="input_penhora">' . f_err_star('penhora') . _Ti('Penhora') . "</label></td>\n";
		echo '<td>';
		echo '<select name="penhora" id="input_penhora" class="sel_frm">' . "\n";
		$penhoras = array('','Bem Móvel','Imóvel','Veículo','Bancejud');
		
		foreach ($penhoras as $s) {
			$sel = ($s == $this->getDataString('penhora') ? ' selected="selected"' : '');
			echo '<option value="' . $s . '"' . $sel . ">" 
				. _T($s)
				. "</option>\n";
		}
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_seguimento'>" . f_err_star('case_seguimento') . _Ti('Seguimento') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_seguimento" id="input_case_seguimento" class="frm_tarea" value="'. clean_output($this->getDataString('seguimento')). '"/>';
		echo "</td>\n";
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_fase'>" . f_err_star('case_fase') . _Ti('Fase (CNHi)') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_fase" id="input_case_fase" class="frm_tarea" value="'. clean_output($this->getDataString('fase')). '"/>';
		echo "</td>\n";
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='input_case_alerta'>" . f_err_star('case_alerta') . _Ti('Alerta') . "</label><span style='float:right;'></span></td>\n";
		echo '<td><input type="text" name="case_alerta" id="input_case_alerta" class="frm_tarea" value="'. clean_output($this->getDataString('alerta')). '"/>';
		echo "</tr>\n";
		
		echo "</select></td>\n";
		echo "</tr>\n";
		
		// Case status
		echo '<tr><td><label for="input_status">' . f_err_star('status') . _Ti('case_input_status') . "</label></td>\n";
		echo '<td>';
		echo '<select name="status" id="input_status" class="sel_frm">' . "\n";
		$statuses = ($this->getDataInt('id_case') ? array('draft','open','suspended','closed','merged') : array('draft','open') );
		
		foreach ($statuses as $s) {
			$sel = ($s == $this->getDataString('status') ? ' selected="selected"' : '');
			echo '<option value="' . $s . '"' . $sel . ">" 
				. _T('case_status_option_' . $s)
				. "</option>\n";
		}

		echo "</select></td>\n";
		echo "</tr>\n";
		
		// Case stage
		if (! $this->getDataString('stage'))
			$this->data['stage'] = get_suggest_in_group_name('stage');
		
		$kws = get_keywords_in_group_name('stage');
		
		echo '<tr><td><label for="input_stage">' . f_err_star('stage') . _T('case_input_stage') . "</label></td>\n";
		echo '<td><select name="stage" id="input_stage" class="sel_frm">' . "\n";
		foreach($kws as $kw) {
			$sel = ($kw['name'] == $this->data['stage'] ? ' selected="selected"' : '');
			echo "\t\t\t\t<option value='" . $kw['name'] . "'" . "$sel>" . _T(remove_number_prefix($kw['title'])) . "</option>\n";
		}
		echo "</select></td>\n";
		echo "</tr>\n";
		
		// Public access rights
		// FIXME FIXME FIXME
		if($GLOBALS['author_session']['status']== 'admin'){
			if ( $this->data['admin'] || (read_meta('case_read_always') != 'yes') || (read_meta('case_write_always') != 'yes') ) {
				$dis = isDisabled(! allowed($this->getDataInt('id_case'), 'a'));
				echo '<tr><td colspan="2">' . _T('case_input_collaboration')
					.  ' <br /><ul>';

				if ( (read_meta('case_read_always') != 'yes') || $GLOBALS['author_session']['status'] == 'admin') {
					echo '<li style="list-style-type: none;">';
					echo '<input type="checkbox" name="public" id="case_public_read" value="yes"';

					if ($_SESSION['form_data']['public'])
						echo ' checked="checked"';

					echo "$dis />";
					echo '<label for="case_public_read">' . _T('case_input_collaboration_read') . "</label></li>\n";
				}

				if ( (read_meta('case_write_always') != 'yes') || _session('admin')) {
					echo '<li style="list-style-type: none;">';
					echo '<input type="checkbox" name="pub_write" id="case_public_write" value="yes"';

					if (_session('pub_write'))
						echo ' checked="checked"';

					echo "$dis />";
					echo '<label for="case_public_write">' . _T('case_input_collaboration_write') . "</label></li>\n";
				}

				echo "</ul>\n";

				echo "</td>\n";
				echo "</tr>\n";
			}
		} else {
			echo '<tr><td colspan="2" style="display:none">' . _T('case_input_collaboration')
					.  ' <br /><ul>';
					echo '<li style="list-style-type: none;">';
					echo '<input type="checkbox" name="public" id="case_public_read" value="yes"';

					if ($_SESSION['form_data']['public'])
						echo ' checked="checked"';

					echo "$dis />";
					echo '<label for="case_public_read">' . _T('case_input_collaboration_read') . "</label></li>\n";
					
					echo '<li style="list-style-type: none;">';
					echo '<input type="checkbox" name="pub_write" id="case_public_write" value="yes"';

					if (_session('pub_write'))
						echo ' checked="checked"';

					echo "$dis />";
					echo '<label for="case_public_write">' . _T('case_input_collaboration_write') . "</label></li>\n";
				echo "</ul>\n";
				echo "</td>\n";
				echo "</tr>\n";
		}

		echo "</table>\n";
	}

	function printFollowups($show_filters = false) {
		$cpt = 0;
		$my_list_pos = intval(_request('list_pos', 0));

		show_page_subtitle(_T('case_subtitle_followups'), 'cases_followups');

		// Show filters (if not shown in ajaxed page)
		if ($show_filters) {
			// By default, show from "case creation date" to NOW().
			$link = new Link();
			$link->delVar('date_start_day');
			$link->delVar('date_start_month');
			$link->delVar('date_start_year');
			$link->delVar('date_end_day');
			$link->delVar('date_end_month');
			$link->delVar('date_end_year');
			echo $link->getForm();

			$date_end = get_datetime_from_array($_REQUEST, 'date_end', 'end', '0000-00-00 00:00:00'); // date('Y-m-d H:i:s'));
			$date_start = get_datetime_from_array($_REQUEST, 'date_start', 'start', '0000-00-00 00:00:00'); // $row['date_creation']);

			echo _Ti('time_input_date_start');
			echo get_date_inputs('date_start', $date_start);

			echo _Ti('time_input_date_end');
			echo get_date_inputs('date_end', $date_end);
			echo ' <button name="submit" type="submit" value="submit" class="simple_form_btn">' . _T('button_validate') . "</button>\n";
			echo "</form>\n";

			echo "<div style='margin-bottom: 4px;'>&nbsp;</div>\n"; // FIXME patch for now (leave small space between filter and list)
		}

		show_listfu_start('general', false);

		for ($cpt = 0, $this->getFollowupStart(); (! $this->getFollowupDone()); $cpt++) {
			$item = $this->getFollowupIterator();
			show_listfu_item($item, $cpt);
		}

		if (! $cpt)
			echo "No followups"; // TRAD

		show_list_end($my_list_pos, $this->getFollowupTotal(), true);
	}
}

class LcmCaseListUI extends LcmObject {
	var $list_pos;
	var $number_of_rows;

	// filters (other smaller details are set in LcmObjet's data)
	var $search;
	var $date_start;
	var $date_end;

	function LcmCaseListUI() {
		$this->LcmObject();

		$this->search = '';
		$this->date_start = '';
		$this->date_end = '';

		$this->list_pos = intval(_request('list_pos', 0));
		$this->number_of_rows = 0;
	}

	function setSearchTerm($term) {
		$this->search = $term;
	}

	function setDateInterval($start, $end) {
		if ($start && $start != -1)
			$this->date_start = $start;

		if ($end && $end != -1)
			$this->date_end = $end;
	}

	function start() {
		show_listcase_start();
	}

	function printList() {
	
		//FT criando novos filtros
		$pasta_int = (int) $_GET['pasta_f'];
		$_GET['pasta_f']    != "" ? $pasta_fil  = "AND c.id_case 	    =  ".$pasta_int."		   " : "";
		$_GET['state_f']    != "" ? $state_f    = "AND c.state 		    = '".$_GET['state_f']."'    " : "";
		$_GET['type_f']     != "" ? $type_f 	= "AND fu.type 		    = '".$_GET['type_f']."' 	   " : "";
		$_GET['cliente_f']  != "" ? $cliente_f  = "AND cco.id_cliente   = '".$_GET['cliente_f']."'  " : "";
		$_GET['condicao_f'] != "" ? $condicao_f = "AND kc.id_keyword    =  ".$_GET['condicao_f']."  " : "";
		$_GET['diversos_f'] != "" ? $diversos_f = "AND kc.id_keyword    =  ".$_GET['diversos_f']."  " : "";
		$_GET['processo_f'] != "" ? $processo_f = "AND c.processo   like '%".$_GET['processo_f']."%'" : "";
		$_GET['parte_f']    != "" ? $parte_f    = "AND p_adverso    like '%".$_GET['parte_f']."%' "   : "";
		$_GET['comar_f']    != "" ? $comar_f    = "AND comarca 	    like '%".$_GET['comar_f']."%' "   : "";
		$_GET['status_f']   != "" ? $status_f   = "AND c.status   	in   ('".$_GET['status_f']."') " : $status_f = "AND c.status in ('open','closed','suspended') ";
		$_GET['stopday_f']  != "" ? $stopday_f	= "AND DATEDIFF(curdate(), fu.date_start) >= '".$_GET['stopday_f']."' " : "";
		$_GET['vara_f']     != "" ? $vara_f 	= "AND c.vara 	    like '%".trim(str_replace('_|_', ' ',$_GET['vara_f'])). "%' " : "";
		$_GET['acao_f']     != "" ? $acao_f 	= "AND c.legal_reason	  = '".trim(str_replace('_|_', ' ', $_GET['acao_f'])). "' " : "";
		
		//$last_f = "AND fu.date_start in (SELECT max(ff.date_start) FROM lcm_followup AS ff where ff.id_case = fu.id_case order by ff.id_followup asc) ";
		
		global $prefs;

		// Select cases of which the current user is author
		$q  = " SELECT";
		$q .= " DISTINCT c.id_case,";
		$q .= " c.p_adverso,";
		$q .= " c.status,";
		$q .= " c.public,";
		$q .= " c.pub_write,";
		$q .= " c.date_creation,";
		$q .= " c.processo,";
		$q .= " c.vara,";
		$q .= " c.comarca,";
		$q .= " c.state,";
		$q .= " DATEDIFF(CURDATE(), max(fu.date_start)) AS stopday,";
		$q .= " o.name,";
		$q .= " c.legal_reason,";
		$q .= " c.p_cliente,";
		$q .= " fu.type as type,";
		$q .= " fu.description as description,";
		$q .= " fu.date_start";
		$q .= " FROM lcm_case as c";
		$q .= " JOIN lcm_case_author AS a ON a.id_case = c.id_case";
		$q .= " LEFT JOIN lcm_followup AS fu ON c.id_case = fu.id_case";
		$q .= " LEFT JOIN lcm_case_adverso_cliente AS cco ON cco.id_case = c.id_case";
		$q .= " LEFT JOIN lcm_cliente AS o ON o.id_cliente = cco.id_cliente ";
		
		if($_GET['last_f']	 == "on" || $_GET['stopday_f'] != "")
		$last_f = "AND fu.date_start in (SELECT max(ff.date_start) FROM lcm_followup AS ff where ff.id_case = fu.id_case order by ff.id_followup asc) ";
		//$q .= " JOIN lcm_followup_last AS fl ON c.id_case = fl.id_case";
		
		if ($this->search || $_GET['condicao_f']!= "" || $_GET['diversos_f']!= "")
			$q .= " LEFT JOIN lcm_keyword_case as kc on kc.id_case = c.id_case ";

		//
		// Apply filters to SELECT output

		$q .= " WHERE 1=1 ";

		// Add search criteria, if any
		if ($this->search) {
			$q .= " AND (";

			if (is_numeric($this->search))
				$q .= " (c.id_case = $this->search) OR ";
		
			$q .= " (kc.value LIKE '%" . $this->search . "%') OR "
			   . " (c.p_adverso LIKE '%" . $this->search . "%') OR "
			   . " (c.processo LIKE '%" . $this->search . "%') ";

			$q .= " )";
		}

		//
		// Proprietário Case: pode ser usado por archives.php listcases.php, author_det.php, etc
		// Além disso, ele pode ser um usuário verificar o perfil de outro usuário (neste caso, mostram apenas os casos públicos)
		// Ou pode ser um administrador verificar o perfil de outro usuário. etc
		//
		global $author_session;	
		
		$owner_filter = $this->getDataString('owner', $prefs['case_owner']);
		$owner_id     = $this->getDataInt('id_author', $author_session['id_author']);
		
		$q_owner = " (a.id_author = " . $owner_id;

		if ($owner_id == $author_session['id_author']) {
			// Quer na listcases, ou usuário olhando para sua página no author_det
			if ($owner_filter == 'public')
				$q_owner .= " OR c.public = 1";
			//FT dando permissão ao gestor
			if ($author_session['status'] == 'admin' && $owner_filter == 'all' || $author_session['status'] == 'manager' && $owner_filter == 'public')
				$q_owner .= " OR 1=1 ";
		} else {
			// Se não for um administrador, mostram apenas os casos públicos de que o usuário
			if ($author_session['status'] != 'admin')
				$q_owner .= " AND c.public = 1";
		}

		$q_owner .= " ) ";
		$q .= " AND " . $q_owner; 
		//FT Incluído as condições do filtro
		$q .= " " . $pasta_fil;
		$q .= " " . $processo_f;
		$q .= " " . $status_f;
		$q .= " " . $parte_f;
		$q .= " " . $comar_f;
		$q .= " " . $state_f;
		$q .= " " . $type_f;
		$q .= " " . $stopday_f;
		$q .= " " . $cliente_f;
		$q .= " " . $vara_f;
		$q .= " " . $acao_f;
		$q .= " " . $condicao_f;
		$q .= " " . $last_f;
		$q .= " " . $diversos_f;

		// Period (date_creation) to show
		//FT Criando o "all" para todos os anos
		if($_GET['case_period']!="all") {
			if ($this->date_start || $this->date_end) {
				if ($this->date_start)
					$q .= " AND c.date_creation >= '" . $this->date_start . "'";

				if ($this->date_end)
					$q .= " AND c.date_creation <= '" . $this->date_end . "'";
			} else {
				if ($prefs['case_period'] < 1900) // since X days
					$q .= " AND " . lcm_query_subst_time('c.date_creation', 'NOW()') . ' < ' . $prefs['case_period'] * 3600 * 24;
				else // for year X
					$q .= " AND " . lcm_query_trunc_field('c.date_creation', 'year') . ' = ' . $prefs['case_period'];
			}
		}
		//
		// Sort results
		//

		$sort_clauses = array();
		$sort_allow = array('ASC' => 1, 'DESC' => 1);

		// Sort cases by creation date
		if ($sort_allow[_request('status_order')])
			$sort_clauses[] = "c.status " . _request('status_order');
		//FT inclusão de alguns "order"
		elseif ($sort_allow[_request('pasta_order')])
			$sort_clauses[] = "c.id_case " . _request('pasta_order');
		elseif ($sort_allow[_request('p_adverso_order')])
			$sort_clauses[] = "c.p_adverso " . _request('p_adverso_order');
		elseif ($sort_allow[_request('comar_order')])
			$sort_clauses[] = "c.comarca " . _request('comar_order');
		elseif ($sort_allow[_request('stopday_order')])
			$sort_clauses[] = "stopday " . _request('stopday_order');

		if ($sort_allow[_request('case_order')])
			$sort_clauses[] = 'c.date_creation ' . _request('case_order');
		elseif ($sort_allow[_request('upddate_order')])
			$sort_clauses[] = "date_update " . _request('upddate_order');
		else
			$sort_clauses[] = 'c.date_creation DESC'; // default

		$q .= " GROUP BY c.id_case ";
		$q .= " ORDER BY " . implode(', ', $sort_clauses);

		//echo $q;
		//FT Criando a exportação para excel
		?>
		<script language="javascript">
		function fc_cad_dados()
		{
			document.form.action = "aj_externo.php";
			document.form.submit();
		}
		</script>
		<form name="form" action="aj_externo.php" method="post" target="_blank" style="margin:0px;">
			<input type="hidden" name="id_dados"  value="<?php echo $q; ?>">
			<input type="hidden" name="flag"  value="exp_proc">
		</form>
		<?php
		
		$result = lcm_query($q);

		// Check for correct start position of the list
		$this->number_of_rows = lcm_num_rows($result);

		if ($this->list_pos >= $this->number_of_rows)
			$this->list_pos = 0;

		// Position to the page info start
		if ($this->list_pos > 0) {
			if (! lcm_data_seek($result, $this->list_pos))
				lcm_panic("Error seeking position " . $this->list_pos . " in the result");
		}

		for ($i = 0; (($i<$prefs['page_rows']) && ($row = lcm_fetch_array($result))); $i++)
			show_listcase_item($row, $i, $this->search);
	}

	function finish() {
		show_listcase_end($this->list_pos, $this->number_of_rows);
	}
}

?>