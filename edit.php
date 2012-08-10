<?php
require_once('linkontrol/template.php');
require_once('linkontrol/global.php');
require_once('linkontrol/access.php');
require_once('linkontrol/functions_linkontrol.php');

$movieid = intval($_GET['movieid']);

$linkontrol = new linkontrol();

$arr = $linkontrol->getMovie($movieid);
$movie_name = $arr['name'];
$movie_href = $arr['href'];

$arr = $linkontrol->getTimeFeeds($movieid);
$index = 1;
if (isset($arr)) {
	foreach ($arr as $key => $val) {
		$link_list .= "\t\t" . $linkontrol->timeFeedToHtmlForm($index++, $val);
	}
}

eval('$content .= "' . fetchTemplate('edit') . '";');
eval('printOutput("' . fetchTemplate('shell') . '");');
?>
