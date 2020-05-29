<?php

// Not needed for now, but maybe later?
// include_lcm('inc_obj_export_generic');

class LcmExportCSV /* extends LcmExportObject */ {

	function LcmExportCSV() {
		// $this->LcmExportObject();
	}

	// Note: $helpref is not used for this exporter
	function printStartDoc($title, $description, $helpref) {
		$title = trim($title);
		$description = trim($description);
	
		if (! $description)
			$description = $title;
	
		header("Content-Type: text/comma-separated-values");
		header('Content-Disposition: filename="' . $title . '.csv"');
		header("Content-Description: " . $description);
		header("Content-Transfer-Encoding: binary");
	}

	function printHeaderValueStart() {

	}

	function printHeaderValue($val) {
		$val = _Th(remove_number_prefix($val));
		echo utf8_decode($val) . ';';
	}

	function printHeaderValueEnd() {
		$this->printEndLine();
	}

	function printValue($val, $h, $css) {
		$align = '';

		// Maybe formalise 'time_length' filter, but check SQL pre-filter also
		if ($h['filter_special'] == 'time_length') {
			// $val = format_time_interval_prefs($val);
			$val = format_time_interval($val, true, '%.2f');
			if (! $val)
				$val = 0;
		} elseif ($h['description'] == 'time_input_length') {
			$val = format_time_interval($val, true, '%.2f');
			if (! $val)
				$val = 0;
		}

		switch ($h['filter']) {
			case 'date':
				// we leave the date in 0000-000-00 00:00:00 format
				break;
			case 'currency':
				if ($val)
					$val = format_money($val);
				else
					$val = 0;
				break;
			case 'number':
				$align = 'align="right"';
				if (! $val)
					$val = 0;
				break;
		}

		if (is_numeric($val)) {
			echo utf8_decode($val) . "; ";
		} else {
			// escape " character (csv)
			$val = str_replace('"', '""', $val); 
			echo utf8_decode($val) . '; ';
		}
	}

	function printStartLine() {
		// nothing
	}

	function printEndLine() {
		echo "\n";
	}

	function printEndDoc() {
		// nothing
	}
}

?>
