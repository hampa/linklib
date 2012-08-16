var streamId="someStreamId2";

function emit(command, data) {
	data.streamId = streamId;
	socket.emit(command,data);
}

function sendPlay(time) {
	console.log("sendPlay time:" + time);
	handleSendPlay({"time": time});
	emit("play", {"time": time})
}

function sendPause(time) {
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
