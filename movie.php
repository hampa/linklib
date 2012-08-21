<?php
include_once("linkontrol/functions_linkontrol.php");
$linkontrol = new linkontrol();
$movieid = intval($_GET['movieid']);
if ($movieid == 0) {
	include_once('movies.php');
	die();
}
$remote_code = mysql_real_escape_string($_GET['id']);
if ($remote_code == '') {
	$remote_code = $linkontrol->createSession($movieid);
}
else {
	$linkontrol->reuseSession($remote_code, $movieid);
}

$arr = $linkontrol->getMovie($movieid);
$movie_href = $arr['href'];
$is_youtube = false;
$is_vimeo = false;
if (stristr($movie_href, "http://youtu.be")) {
	$is_youtube = true;
}
else if (stristr($movie_href, "http://player.vimeo.com")) {
	$is_vimeo = true;
}
?>
<!doctype html>
<html>
<head>
<script type="text/javascript" src="//use.typekit.net/gtv1fsm.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<!-- <script src="lib/popcorn/popcorn.js"></script> -->
<script src="http://popcornjs.org/code/dist/popcorn-complete.js"></script>
<script src="timefeed.js"></script>
<script src="movie.js"></script>
<script src="util.js"></script>
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
var serverLocation = 'http://linkontrol.toribash.com:1337';	
var streamId = "someStreamId";
</script>
<script src="http://linkontrol.toribash.com/~hampa/tpbafk/example/node-server/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js"></script>
<link href="css/timefeed.css" rel="stylesheet"/>
<link href="css/movie.css" rel="stylesheet"/>
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
			console.log("onFirstItem");
        	};

<?php
		if ($is_youtube) {
			echo("popcorn = Popcorn.smart('#youtube', '$movie_href');\n");
		}
		else if ($is_vimeo) {
			echo("popcorn = Popcorn.smart('#video', '$movie_href');\n");
		}
		else {
			echo("popcorn = Popcorn('#video');\n");
		}
		//echo("//$movie_href\n");
		//echo("popcorn = Popcorn.smart('#video', '$movie_href');\n");
?>
		//console.log(popcorn); 

		// todo: debug why this doesn't fire
		$("#video").bind('ended', function () {
			tf.hideAll();
		});

		$('.tab').click(function () {
			console.log("tab click");
			toggleOverlay();
		});
        	$('.remotecode').click(function () {
			console.log("do something");
			toggleOverlay();
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

		popcorn.play();

		toggleOverlay();

	}, false);

	setInterval(function () {
		var data = {};
		if (!data.time) {
			//data.time = popcorn.video.currentTime;
			data.time = popcorn.currentTime();
		}
		//if (popcorn.video.paused) {
		if (popcorn.paused()) {
			emit("onPause", data);
		}
		else {
			emit("onPlay", data);
		}
	}, 700);

	var handlePlay = function (data) {
		console.log("handlePlay " + data);
		popcorn.play(data.time);
        	if (!data.time) {
            		data.time = popcorn.currentTime();
        	}
        	emit("onPlay", data);
    	};

	var handlePause = function (data) {
		console.log("handlePause");
		popcorn.pause(data.time);
        	if (!data.time) {
			data.time = popcorn.currentTime();
		}
		emit("onPause", data);
	};

	var handleRewind = function (data) {
		jumpTo(data, -5);
	}

	var handleForward = function(data) {
		jumpTo(data, 5);
	}

	var jumpTo = function(data, seconds) {
        	var newtime = popcorn.currentTime() + seconds;
		if (newtime < 0) {
			newtime = 0;
		}
		var duration = popcorn.duration();
		if (duration && duration < newtime) {
			newtime = duration;
		}	
		console.log("jumpTo" + newtime + " duration " + duration);
        	//popcorn.play(newtime);
        	popcorn.currentTime(newtime);
        	if (!data.time) {
            		data.time = popcorn.currentTime();
        	}
        	emit("onRewind", data);
    	}

	var handleShowOverlay = function (data) {
		toggleOverlay(true, false);
		emit("onShowOverlay", data);
	};

	var handleHideOverlay = function (data) {
		toggleOverlay(false, false);
		emit("onHideOverlay", data);
	};

	var handleHrefInPlayerOverlay = function (data) {
		handlePause(data);
		window.open(data.href);
	};

	function connect() {
		console.log("connecting");
		socket = io.connect(serverLocation);
		socket.emit("join", {'streamId':streamId});
		socket.on('play', handlePlay);
		socket.on('pause', handlePause);
		socket.on('rewind', handleRewind);
		socket.on('forward', handleForward);
		socket.on('showOverlay', handleShowOverlay);
		socket.on('hideOverlay', handleHideOverlay);
		socket.on('hrefInPlayer', handleHrefInPlayerOverlay)
	}

	function emit(command, data) {
        	if (!data) {
            		data = {};
        	}
		data.streamId = streamId;
		socket.emit(command, data);
	}
</script>
</head>
<body>
	<div class="container">
		<div id="videodiv" class="videodiv">
<?php 
	if ($is_youtube) {
		echo('<div id="youtube" style="padding-top:50px;width:640px;height:480px;"></div>' . "\n");
	}
	else if ($is_vimeo) {
		echo('<div id="video" style="padding-top:50px;width:640px;height:480px;"></div>' . "\n");
	}
	else {
		echo('<video style="background:#000" id="video" loop="" controls="">' . "\n");
		echo("<source src=\"$movie_href\" type=\"video/ogg\">\n");
		echo("<p>Your user agent does not support the HTML5 Video element.</p>\n");
		echo("</video>\n");
	}
?>
		</div>
		<div class="tab" style="padding-top: 6px; padding-left: 6px;">+</div>
		<div id="feed" class="feed">
			<div class="searchdiv">
				<input type=text class="search" placeholder="Search">
        		</div>
			<div id="feeddiv"> </div>
		</div>
	</div>
	<div class="remotecode"><img src="http://qr.kaywa.com/img.php?s=8&d=http%3A%2F%2Fwww.linkontrol.com%2Fremote.php%3Fid%3D<?php echo("$remote_code");?>" width="256" height="256" alt="QRCode"/> </div>
</body>
</html>
