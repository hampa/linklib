<?php
require_once('linkontrol/template.php');
require_once('linkontrol/global.php');
include_once("linkontrol/functions_linkontrol.php");

$linkontrol = new linkontrol();
$sessionkey = mysql_real_escape_string($_GET['id']);
if ($sessionkey == '') {
	include_once('remote_start.php');
	die();
}
$arr = $linkontrol->getSession($sessionkey);
$movieid = 0;
$movie_name = "No movie specified";
if (isset($arr)) {
	$movieid = $arr['movieid'];
	$movie_name = $arr['moviename'];
}

$arr = $linkontrol->getTimeFeeds($movieid);
if (isset($arr)) {
	$arr_reverse = array_reverse($arr);
	foreach ($arr_reverse as $key => $val) {
		$timefeed_list .= $linkontrol->timeFeedToList($val);
	}
}

eval('$content .= "' . fetchTemplate('remote') . '";');
die($content);
?>
