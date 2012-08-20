var isPlaying = false;
var streamId = "someStreamId2";

function emit(command, data) {
	data.streamId = streamId;
	socket.emit(command,data);
}

function sendPlay(time) {
	console.log("sendPlay time:" + time);
	handleSendPlay({"time": time});
	emit("play", {"time": time})
}

function togglePlay() {
	console.log("togglePlay");
	if (isPlaying) {
		sendPause();
	}
	else {
		sendPlay();
	}
}

function sendPause(time) {
	console.log("sendPause");
	handleSendPause({"time": time});
	emit("pause", {"time": time})
}

function sendRewind(time) {
	console.log("sendRewind");
	handleSendRewind({"time": time});
	emit("rewind", {"time": time})
}

function sendForward(time) {
	console.log("sendForward");
	handleSendForward({"time": time});
	emit("forward", {"time": time})
}

function sendShowOverlay() {
	handleSendShowOverlay();
	emit("showOverlay",{});
}

function sendHideOverlay() {
	handleSendHideOverlay();
	emit("hideOverlay",{});
}

function sendHref(href) {
	emit("hrefInPlayer",{'href':href});
}

function connect() {
	console.log("connecting to " + serverLocation + " using streamId " + streamId);
	socket = io.connect(serverLocation);
	socket.emit("join", {'streamId': streamId});
	socket.on('onPlay', handleOnPlay);
	socket.on('onPause', handleOnPause);
	socket.on('onShowOverlay', handleOnShowOverlay);
	socket.on('onHideOverlay', handleOnHideOverlay);
}

var handleFeed = function(data) {
	var item = document.getElementById('linkfeed');
	var items = item.getElementsByTagName('li');
	for (var i = 0; i < items.length; i++) {
		var item = items[i];
		var start =  item.getAttribute("start");
		if ((data.time) >= start) {
			$(item).slideDown("slow");
		}
		else {
			item.style.display = 'none';
		}
	}
}

var handleSendPlay = function(data) {
	//   document.getElementById('playerstatus').innerHTML = 'sending ';
};

var handleSendRewind = function(data) {
	// do nothing for now
};

var handleSendForward = function(data) {
	// do nothing for now
};

var handleOnPlay = function(data) {
	//console.log("handleOnPlay");
	//document.getElementById('play').style.display ="none";
	//document.getElementById('pause').style.display = "inline";
	enablePauseButton();
	handleFeed(data);
	isPlaying = true;
	//   document.getElementById('playerstatus').innerHTML = 'playing ' +  data.time;
};

var handleSendPause = function(data) {
	//   document.getElementById('playerstatus').innerHTML = 'sending ';
};

var handleOnPause = function(data) {
	//console.log("handleOnPause");
	//document.getElementById('play').style.display ="inline";
	//document.getElementById('pause').style.display ="none";

	enablePlayButton();
	handleFeed(data);
	isPlaying = false;
	// document.getElementById('playerstatus').innerHTML = 'paused ' +  data.time;
};

var handleOnShowOverlay = function(data) {
	//document.getElementById('list2').style.display ="inline";
	//document.getElementById('list').style.display ="none";
	//   document.getElementById('overlaystatus').innerHTML = 'visible';
};

var handleOnHideOverlay = function(data) {
	//document.getElementById('list2').style.display ="none";
	//document.getElementById('list').style.display ="inline";
};

var handleSendShowOverlay = function(data) {
	//   document.getElementById('overlaystatus').innerHTML = 'sending';
};

var handleSendHideOverlay = function(data) {
	//   document.getElementById('overlaystatus').innerHTML = 'sending';
};

var enablePauseButton = function() {
	//$("#play .ui-btn-text").text("Pause");
	$('#play').prev('span').find('span.ui-btn-text').text("Pause");
};

var enablePlayButton = function() {
	//$("#play .ui-btn-text").text("Play");
	// wtf
	$('#play').prev('span').find('span.ui-btn-text').text("Play");
};


