<?php
include_once("linkontrol/functions_linkontrol.php");
$linkontrol = new linkontrol();
$sessionkey = mysql_real_escape_string($_GET['id']);
if ($sessionkey == '') {
	include_once('remote_start.php');
	die();
}
$arr = $linkontrol->getSession($sessionkey);
$movieid = 0;
$movie_name = "No movie specified";
if (isset($arr)) {
	$movieid = $arr['movieid'];
	$movie_name = $arr['moviename'];
}
?>
<html>
<head>
	<title><?php echo("$movie_name");?></title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon" href="apple-touch-icon.png" type="image/png"/>
	<link rel="apple-touch-startup-image" href="apple-touch-startup-image.png" type="image/png"/>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width"/>
	<link rel="stylesheet" href="css/style.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script src="util.js"></script>
	<script type="application/x-javascript"> 
        	var streamId = "someStreamIdFirst";
		var serverLocation = 'http://23.20.12.188:1339';	 // works
		addEventListener("load", 
			function() { 
				setTimeout(hideURLbar, 0); 
			}, false); 
			function hideURLbar() { 
				window.scrollTo(0,1);
			} 
			$(document).ready(function() {
				params = getParams();
				if (params && params["id"]) {
					streamId = params["id"];
				}
				<?php echo("streamId = \"$sessionkey\";\n"); ?>
				connect();
		});

		var serverLocation = 'http://23.20.12.188:1339';	 // works
		console.log("streamId" + streamId);
    	</script>
    	<script src="http://linkontrol.toribash.com/~hampa/tpbafk/example/node-server/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js"></script> 
	<script src="hooks.js"></script>
	<script src="core.js"></script>
</head>
<body id="body">
<div id="listener">
<div id="header">
	<h1><?php echo("$movie_name"); ?></h1>
</div>

<div id="time">
	<img class="time" src="images/time.png" />
</div>

<div class="feed" id="feed">
</div>

<div id="timelapse">00:00:00</div>
<div id="bottomshadow">
</div>
<div id="footer">
	<div id="filmnav">
        <a href="#" id="reverse" onclick="sendRewind()"><img src="images/rewind.png"></a>
        <a href="#" id="play"  onclick="sendPlay()" ><img src="images/play.png"></a>
        <a href="#" id="pause" onclick="sendPause()" style="display: none" ><img src="images/pause.png"></a>
        <a href="#" id="forward" onclick="sendForward()"><img src="images/forward.png"></a>
	</div>

	<a href="#" id="list" onclick="sendShowOverlay()"  ><img src="images/links.png"></a>
	<a href="#" id="list2" style="display: none" onclick="sendHideOverlay()"  ><img src="images/links_in.png"></a>

<!--    <a href="#" id="list2" onclick="sendHideOverlay()" style="display: none"><img src="images/links.png"></a>
-->
</div>

<div id="template" style="display: none">
<!--
<div class="item" start="$start"  style="display:none" >
	<div class="title" ></div>
	<div class="time">
		<span class="start">$start</span>
	</div>
	<img class="ico" src="$image" onclick="sendPause('$start')" />
	<div class="body">$body</div>
	<a class="link" target="_blank" href="$href">   $linktext</a>
</div>
-->
</div>

<script type="text/javascript">
    var popcorn = {
        timefeed: function(data) {
            var templateW = document.getElementById('template').innerHTML;
            var template = templateW.substring(4, templateW.length-4);
            template = template.replace(/\$body/g, data.body );
            template = template.replace(/\$image/g, data.img );
            template = template.replace(/\$href/g, data.href );
            template = template.replace(/\$start/g, data.start );
            template = template.replace(/\$linktext/g, data.href );

            document.getElementById('feed').innerHTML = template + document.getElementById('feed').innerHTML;
        }
    };
<?php
$linkontrol = new linkontrol();
$arr = $linkontrol->getTimeFeeds($movieid);
if (isset($arr)) {
	foreach ($arr as $key => $val) {
		echo($linkontrol->timeFeedToJson($val));
	}
}
?>

    sendPlay();
</script>
</div>

</body>
</html>
