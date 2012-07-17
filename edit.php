<?php
include_once("linkontrol/functions_linkontrol.php");
$linkontrol = new linkontrol;
$movieid = intval($_GET['movieid']);
$arr = $linkontrol->getMovie($movieid);
$moviename = $arr['name'];
$movieurl = $arr['href'];

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
$remote_code = $_GET['id'];
if ($remote_code == '') {
	$remote_code = $linkontrol->createSession($movieid);
}
?>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="http://popcornjs.org/code/dist/popcorn-complete.js"></script>
<script src="timefeed.js"></script>
<script src="movie.js"></script>
<script src="util.js"></script>
<script src="player.js"></script>
<link href="css/editfeed.css" rel="stylesheet" />
<link href="css/edit.css" rel="stylesheet" />
<link href="css/apistyle.css" rel="stylesheet" />
<link href="css/nav.css" rel="stylesheet" />
<script type="text/javascript">

$(document).ready(function() {
	params = getParams();
        if (params && params["id"]) {
                streamId = "someStreamId" + params["id"];
        }
	<?php echo("streamId = '$remote_code';\n") ?>
	console.log("documentReady remote_code = " + streamId);
        connect();
});
       //var serverLocation = 'http://empty-stone-2701.herokuapp.com';
var serverLocation = 'http://linkontrol.toribash.com:1339';	
var streamId = "someStreamId";
</script>
<script src="http://linkontrol.toribash.com/~hampa/tpbafk/example/node-server/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js"></script>
<script type="text/javascript">
	var popcorn, feed;

	// ensure the web page (DOM) has loaded
	document.addEventListener("DOMContentLoaded", function () {

		var tf = timefeed('#feeddiv', {
			highlight:false
        	});

		tf.onFirstItem = function () {
			$('.tab').show();
			$('.remotecode').show();
        	};

		popcorn = Popcorn("#video");

		// todo: debug why this doesn't fire
		$("#video").bind('ended', function () {
			tf.hideAll();
		});

		$('.tab').click(function () {
			toggleOverlay();
		});
        	$('.remotecode').click(function () {
			console.log("do something");
		});
<?php
		$linkontrol = new linkontrol();
		$arr = $linkontrol->getTimeFeeds($movieid);
		if (isset($arr)) {
        		foreach ($arr as $key => $val) {
                		echo($linkontrol->timeFeedToJson($val));
        		}
		}
?>
        	// initial render
        	handleHideOverlay({'streamId':streamId});

        	// play
		handlePlay({'streamId':streamId});
	}, false);
</script>
</head>
<body>
<?php echo($linkontrol->getNavigationMenu()); ?>

<!-- <div class="remotecode">Remote Code: <?php echo("$remote_code");?></div> -->
<!--
<div class="container">
	<div id="videodiv" class="videodiv">
       		<video style="background:#000" id="video" loop="" controls="">
			<source src="http://www.tpbafk.tv/augumentary/video/TPB_AFK_Demo720.theora.ogv" type="video/ogg">
			<p>Your user agent does not support the HTML5 Video element.</p>
		</video>
		<div class="tab" style="padding-top: 6px; padding-left: 6px;">+</div>
	</div>
	<div id="feed" class="feed">
		<div class="searchdiv"><input type=text class="search" placeholder="Search"></div>
		<div id="feeddiv"> </div>
	</div>
</div>
<div>
-->
<?php
if ($msg != '') {
	echo("<div class=$msg_level>$msg</div>");
}
?>
<p>Movie: <?php echo($moviename); ?> </p>
<p>Url: <?php echo($movieurl); ?> </p>
<table>
<tr><td>Start (sec)</td><td>body</td><td>img</td><td>href</td><td></td><td></td></tr>
<form method=post action="?do=add_time_feed&movieid=<?php echo($movieid) ?>" >
<input type=hidden name=movieid value=<?php echo($movieid) ?>
<input type=hidden name=userid value="123">
<tr bgcolor="#AAAAAA">
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
