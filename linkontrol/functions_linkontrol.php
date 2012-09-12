<?php
require_once('linkontrol/global.php');
require_once('linkontrol/class_linkontrol.php');
require_once('linkontrol/class_scrape.php');

$linkontrol = new linkontrol;
$alert = "info";
$userid = intval($userid);

if ($_GET['do'] == 'scrape') {
	if ($_REQUEST['url'] == '') {
		$msg = "url is missing";
		$alert = "error";
		return;
	}
	$scrape = new scrape(); 
        header("Content-type: text/json;charset=utf-8");
	echo($scrape->getUrl($url));
	die();
}
else if ($_GET['do'] == "logout") {
	$fgmembersite->LogOut();
	$msg = "Logged out";
	$username = "";
	$userid = 0;
}
else if ($_GET['do'] == "login") {
	if ($_REQUEST['username'] == '') {
		$msg = "Username cannot be empty";
		$alert = "error";
		return;
	}
	if ($_REQUEST['password'] == '') {
		$msg = "Password cannot be empty";
		$alert = "error";
		return;
	}
	if ($fgmembersite->Login()) {
		$username = $fgmembersite->Username();
		$userid = $fgmembersite->UserId();
		$msg = "Logged in";
		$alert = "info";
	}
	else {
		$msg = "Login failed";
		$alert = "error";
	}
}
else if ($_GET['do'] == "password_reminder") {
	if ($_REQUEST['email'] == '') {
		$msg = "Email cannot be empty";
		$alert = "error";
		return;
	}	
	if ($fgmembersite->EmailResetPasswordLink()) {
		$msg = "Link sent to your email";
		return;
	}
	else {
		$msg = $fgmembersite->GetErrorMessage();
		$alert = "error";
	}
}
else if ($_GET['do'] == "change_password") {
	if ($_REQUEST['oldpwd'] == '') {
		$msg = "Old password cannot be empty";
		$alert = "error";
		return;
	}
	if ($_REQUEST['newpwd'] == '') {
		$msg = "New Password cannot be empty";
		$alert = "error";
		return;
	}
	if ($fgmembersite->ChangePassword()) {
		$msg = "Password updated";
		$alert = "info";
	}
	else {
		$msg = $fgmembersite->GetErrorMessage();
		$alert = "error";
	}
}
else if ($_GET['do'] == "register") {
	if ($fgmembersite->RegisterUser()) {
		$msg = "Registered";
		return;
	}
	$msg = $fgmembersite->GetErrorMessage();
	$alert = "error";
}
else if ($_GET['do'] == 'add_time_feed') {
	if ($userid == 0) {
		$msg = "You need to be logged for this";
		$alert = "error";
		return;
	}
	$movieid = intval($_REQUEST['movieid']);
	$arr = $linkontrol->getMovie($movieid);
	if ($arr['userid'] != $userid) {
		$msg = "You can only add time feeds to your movie";
		$alert = "error";
		return;
	}
	$start = intval($_REQUEST['start']);
	$end = intval($_REQUEST['end']);
	$linktypeid = intval($_REQUEST['linktypeid']);
	$title = mysql_real_escape_string($_REQUEST['title']);
	$img = mysql_real_escape_string($_REQUEST['img']);
	$body = mysql_real_escape_string($_REQUEST['body']);
	$href = mysql_real_escape_string($_REQUEST['href']);
	$feedid = $linkontrol->addTimeFeed($movieid, $start, $end, $title, $img, $body, $href, $linktypeid);
	$msg = "added time feed $feedid";
}
else if ($_GET['do'] == 'update_time_feed') {
	$feedid = intval($_REQUEST['feedid']);
	$linktypeid = intval($_REQUEST['linktypeid']);
	$start = intval($_REQUEST['start']);
	$end = intval($_REQUEST['end']);
	$title = mysql_real_escape_string($_REQUEST['title']);
	$img = mysql_real_escape_string($_REQUEST['img']);
	$body = mysql_real_escape_string($_REQUEST['body']);
	$href = mysql_real_escape_string($_REQUEST['href']);
	if ($userid == 0) {
		$msg = "You need to be logged for this";
		$alert = "error";
		return;
	}
	$arr = $linkontrol->getTimeFeed($feedid);
	/*
	if ($arr['userid'] != $userid) {
		$msg = "You can only update your timefeeds";
		$alert = "error";
		return;
	}
	*/
	if ($feedid == 0) {
		$msg = "Feedid cannot be 0 or empty";
		$alert = "error";
		return;
	}
	/*
	if ($body == '') {
		$msg = "Body cannot be empty";
		$alert = "error";
		return;
	}
	*/
	$rows = $linkontrol->updateTimeFeed($feedid, $start, $end, $title, $img, $body, $href, $linktypeid);
	$msg = "time feed updated $rows row affected";
}
else if ($_GET['do'] == 'delete_time_feed') {
	if ($userid == 0) {
		$msg = "You need to be logged for this";
		$alert = "error";
		return;
	}
	$timefeedid = intval($_REQUEST['timefeedid']);

	$arr = $linkontrol->getTimeFeed($timefeedid);
	if ($arr['userid'] != $userid) {
		$msg = "You can only delete your timefeeds";
		$alert = "error";
		return;
	}

	$linkontrol->deleteTimeFeed($timefeedid);
	$msg = "deleted time feed $timefeedid";
}
else if ($_GET['do'] == 'add_movie') {
	$name = mysql_real_escape_string($_REQUEST['name']);
	$href = mysql_real_escape_string($_REQUEST['href']);
	$userid = intval($userid);

	if ($userid == 0) {
		$msg = "You need to be logged in to add a movie";
		$alert = "error";
		return;
	}

	if ($name == '') {
		$msg = "Name cannot be empty";
		$alert = "warning";
		return;
	}

	$movieid = $linkontrol->addMovie($userid, $name, $href);
	$msg = "added movie $movieid";
}
else if ($_GET['do'] == 'update_movie') {
	$movieid = intval($_REQUEST['movieid']);
	$name = mysql_real_escape_string($_REQUEST['name']);
	$href = mysql_real_escape_string($_REQUEST['href']);
	if ($userid == 0) {
		$msg = "You need to be logged in to update a movie";
		$alert = "error";
		return;
	}
	$arr = $linkontrol->getMovie($movieid);
	if ($arr['userid'] != $userid) {
		$msg = "You cannot edit other peoples movies";
		$alert = "error";
		return;
	}
	if ($movieid == 0) {
		$msg = "movieid cannot be 0";
		$alert = "warning";
		return;
	}
	if ($name == '') {
		$msg = "Name cannot be empty";
		$alert = "warning";
		return;
	}
	if ($href == '') {
		$msg = "href cannot be empty";
		$alert = "warning";
		return;
	}

	$rows = $linkontrol->updateMovie($movieid, $name, $href);
	if ($rows == 0) {
		$msg = "unable to find movie $movieid";
		$alert = "error";
		return;
	}
	$msg = "Movie was updated";
}
else if ($_GET['do'] == 'delete_movie') {
	if ($userid == 0) {
		$msg = "You need to be logged for this";
		$alert = "error";
		return;
	}
	$movieid = intval($_REQUEST['movieid']);
	$arr = $linkontrol->getMovie($movieid);
	if ($arr['userid'] != $userid) {
		$msg = "You cannot delete other peoples movies";
		$alert = "error";
		return;
	}

	$linkontrol->deleteMovie($movieid);
	$msg = "deleted movie $movieid";
}
else if ($_GET['do'] == 'create_session') {
	$movieid = intval($_REQUEST['movieid']);
	if ($movieid == 0) {
		$msg = "No movieid specified";
		$alert = "warning";
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
		$alert = "warning";
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
