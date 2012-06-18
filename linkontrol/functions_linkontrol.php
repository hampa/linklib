<?php
require_once('linkontrol/global.php');
require_once('linkontrol/class_linkontrol.php');

$linkontrol = new linkontrol;

if ($_GET['do'] == 'add_time_feed') {
	$movieid = intval($_REQUEST['movieid']);
	$userid = intval($_REQUEST['userid']);
	$start = intval($_REQUEST['start']);
	$end = intval($_REQUEST['end']);
	$title = mysql_real_escape_string($_REQUEST['title']);
	$img = mysql_real_escape_string($_REQUEST['img']);
	$body = mysql_real_escape_string($_REQUEST['body']);
	$href = mysql_real_escape_string($_REQUEST['href']);
	$feedid = $linkontrol->addTimeFeed($movieid, $userid, $start, $end, $title, $img, $body, $href);
	$msg = "added time feed $feedid";
}
?>
