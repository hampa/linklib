<?php
require_once('linkontrol/template.php');
require_once('linkontrol/global.php');
require_once('linkontrol/access.php');
require_once('linkontrol/functions_linkontrol.php');

eval('$content .= "' . fetchTemplate('register') . '";');
eval('printOutput("' . fetchTemplate('shell') . '");');
?>
