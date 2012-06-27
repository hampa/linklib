
 var streamId="someStreamId2";

 function emit(command, data)
 {
     data.streamId = streamId;
     socket.emit(command,data);
 }

 function sendPlay(time) {
     handleSendPlay({"time": time});
     emit("play", {"time": time})
 }

 function sendPause(time) {
     handleSendPause({"time": time});
     emit("pause", {"time": time})
 }

 function sendShowOverlay() {
       //  handlePause();
    handleSendShowOverlay();
    emit("showOverlay",{});
}

 function sendHideOverlay() {
     handleSendHideOverlay();
     emit("hideOverlay",{});
 }

 function sendHref(href) {
           //  handlePause();
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
