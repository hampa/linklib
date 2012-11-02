<?php
require_once('linkontrol/global.php');
require_once('linkontrol/access.php');
require_once("linkontrol/functions_linkontrol.php");

header('Content-type: application/json');
die(json_encode(
	array("msg" => $msg, "alert" => $alert, "error" => $error,
	"username" => $username, "userid" => $userid, "response" => $response_array, "movie" => $json_movie, 
	"movies" => $json_movies,
	"timefeed" => $json_feed, "sessionkey" => $sessionkey)));
?>
