
$( function () {
    var fullwidth = false;


//    $('#video').click( function () {
//        toggleOverlay();
//    });

    $('.feed').mouseenter( function (){
        handlePause({});
    });

    $('#video').mouseenter( function (){
        handlePlay({});
    });
});


function toggleOverlay( bool, now ){
    fullwidth = ! ( bool == null ? fullwidth : bool );
    if(!fullwidth)
        emit("onShowOverlay", {'streamId': streamId});
    else
        emit("onHideOverlay", {'streamId': streamId});


    var cw = $('.container').width();
    var tw = $('#feeddiv').width();
    var vw = fullwidth ? cw : cw - tw;

    if( now )
        $('#videodiv').width(vw);
    else
        $('#videodiv').animate({width:  vw}, 1000);


    $('.tab').text( fullwidth ? "+" : "-");

    $('.feed').stop().animate({
        'width': (fullwidth ? 0 : 300),
        'opacity': (fullwidth ? 0 : 1)
    }, 1000);
}
