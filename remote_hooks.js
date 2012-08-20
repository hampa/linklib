                var handleFeed = function(data) {
                        var item = document.getElementById('linkfeed');
                        var items = item.getElementsByTagName('li');
                        for (var i = 0; i < items.length; i++) {
                                var item = items[i];
				var start =  item.getAttribute("start");
				if ((data.time) >= start) {
					//$(item).fadeIn();
					$(item).slideDown("slow");
				}
				else {
					item.style.display = 'none';
				}
                        }
                }

var handleSendPlay = function(data){
 //   document.getElementById('playerstatus').innerHTML = 'sending ';
};

var handleSendRewind = function(data) {
	// do nothing for now
};

var handleSendForward = function(data) {
	// do nothing for now
};

var handleOnPlay = function(data){
	//console.log("handleOnPlay");
	//document.getElementById('play').style.display ="none";
	//document.getElementById('pause').style.display = "inline";
	handleFeed(data);
  //   document.getElementById('playerstatus').innerHTML = 'playing ' +  data.time;
};

var handleSendPause= function(data){
  //   document.getElementById('playerstatus').innerHTML = 'sending ';
};

var handleOnPause = function(data){
	console.log("handleOnPause");
    //document.getElementById('play').style.display ="inline";
    //document.getElementById('pause').style.display ="none";

    handleFeed(data);
   // document.getElementById('playerstatus').innerHTML = 'paused ' +  data.time;
};

var handleOnShowOverlay = function(data){
    document.getElementById('list2').style.display ="inline";
    document.getElementById('list').style.display ="none";
     //   document.getElementById('overlaystatus').innerHTML = 'visible';
};

var handleOnHideOverlay = function(data){


    document.getElementById('list2').style.display ="none";
    document.getElementById('list').style.display ="inline";
};

var handleSendShowOverlay = function(data){
     //   document.getElementById('overlaystatus').innerHTML = 'sending';
};

var handleSendHideOverlay = function(data){
     //   document.getElementById('overlaystatus').innerHTML = 'sending';
};
