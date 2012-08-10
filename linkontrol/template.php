<?php
function fetchTemplate($templatename) {
	$fp = fopen(TP_PATH . $templatename . TP_EXTN, "r");
	if ($fp) {
		while(!feof($fp)) {
			$template .= str_replace(array('"'), array('\"'), fread($fp, 4096));
		}
	}
	$template = processTemplateConditionals($template);
	return $template;
}

function printOutput($output) {
	echo $output;
}

function processTemplateConditionals($template) {
	$if_lookfor = '<if condition=';
	$if_location = -1;
	$if_end_lookfor = '</if>';
	$if_end_location = -1;

	$else_lookfor = '<else />';
	$else_location = -1;

	$condition_value = '';
	$true_value = '';
	$false_value = '';

	$template_cond = $template;

	static $safe_functions;
	if (!is_array($safe_functions)) {
		$safe_functions = array(
				0 => 'and',
				1 => 'or',
				2 => 'xor',

				'in_array',
				'is_array',
				'is_numeric',
				'isset',
				'empty',
				'defined',
				'array',
				);
	}

	while (1) {
		$condition_end = 0;
		$strlen = strlen($template_cond);

		$if_location = strpos($template_cond, $if_lookfor, $if_end_location + 1); // look for opening <if>
		if ($if_location === false)	{
			break;
		}

		$condition_start = $if_location + strlen($if_lookfor) + 2; // the beginning of the conditional

		$delimiter = $template_cond[$condition_start - 1];
		if ($delimiter != '"' AND $delimiter != '\'')
		{
			$if_end_location = $if_location + 1;
			continue;
		}

		$if_end_location = strpos($template_cond, $if_end_lookfor, $condition_start + 3); // location of conditional terminator
		if ($if_end_location === false){
			return str_replace("\\'", '\'', $template_cond); // no </if> found -- return the original template
		}

		for ($i = $condition_start; $i < $strlen; $i++) {
			if ($template_cond["$i"] == $delimiter AND $template_cond[$i - 2] != '\\' AND $template_cond[$i + 1] == '>') {
				$condition_end = $i - 1;
				break;
			}
		}
		if (!$condition_end) {
			return str_replace("\\'", '\'', $template_cond);
		}

		$condition_value = substr($template_cond, $condition_start, $condition_end-$condition_start);
		if (empty($condition_value)) {
			// something went wrong
			$if_end_location = $if_location + 1;
			continue;
		}
		else if (strpos($condition_value, '`') !== false) {
			debugEcho('expression_contains_backticks_x_please_rewrite_without', htmlspecialchars('<if condition="' . stripslashes($condition_value) . '">'));
		}
		else {
			if (preg_match_all('#([a-z0-9_\x7f-\xff\\\\{}$>-\\]]+)(\s|/\*.*\*/|(\#|//)[^\r\n]*(\r|\n))*\(#si', $condition_value, $matches)) {
				$functions = array();
			foreach($matches[1] AS $key => $match) {
				if (!in_array(strtolower($match), $safe_functions)) {
					$funcpos = strpos($condition_value, $matches[0]["$key"]);
					$functions[] = array(
							'func' => stripslashes($match),
							'usage' => stripslashes(substr($condition_value, $funcpos, (strpos($condition_value, ')', $funcpos) - $funcpos + 1))),
							);
				}
			}
		}
		}

		if ($template_cond[$condition_end + 2] != '>') {
			// the > doesn't come right after the condition must be malformed
			$if_end_location = $if_location + 1;
			continue;
		}

		// look for recursive case in the if block -- need to do this so the correct </if> is looked at
		$recursive_if_loc = $if_location;
		while (1) {
			$recursive_if_loc = strpos($template_cond, $if_lookfor, $recursive_if_loc + 1); // find an if case
			if ($recursive_if_loc === false OR $recursive_if_loc >= $if_end_location) {
				//not found or out of bounds
				break;
			}

			// the bump first level's recursion back one </if> at a time
			$recursive_if_end_loc = $if_end_location;
			$if_end_location = strpos($template_cond, $if_end_lookfor, $recursive_if_end_loc + 1);
			if ($if_end_location === false)	{
				return str_replace("\\'", "'", $template_cond); // no </if> found -- return the original template
			}
		}

		$else_location = strpos($template_cond, $else_lookfor, $condition_end + 3); // location of false portion

		// this is needed to correctly identify the <else /> tag associated with the outermost level
		while (1) {
			if ($else_location === false OR $else_location >= $if_end_location)	{
				// else isn't found/in a valid area
				$else_location = -1;
				break;
			}

			$temp = substr($template_cond, $condition_end + 3, $else_location - $condition_end + 3);
			$opened_if = substr_count($temp, $if_lookfor); // <if> tags opened between the outermost <if> and the <else />
			$closed_if = substr_count($temp, $if_end_lookfor); // <if> tags closed under same conditions
			if ($opened_if == $closed_if) { 
				// if this is true, we're back to the outermost level
				// and this is the correct else
				break;
			}
			else {
				// keep looking for correct else case
				$else_location = strpos($template_cond, $else_lookfor, $else_location + 1);
			}
		}

		if ($else_location == -1) {
			// no else clause
			$read_length = $if_end_location - strlen($if_end_lookfor) + 1 - $condition_end + 1; // number of chars to read
			$true_value = substr($template_cond, $condition_end + 3, $read_length); // the true portion
			$false_value = '';
		}
		else {
			$read_length = $else_location - $condition_end - 3; // number of chars to read
			$true_value = substr($template_cond, $condition_end + 3, $read_length); // the true portion

			$read_length = $if_end_location - strlen($if_end_lookfor) - $else_location - 3; // number of chars to read
			$false_value = substr($template_cond, $else_location + strlen($else_lookfor), $read_length); // the false portion
		}

		if (strpos($true_value, $if_lookfor) !== false) {
			$true_value = processTemplateConditionals($true_value);
		}
		if (strpos($false_value, $if_lookfor) !== false) {
			$false_value = processTemplateConditionals($false_value);
		}

		// clean up the extra slashes
		$str_find = array('\\"', '\\\\');
		$str_replace = array('"', '\\');
		if ($delimiter == "'") {
			$str_find[] = "\\'";
			$str_replace[] = "'";
		}

		$str_find[] = '\\$delimiter';
		$str_replace[] =  $delimiter;

		$condition_value = str_replace($str_find, $str_replace, $condition_value);

		$conditional = "\".(($condition_value) ? (\"$true_value\") : (\"$false_value\")).\"";
		$template_cond = substr_replace($template_cond, $conditional, $if_location, $if_end_location + strlen($if_end_lookfor) - $if_location);

		$if_end_location = $if_location + strlen($conditional) - 1; // adjust searching position for the replacement above
	}
	return str_replace("\\'", "'", $template_cond);
}
?>
