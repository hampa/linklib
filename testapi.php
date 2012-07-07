<?php
include_once("linkontrol/functions_linkontrol.php");
$movieid = intval($_GET['movieid']);
?>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="http://popcornjs.org/code/dist/popcorn-complete.js"></script>
<script src="tpbafk/timefeed.js"></script>
<link href="tpbafk/timefeed.css" rel="stylesheet" />
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
<h3>get_movies</h3>
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
<?php
if ($msg != '') {
	echo("<h2>$msg</h2>");
}
?>

<h3>add_movie</h3>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=add_movie" >
<table>
<tr><td>userid:</td>	<td><input name="userid" value="2101483"></td></tr>
<tr><td>name:</td>	<td><input size="60" name="body" value="Prometheus"></td></tr>
<tr><td>href:</td>	<td><input size="60" name="href" value="http://en.wikipedia.org/wiki/"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>
</form>
</div>

<h3>create_session</h3>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=create_session" >
<table>
<tr><td>movieid:</td>	<td><input name="movieid" value="1"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>
</form>

<h3>get_session</h3>
<form method=post action="http://linkontrol.toribash.com/~hampa/testapi.php?do=get_session" >
<table>
<tr><td>sessionkey:</td>	<td><input name="sessionkey" value="abcd"></td></tr>
<tr><td></td><td><input type=submit value="Submit"></td></tr>
</table>

<h3>add_time_feed</h3>
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

<h3>get_time_feed</h3>
<table border=1>
<?php
$linkontrol = new linkontrol();
$arr = $linkontrol->getTimeFeed($movieid);
if (isset($arr)) {
	foreach ($arr as $key => $val) {
		#print_r($val);
		echo("\t\t" . $linkontrol->timeFeedToHtml($val));
	}
}
?>
</table>
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
