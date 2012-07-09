<?php
include_once("linkontrol/functions_linkontrol.php");
$linkontrol = new linkontrol();
$movieid = intval($_GET['movieid']);
if ($movieid == 0) {
	include_once('movies.php');
	die();
}
$remote_code = $_GET['id'];
if ($remote_code == '') {
	$remote_code = $linkontrol->createSession($movieid);
}
?>
<!doctype html>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script src="lib/popcorn/popcorn.js"></script>
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
var serverLocation = 'http://linkontrol.toribash.com:1339';	
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

	setInterval(function () {
		var data = {};
		if (!data.time) {
			data.time = popcorn.video.currentTime;
		}
		if (popcorn.video.paused) {
			emit("onPause", data);
		}
		else {
			emit("onPlay", data);
		}
	}, 700);

	var handlePlay = function (data) {
		popcorn.play(data.time);
        	if (!data.time) {
            		data.time = popcorn.video.currentTime;
        	}
        	emit("onPlay", data);
    	};

	var handlePause = function (data) {
		popcorn.pause(data.time);
        	if (!data.time) {
			data.time = popcorn.video.currentTime;
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
        	var newtime = popcorn.video.currentTime + seconds;
		if (newtime < 0) {
			newtime = 0;
		}
		var duration = popcorn.video.duration;
		if (duration && duration < newtime) {
			newtime = duration;
		}	
		console.log("jumpTo" + newtime + " duration " + duration);
        	popcorn.play(newtime);
        	if (!data.time) {
            		data.time = popcorn.video.currentTime;
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
	<div class="remotecode">Remote Code: <?php echo("$remote_code");?></div>
	<div class="container">
		<div id="videodiv" class="videodiv">
        		<video style="background:#000" id="video" loop="" controls="">
				<!-- <source src="http://videos.mozilla.org/serv/webmademovies/wtfpopcorn.mp4" type="video/mp4">-->
				<!-- <source src="http://videos.mozilla.org/serv/webmademovies/wtfpopcorn.webm" type="video/webM">-->
				<source src="http://www.tpbafk.tv/augumentary/video/TPB_AFK_Demo720.theora.ogv" type="video/ogg">
				<!-- <source src="/augumentary/video/TPB_AFK_Demo720.theora.ogv" type="video/ogg"> -->
				<p>Your user agent does not support the HTML5 Video element.</p>
        		</video>
		<div class="tab" style="padding-top: 6px; padding-left: 6px;">+</div>
	</div>
	<div id="feed" class="feed">
		<div class="searchdiv">
			<input type=text class="search" placeholder="Search">
        	</div>
		<div id="feeddiv"> </div>
	</div>
</div>
</body>
</html>
