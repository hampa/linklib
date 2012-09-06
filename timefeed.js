
(function () {
    var $ = jQuery;

    var defaults = {
        hightlight: true
    };

    var TimeFeed = function ( target, options ) {

        if( TimeFeed.instances[target] )
            return TimeFeed.instances[target];

        if( !( this instanceof TimeFeed))
            return new TimeFeed(target, options)

        this.config = $.extend({}, defaults, options);
        this.items = [];
        this.el = target;
        this.options = options;
        this.visible = true;

        TimeFeed.instances[target] = this;
    };

    TimeFeed.instances = {};

    TimeFeed.getFav = function  ( url ) {
        var matches = url.match(/(https?:\/\/[^\/]*)/);
        return matches[1] + "/favicon.ico";
    };

    TimeFeed.prototype =  {
        add : function (options) {
            var el = $("<div class='item'></item>");
            el.append("<div class='title'></item>");
            el.append("<div class='time'><span class='start'>N</span>s</item>");
            el.append("<img class='ico' />");
            el.append("<div class='body'></item>");
            el.append("<a class='link'></a>");
            el.css('opacity', 0);
            el.hide();
            $(this.el).prepend(el);
            options._el = el;
            this.items.push(options);
            return el;
        },

        render : function (options, player){
            var self= this;
            var el = $(options._el);
            el.find('.title').text( options.title );
            el.find('.body').text( options.body );
            el.find('.start').text( options.start );
            el.find('img').attr('src',  options.img || TimeFeed.getFav(options.href) );
            el.find('.link').attr('target',  "_blank");
            el.find('.link').attr('href',  options.href)
                .text(options.href)
                .click( function () {
			console.log("click.handlePause");
                    //handlePause();
                    player.pause();
            });
            el.click( function () {
                if( player.paused() ) {
			console.log("handlePause time");
                    handlePause({'time': options.start});
		}
                else {
			console.log("handlePause time");
                    handlePlay({'time': options.start});
                    //handlePause({'time': options.start});
		}

            });

            el.mouseenter( function (){
                self.mouseEnter();
            });

            el.mouseleave( function (){
                self.mouseLeave();
            });

            el.find('a').click( function (e) {
                // don't seek
                e.stopPropagation();
            });

            el.show();
            this.focus(options);
        },

        player : function (popcorn) {
            if( !(popcorn === undefined) )
                this._player = popcorn;
            return this._player;
        },

        mouseEnter : function (){
//            this._player.pause();
        },

        mouseLeave : function (){
//            this._player.play();
        },

        focus : function (options) {
            var el = $(options._el).animate({'opacity': 1}, 1000, function () {
            });
        },

        blur : function (options) {
            if(! this.config.highlight )
                return;
            var el = $(options._el).animate({'opacity': .5}, 1000, function () {
                el.css('opacity', '');
            });
        },

        hideAll : function () {
            $(this.el).find('.item').hide();
        },

        toggle : function (bool) {
            var show;
            if( bool == null)
                show = "toggle";
            else if( bool )
                show = 1;
            else
                show = 0;
            $('#feeddiv').animate({'opacity': show});
        },

        onFirstItem : function () {
            /// abstract, callback
        }

    };
    window.timefeed = TimeFeed;
})();


(function () {
    if( ! Popcorn )
        return;

    var hasItems = false;
    Popcorn.plugin( "timefeed" , {
        _setup : function (options) {
            timefeed(options.target).player(this);
            timefeed(options.target).add(options);
        },
        start : function (event, options) {
            var tf = timefeed(options.target);
            if( ! tf.hasItems ){
                tf.hasItems = true;
                tf.onFirstItem && tf.onFirstItem();
            }
            timefeed(options.target).render(options, this);
        },
        end : function (event, options) {
            timefeed(options.target).blur(options);
        },
        _teardown : function (options) {
            var tf = timefeed(options.target);
            tf.hasItems = false;
            tf.hideAll();

        }
    });

})();
