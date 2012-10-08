var isPlaying = false;
var streamId = "someStreamId2";

function emit(command, data) {
	console.log("emit");
	data.streamId = streamId;
	socket.emit(command,data);
}

function connect() {
	console.log("connecting to " + serverLocation + " using streamId " + streamId);
	socket = io.connect(serverLocation);
	socket.emit("join", {'streamId': streamId});
	socket.emit("getTime", {'emptystuff': streamId});
	socket.on('onPlay', handleOnPlay);
	socket.on('onPause', handleOnPause);
	socket.on('time', handleTime);
}

var getTime = function() {
	socket.emit("getTime", {'emptystuff': streamId});
}

var handleFeed = function(currenttime) {
	//console.log("hanleFeed currenttime " + currenttime);
	var data = timeFeed;
	var topItem = null;
	$.each(data.timefeed, function(i, item) {
		var x = document.getElementById('item' + item.feedid);
		//console.log(i + " " + item.feedid + " ct: " + currenttime + " start: " + item.start +  " " + x);
		if (x == null) {
			console.log("cant find " + item.feedid);
		}
		else if (currenttime >= item.start) {
			//console.log("showing");
			//console.log(i + " " + item.feedid + " " + item.start);
			if (x.style.display == 'none') {
				$(x).fadeIn("fast");
				topItem = item;
				//x.style.display = '';
			}
		}
		else {
			//console.log("hiding");
			x.style.display = 'none';
		}
	});
	if (topItem && autosurf) {
		console.log("topItem" + topItem.title);
		if (topItem.url) {
			
			openUrl(topItem.url);
		}
	}
}

var handleTime = function(data) {
	console.log("handleTime");
	handleFeed(data.time);
}

var handleOnPlay = function(data) {
	console.log("handleOnPlay");
	handleFeed(data.time);
	isPlaying = true;
};

var handleOnPause = function(data) {
	if (isPlaying == false) {
		return;
	}
	console.log("handleOnPause");
	handleFeed(data.time);
	isPlaying = false;
};
