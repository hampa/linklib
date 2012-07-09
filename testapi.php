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
<script>
        // ensure the web page (DOM) has loaded
        document.addEventListener("DOMContentLoaded", function () {

            timefeed('#feeddiv', {
                highlight: true
            });

            // Create a popcorn instance by calling the Youtube player plugin
            //popcorn = Popcorn("#video");
	    popcorn = Popcorn.youtube("#youtube", "http://www.youtube.com/watch?v=v-7kf7OZQtw");
            popcorn.volume(0);
<?php
$linkontrol = new linkontrol();
$arr = $linkontrol->getTimeFeed($movieid);
if (isset($arr)) {
	foreach ($arr as $key => $val) {
		echo($linkontrol->timeFeedToJson($val));
	}
}
?>
            // initial render
            //toggleOverlay(true, true);

            // play
            popcorn.play();
        }, false);

</script>
</head>
<body>
<div>
<?php
if ($msg != '') {
	echo("<div class=$msg_level>$msg</div>");
}
?>
<h1>get_movies</h1>
<table border=1>
<?php
$linkontrol = new linkontrol();
$arr = $linkontrol->getMovies();
if (isset($arr)) {
        foreach ($arr as $key => $val) {
		echo('<tr><td><a href="?movieid=' . $val['movieid'] . '">' . $val['name'] . "($val[movieid])</a></td></tr>\n");
        }
}
?>
</table>
<h1>add_movie</h1>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=add_movie" >
<table>
<tr><td>userid:</td>	<td><input name="userid" value="2101483"></td></tr>
<tr><td>name:</td>	<td><input size="60" name="body" value="Prometheus"></td></tr>
<tr><td>href:</td>	<td><input size="60" name="href" value="http://en.wikipedia.org/wiki/"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>
</form>
</div>

<h1>create_session</h1>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=create_session" >
<table>
<tr><td>movieid:</td>	<td><input name="movieid" value="1"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>
</form>

<h1>get_session</h1>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=get_session" >
<table>
<tr><td>sessionkey:</td>	<td><input name="sessionkey" value="abcd"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>
</form>

<h1>add_time_feed</h1>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=add_time_feed&movieid=<?php echo($movieid) ?>" >
<table>
<tr><td>movieid:</td>	<td><input name="movieid" value="1"></td></tr>
<tr><td>userid:</td>	<td><input name="userid" value="2101483"></td></tr>
<tr><td>start:</td>	<td><input name="start" value="10"></td></tr>
<tr><td>end:</td>	<td><input name="end" value="13"></td></tr>
<tr><td>title:</td>	<td><input name="title" value="Feed Title"></td></tr>
<tr><td>img:</td>	<td><input size="60" name="img" value="pp.png"></td></tr>
<tr><td>body:</td>	<td><input size="60" name="body" value="This is a feed title"></td></tr>
<tr><td>href:</td>	<td><input size="60" name="href" value="http://en.wikipedia.org/wiki/"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>
</form>

<h1>update_time_feed</h1>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=update_time_feed&movieid=<?php echo($movieid); ?>" >
<table>
<tr><td>feedid:</td>	<td><input name="feedid" value="<?php echo($timefeedid); ?>"></td></tr>
<tr><td>userid:</td>	<td><input name="userid" value="<?php echo($userid); ?>"></td></tr>
<tr><td>start:</td>	<td><input name="start" value="<?php echo($start); ?>"></td></tr>
<tr><td>end:</td>	<td><input name="end" value="<?php echo($end); ?>"></td></tr>
<tr><td>title:</td>	<td><input name="title" value="<?php echo($title); ?>"></td></tr>
<tr><td>img:</td>	<td><input size="60" name="img" value="<?php echo($img); ?>"></td></tr>
<tr><td>body:</td>	<td><input size="60" name="body" value="<?php echo($body); ?>"></td></tr>
<tr><td>href:</td>	<td><input size="60" name="href" value="<?php echo($href); ?>"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>
</form>

<h1>get_time_feeds</h1>
<table border=1>
<?php
$linkontrol = new linkontrol();
$arr = $linkontrol->getTimeFeeds($movieid);
if (isset($arr)) {
	foreach ($arr as $key => $val) {
		#print_r($val);
		echo("\t\t" . $linkontrol->timeFeedToHtml($val));
	}
}
?>
</table>

<h1>Style test</h2
<div class=info>This is an info message</div>
<div class=success>This is a success message</div>
<div class=warning>This is an warning message</div>
<div class=error>This is an error message</div>

<div class="container">
<!--
    	<video height=100 width=100 style="background:#000" id="video" preload="auto" autobuffer="" controls="" poster="http://videos.mozilla.org/serv/webmademovies/popcornposter.png">
        <source src="http://videos.mozilla.org/serv/webmademovies/wtfpopcorn.mp4" type="video/mp4">
        <source src="http://videos.mozilla.org/serv/webmademovies/wtfpopcorn.webm" type="video/webM">
        <source src="http://videos.mozilla.org/serv/webmademovies/wtfpopcorn.ogv" type="video/ogg">
        <p>Your user agent does not support the HTML5 Video element.</p>
    </video>
-->
    <!--<div id="viddeo">dum-->
    <!--</div>-->
    <div class="feed">
        <div class="searchdiv">
            <input type=text class="search" placeholder="Search">
        </div>
        <div id="feeddiv">
    </div>
</div>
<div id="youtube" style="width:600px;height:400px;"></div>
<div id="foo"></div>
</body>
</html>
