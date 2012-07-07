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
else if ($_GET['do'] == 'delete_time_feed') {
	$timefeedid = intval($_REQUEST['timefeedid']);
	$linkontrol->deleteTimeFeed($timefeedid);
	$msg = "deleted time feed $timefeedid";
}
else if ($_GET['do'] == 'add_movie') {
	$userid = intval($_REQUEST['userid']);
	$name = mysql_real_escape_string($_REQUEST['name']);
	$href = mysql_real_escape_string($_REQUEST['href']);

	if ($name == '') {
		$msg = "Name cannot be empty";
		return;
	}
	$movieid = $linkontrol->addMovie($userid, $name, $href);
	$msg = "added movie $movieid";
}
else if ($_GET['do'] == 'create_session') {
	$movieid = intval($_REQUEST['movieid']);
	if ($movieid == 0) {
		$msg = "No movieid specified";
		return;
	}
	$sessionkey = $linkontrol->createSession($movieid);
	$msg = "Created session $sessionkey for movieid $movieid";
}
else if ($_GET['do'] == 'get_session') {
	$sessionkey = mysql_real_escape_string($_REQUEST['sessionkey']);
	$arr = $linkontrol->getSession($sessionkey);
	$movieid = intval($arr['movieid']);
	if (isset($arr)) {
		$msg = "Movie Id $movieid";
	}
	else {
		$msg = "could not find session";
	}
}
else if ($_GET['do'] == 'dump_time_feed') {
	$movieid = intval($_GET['movieid']);
        $arr = $linkontrol->getTimeFeed($movieid);
        if (isset($arr)) {
                foreach ($arr as $key => $val) {
                        echo($linkontrol->timeFeedToJson($val));
                }
        }
}
?>
