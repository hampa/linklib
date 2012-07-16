<?php
include_once("linkontrol/functions_linkontrol.php");
$linkontrol = new linkontrol;
$movieid = intval($_GET['movieid']);
$timefeedid = intval($_GET['timefeedid']);
if ($timefeedid > 0) {
	$arr = $linkontrol->getTimeFeed($timefeedid);
	if (isset($arr)) {
		$userid = $arr['userid'];
		$start = $arr['start'];
		$end = $arr['end'];
		$img = $arr['img'];
		$href = $arr['href'];
		$body = $arr['body'];
		$title = $arr['title'];
	}
	#print_r($arr);
	#die("buy");
}
?>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="http://popcornjs.org/code/dist/popcorn-complete.js"></script>
<script src="timefeed.js"></script>
<link href="css/timefeed.css" rel="stylesheet" />
<link href="css/apistyle.css" rel="stylesheet" />
</head>
<body>
<div>
<?php
if ($msg != '') {
	echo("<div class=$msg_level>$msg</div>");
}
?>
<h1>Feeds</h1>
<table>
<tr><td>Start (sec)</td><td>body</td><td>img</td><td>href</td><td></td><td></td></tr>
<form method=post action="?do=add_time_feed&movieid=<?php echo($movieid) ?>" >
<input type=hidden name=movieid value=<?php echo($movieid) ?>
<input type=hidden name=userid value="123">
<tr bgcolor="0xCCCCCC">
<td><input size=4 name=start value="10"></td>
<td><input size=15 name=body value="This is a feed title"></td>
<td><input size=10 name=img value="pp.png"></td>
<td><input size=25 name=href value="http://en.wikipedia.org/wiki/"></td>
<td colspan=2><input type=submit value="Add Time Feed"></td>
</form>

<?php
$linkontrol = new linkontrol();
$arr = $linkontrol->getTimeFeeds($movieid);
if (isset($arr)) {
	
	foreach ($arr as $key => $val) {
		#print_r($val);
		echo("\t\t" . $linkontrol->timeFeedToHtmlForm($val));
	}
}
?>
</table>
</body>
</html>
