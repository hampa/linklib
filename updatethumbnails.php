<?php
require_once('linkontrol/global.php');
require_once("linkontrol/class_linkontrol.php");

$l = new linkontrol;
$arr = $l->getMovies();
if (isset($arr)) {
	foreach ($arr as $key => $val) {
		$url = $val['href'];
		$movieid = $val['movieid'];
		$thumbnail = $l->urlToThumbnail($url);
		$l->updateMovieThumbnail($movieid, $thumbnail);
		echo($movieid . " " . $thumbnail. "\n");
	}
}
?>
