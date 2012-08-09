<?php
require_once('linkontrol/template.php');
require_once('linkontrol/global.php');
require_once('linkontrol/access.php');
require_once('linkontrol/functions_linkontrol.php');

if ($username != '') {
	eval('$content .= "' . fetchTemplate('account') . '";');
}
else {
	eval('$content .= "' . fetchTemplate('noaccess') . '";');
}

eval('printOutput("' . fetchTemplate('shell') . '");');
?>
