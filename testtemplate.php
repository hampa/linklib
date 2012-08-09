<?php
require_once('linkontrol/template.php');
require_once('linkontrol/global.php');

$content = "<p>content</p>";

eval('$content .= "' . fetchTemplate('testtemplate') . '";');
eval('printOutput("' . fetchTemplate('shell') . '");');
?>
