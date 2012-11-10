<?php
require_once('linkontrol/global.php');
require_once('linkontrol/class_linkontrol.php');
require_once('linkontrol/class_scrape.php');

$linkontrol = new linkontrol;
$alert = "info";
$sessionkey = "";
$userid = intval($userid);
$response_array = array();
$json_movies = array();
$json_movie = array();
$json_feed = array();
$msg = "";
$error = 0;

if ($_GET['do'] == 'scrape') {
	if ($_REQUEST['url'] == '') {
		$msg = "url is missing";
		$alert = "error";
		$error = 1;
		return;
	}
	$url = $_REQUEST['url'];
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
		$error = 1;
		return;
	}
	if ($_REQUEST['password'] == '') {
		$msg = "Password cannot be empty";
		$alert = "error";
		$error = 1;
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
		//die($fgmembersite->GetErrorMessage());
		$alert = "error";
		$error = 1;
	}
}
else if ($_GET['do'] == "password_reminder") {
	if ($_REQUEST['email'] == '') {
		$msg = "Email cannot be empty";
		$alert = "error";
		$error = 1;
		return;
	}	
	if ($fgmembersite->EmailResetPasswordLink()) {
		$msg = "Link sent to your email";
		return;
	}
	else {
		$msg = $fgmembersite->GetErrorMessage();
		$alert = "error";
		$error = 1;
	}
}
else if ($_GET['do'] == "change_password") {
	if ($_REQUEST['oldpwd'] == '') {
		$msg = "Old password cannot be empty";
		$alert = "error";
		$error = 1;
		return;
	}
	if ($_REQUEST['newpwd'] == '') {
		$msg = "New Password cannot be empty";
		$alert = "error";
		$error = 1;
		return;
	}
	if ($fgmembersite->ChangePassword()) {
		$msg = "Password updated";
		$alert = "info";
	}
	else {
		$msg = $fgmembersite->GetErrorMessage();
		$alert = "error";
		$error = 1;
	}
}
else if ($_GET['do'] == "register") {
	if ($fgmembersite->RegisterUser()) {
		$msg = "Registered";
		return;
	}
	$msg = $fgmembersite->GetErrorMessage();
	$alert = "error";
	$error = 1;
}
else if ($_GET['do'] == 'add_time_feed') {
	if ($userid == 0) {
		$msg = "You need to be logged for this";
		$alert = "error";
		$error = 1;
		return;
	}
	$movieid = intval($_REQUEST['movieid']);
	$arr = $linkontrol->getMovie($movieid);
	if ($arr['userid'] != $userid) {
		$msg = "You can only add time feeds to your movie";
		$alert = "error";
		$error = 1;
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
	$arr = $linkontrol->getTimeFeed($feedid);
	if (isset($arr)) {
		$json_feed = $linkontrol->timeFeedToArray($arr);
	}	
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
		$error = 1;
		return;
	}
	$arr = $linkontrol->getTimeFeed($feedid);
	if ($arr['userid'] != $userid) {
		$msg = "You can only update your timefeeds";
		$alert = "error";
		$error = 1;
		return;
	}
	if ($feedid == 0) {
		$msg = "Feedid cannot be 0 or empty";
		$alert = "error";
		$error = 1;
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
	$arr = $linkontrol->getTimeFeed($feedid);
	if (isset($arr)) {
		$json_feed = $linkontrol->timeFeedToArray($arr);
	}
}
else if ($_GET['do'] == 'delete_time_feed') {
	if ($userid == 0) {
		$msg = "You need to be logged for this";
		$alert = "error";
		$error = 1;
		return;
	}
	$timefeedid = intval($_REQUEST['timefeedid']);

	$arr = $linkontrol->getTimeFeed($timefeedid);
	if ($arr['userid'] != $userid) {
		$msg = "You can only delete your timefeeds";
		$alert = "error";
		$error = 1;
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
		$error = 1;
		return;
	}

	if ($name == '') {
		$msg = "Name cannot be empty";
		$alert = "warning";
		$error = 2;
		return;
	}

	/*
	if (!$linkontrol->isMovieLink($href)) {
		$msg = "Not a movie link";
		$alert = "error";
		$error = 3;
		return;
	}
	*/
	$thumbnail = $linkontrol->urlToThumbnail($href);

	$movieid = $linkontrol->addMovie($userid, $name, $href, $thumbnail);
	$arr = $linkontrol->getMovie($movieid);
	$json_movie = array("name" => $arr['name'], "movieid" => $arr['movieid'], "url" => $arr['href'], "thumbnail" => $arr['thumbnail']);
	$msg = "added movie $movieid";
}
else if ($_GET['do'] == 'update_movie') {
	$movieid = intval($_REQUEST['movieid']);
	$name = mysql_real_escape_string($_REQUEST['name']);
	$href = mysql_real_escape_string($_REQUEST['href']);
	if ($userid == 0) {
		$msg = "You need to be logged in to update a movie";
		$alert = "error";
		$error = 1;
		return;
	}
	$arr = $linkontrol->getMovie($movieid);
	if ($arr['userid'] != $userid) {
		$msg = "You cannot edit other peoples movies";
		$alert = "error";
		$error = 1;
		return;
	}
	if ($movieid == 0) {
		$msg = "movieid cannot be 0";
		$alert = "warning";
		$error = 1;
		return;
	}
	if ($name == '') {
		$msg = "Name cannot be empty";
		$alert = "warning";
		$error = 1;
		return;
	}
	if ($href == '') {
		$msg = "href cannot be empty";
		$alert = "warning";
		$error = 1;
		return;
	}

	$rows = $linkontrol->updateMovie($movieid, $name, $href);
	if ($rows == 0) {
		$msg = "unable to find movie $movieid";
		$alert = "error";
		$error = 1;
		return;
	}
	$msg = "Movie was updated";
}
else if ($_GET['do'] == 'delete_movie') {
	if ($userid == 0) {
		$msg = "You need to be logged for this";
		$alert = "error";
		$error = 1;
		return;
	}
	$movieid = intval($_REQUEST['movieid']);
	$arr = $linkontrol->getMovie($movieid);
	if ($arr['userid'] != $userid) {
		$msg = "You cannot delete other peoples movies";
		$alert = "error";
		$error = 1;
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
else if ($_GET['do'] == 'time_feed_to_json') {
	$movieid = intval($_GET['movieid']);
        $arr = $linkontrol->getTimeFeeds($movieid);
        if (isset($arr)) {
		$json = array();
                foreach ($arr as $key => $val) {
			$json[] = $linkontrol->timeFeedToArray($val);
                }
		die(json_encode($json));
        }
}
else if ($_GET['do'] == 'create_time_feed') {
	$movieid = intval($_REQUEST['movieid']);
	$start = intval($_REQUEST['start']);
	$text = mysql_real_escape_string($_REQUEST['text']);
	$arr = $linkontrol->getMovie($movieid);
	if (!isset($arr)) {
		$msg = "could not find movie";
		$alert = "error";
		$error = 1;
		return;
	}
	if ($text == "") {
		$msg = "text cannot be empty";
		$alert = "error";
		$error = 1;
		return;
	}
	$images = array();
	$images[0] = "ll.png";
	$img = "ll.png";
	$host = "";
	$url = "";
	$linktype = 0;
	$title = "";
	if (preg_match('/^http.*/', $text) && preg_match('@^(?:http://)?([^/]+)(.*)@i', $text, $matches)) {
		//print_r($matches);
		$host = $matches[1];
		if (preg_match('/[^.]+\.[^.]+$/', $host, $matches)) {
			//print_r($matches);
			$x = explode(".", $matches[0]);
			$host = $x[0];
		}
		$url = $text;
		if ($linkontrol->isMovieLink($host)) {
			$linktype = linkontrol::VIDEO;
		}
		else {
			$linktype = linkontrol::WEBPAGE;
		}

		$scrape = new scrape();
		$arr = $scrape->getUrl($text);
		$title = $arr['title'];
		// content is too massive
		//$body = $arr['content'];
	}	
	else {
		$title = $text;
		$linktype = linkontrol::TEXT; 
	}
	$found = false;
	if ($handle = opendir('Icons')) {
    		/* This is the correct way to loop over the directory. */
		while (false !== ($entry = readdir($handle))) {
			$x = explode(".", $entry);
			$match = $x[0];
			if ($match == $host) {
				$images[1] = $entry;
				$img = $entry;
				$found = true;
				break;
			}
		}
		closedir($handle);
	}

	if ($found == false) {
		$icon = $linkontrol->fetchTouchIcon($url);
		if ($icon != "") {
			$images[1] = $icon;
			$img = $icon;
		}
	}

	$feedid = $linkontrol->addTimeFeed($movieid, $start, 0, $title, $img, $body, $url, $linktype);
	$arr = $linkontrol->getTimeFeed($feedid);
	if (isset($arr)) {
		$json_feed = $linkontrol->timeFeedToArray($arr);
	}
	$response_array = array("images" => $images);
	//print_r($response_array);
	//print_r($json_feed);
	//die();
}
else if ($_GET['do'] == 'movie_to_json') {
	$sessionkey = mysql_real_escape_string($_REQUEST['sessionkey']);
	if ($sessionkey != "") {
		$arr = $linkontrol->getSession($sessionkey);
		$movieid = intval($arr['movieid']);
	}
	else {
		$movieid = intval($_GET['movieid']);
	}
	//$sessionid = mysql_real_escape_string($_GET['sessionid']);
	$sessionid = intval($userid); //mysql_real_escape_string($_GET['sessionid']);
	/*
	if ($sessionid == '') {
		$sessionid = $linkontrol->createSession($movieid);
	}
	else {
		$linkontrol->reuseSession($sessionid, $movieid);
	}
	*/
	$linkontrol->reuseSession($sessionid, $movieid);

	$arr = $linkontrol->getMovie($movieid);
	//print_r($arr);
	$json_movie = array("name" => $arr['name'], "movieid" => $arr['movieid'], "url" => $arr['href'], "sessionid" => $sessionid);
	$json_feed = array();
        $arr = $linkontrol->getTimeFeeds($movieid, $_REQUEST['sort']);
        if (isset($arr)) {
                foreach ($arr as $key => $val) {
			$json_feed[] = $linkontrol->timeFeedToArray($val);
                }
        }
	$response_array = array("movie" => $json_movie, "timefeed" => $json_feed);
	//die(json_encode($json));
}
else if ($_GET['do'] == 'get_user_movies') {
	$u = intval($_REQUEST['userid']);
	if ($u == 0) {
		$u = $userid;
	}
	$arr = $linkontrol->getUserMovies($u);
	$json_movies = array();
        if (isset($arr)) {
                foreach ($arr as $key => $val) {
			$json_movies[] = $linkontrol->movieToArray($val);
                }
	}
	//die(json_encode($response_array));
}
?>
