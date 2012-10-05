<?php
require_once('linkontrol/global.php');
require_once('linkontrol/access.php');
require_once("linkontrol/functions_linkontrol.php");

header('Content-type: application/json');
//$username = "hej";
//$userid = 666;
die(json_encode(array("msg" => $msg, "alert" => $alert, "username" => $username, "userid" => $userid, "response" => $response_array, "movie" => $json_movie, "timefeed" => $json_feed, "sessionkey" => $sessionkey)));
?>
