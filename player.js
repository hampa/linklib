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

var handleGetTime = function(data) {
	console.log("handleGetTime sending");
	data.time = popcorn.video.currentTime;
	emit("time", data);
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

var handleGetTime = function (data) {
	data.time = popcorn.video.currentTime;
	emit("time", data);
}

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
	socket.on('hrefInPlayer', handleHrefInPlayerOverlay);
	console.log("setting hook handleGetTime");
	socket.on('getTime', handleGetTime);
}

function emit(command, data) {
	if (!data) {
		data = {};
	}
	data.streamId = streamId;
	socket.emit(command, data);
}
