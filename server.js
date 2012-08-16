var app = require('http').createServer(handler)
    , io = require('socket.io').listen(app)
    , fs = require('fs');

app.listen(1337);
console.log('listening on port 1337');

function handler(req, res) {
    fs.readFile(__dirname + '/index.html',
        function (err, data) {
            if (err) {
                res.writeHead(500);
                return res.end('Error loading ' + __dirname + '/index.html');
            }

            res.writeHead(200);
            res.end(data);
        });
}

Array.prototype.removeByValue = function(val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) {
            this.splice(i, 1);
            break;
        }
    }
}

function handleCommand(socket, command, data)
{
	console.log('handleCommand' + command + 'data ' + data);
	socket.broadcast.to(data.streamId).emit(command, data);
	//io.sockets.in(data.streamId).emit(command, data);
}

io.sockets.on('connection', function (socket) {
    console.log('connection');
    socket.on("join", function(data){
	console.log('join');
       socket.join(data.streamId);
    });

    socket.on("play", function(data) {
	console.log('play');
       handleCommand(socket, "play", data);
    });

    socket.on("onPlay", function(data) {
       handleCommand(socket, "onPlay", data);
    });

    socket.on("pause", function(data) {
        handleCommand(socket, "pause", data);
    });

    socket.on("rewind", function(data) {
        handleCommand(socket, "rewind", data);
    });

    socket.on("forward", function(data) {
        handleCommand(socket, "forward", data);
    });

    socket.on("onPause", function(data) {
           handleCommand(socket, "onPause", data);
        });

    socket.on("showOverlay", function(data) {
            handleCommand(socket, "showOverlay", data);
        });


    socket.on("onShowOverlay", function(data) {
            handleCommand(socket, "onShowOverlay", data);
        });

    socket.on("hideOverlay", function(data) {
        handleCommand(socket, "hideOverlay", data);
    });

    socket.on("onHideOverlay", function(data) {
            handleCommand(socket, "onHideOverlay", data);
        });

    socket.on("hrefInPlayer", function(data) {
            handleCommand(socket, "hrefInPlayer", data);
        });



    socket.on('disconnect', function(data) {
        console.log('disconnect', data);

    });
});
