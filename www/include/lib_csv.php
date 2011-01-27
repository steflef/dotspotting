<?php

	#
	# $Id$
	#

	#################################################################

	function csv_parse_fh($fh, $more=array()){

		$keys = array();
		$data = array();

		$checked_fieldnames = 0;	# see below

		$field_names = (is_array($more['field_names'])) ? $more['field_names'] : null;

		$errors = array();
		$record = 0;

		while (! feof($fh)){

			$record ++;

			if (($more['max_records']) && ($record > $more['max_records'])){
				break;
			}

			$ln = trim(fgets($fh));

			if (! $ln){
				continue;
			}

			if (preg_match("/^#/", $ln)){
				continue;
			}

			$row = str_getcsv($ln);

			if (! $row){
				continue;
			}

			if (($record === 1) && (! $field_names)){

				foreach ($row as $col){
					$field_names[] = strtolower($col);
				}

				continue;
			}

			#
			# Okay, first check to make sure that we have some kind
			# of remotely sane input as column names - if we don't
			# even have that then there's not much point in going any
			# further.
			#

			if (! $checked_fieldnames){

				for ($i = 0; $i < count($field_names); $i++){

					$raw = $field_names[$i];
					$clean = sanitize($raw, 'str');

					if (! $clean){

						return array(
							'ok' => 0,
							'error' => "the field name '{$raw}' failed validation",
						);
					}

					$field_names[$i] = $clean;
				}

				$checked_fieldnames = 1;
			}

			#
			# Okay, go!
			#

			$tmp = array();

			for ($i = 0; $i < count($field_names); $i++){

				$raw = trim($row[$i]);
				$clean = sanitize($raw, 'str');

				$tmp[ $field_names[$i] ] = $clean;

				if (($raw) && (! $clean)){

					$errors[] = array(
						'record' => $record,
						'error' => "invalid input for the '{$field_names[$i]}' column",
					);
				}
			}

			$data[] = $tmp;
		}

		fclose($fh);

		return array(
			'ok' => 1,
			'data' => &$data,
			'errors' => &$errors,
		);
	}

	#################################################################

	function csv_export_dots(&$rows, $more=array()){

		$fh = fopen($more['path'], 'w');

		fputcsv($fh, $more['columns']);

		foreach ($rows as $row){
			fputcsv($fh, array_values($row));
		}

		fclose($fh);
		return $more['path'];
	}

	#################################################################
?>