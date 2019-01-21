$(document).ready(function() {

    //Init Page
    refreshHeader();

    //Link smooth scroll
    $("a[href*='#']:not([href='#'])").click(function() {
        if (
            location.hostname == this.hostname
            && this.pathname.replace(/^\//,"") == location.pathname.replace(/^\//,"")
        ) {
            var anchor = $(this.hash);
            anchor = anchor.length ? anchor : $("[name=" + this.hash.slice(1) +"]");
            if ( anchor.length ) {
                $("html, body").animate( { scrollTop: anchor.offset().top }, 900);
            }
        }
    });

    //Header
    function refreshHeader() {
        $( window ).width();
        if ($(window).width() >= 576) {
            $('nav').show("slow");
            $('#wallpaper img').css('height', 'calc(100vh - '+$('nav').height()+'px)');
        } else {
            $( "nav" ).hide("slow");
            $('#wallpaper img').css('height', 'calc(100vh - 100px)');
        }
    }

    //Event
    $( window ).resize(function() {
        refreshHeader()
    });
});