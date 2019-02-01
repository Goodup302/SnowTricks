var breakpoint_sm = 576;
var breakpoint_md = 768;
var breakpoint_lg = 992;
var breakpoint_xl = 1200;

$(document).ready(function() {

    //Init Page
    //$('.sf-toolbar').remove();
    refreshHeader();
    refreshArrowUp();

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
        if ($(window).width() >= breakpoint_md) {
            $("#nav_mobile").hide();
            $("#nav_desktop").show();
            $('#wallpaper img').css('height', 'calc(100vh - '+$('#nav_desktop').height()+'px)');
            $('#mobile_bottom').css('height', '0');
        } else {
            $("#nav_desktop").hide();
            $("#nav_mobile").show();
            $('#mobile_bottom').css('height', $("#nav_mobile").height());
            $('#wallpaper img').css('height', 'calc(100vh - '+$('#nav_mobile').height()+'px)');
        }
    }

    //Arrow up
    function refreshArrowUp() {
        if ($('#tricks').length) {
            var offset = $('#tricks')[0].getBoundingClientRect().top;
            var tricks = $('#tricks')[0].childElementCount;
            if (offset < -200 && $(window).width() >= breakpoint_md && tricks >= 15) {
                $("#arrow_up").show();
            } else {
                $("#arrow_up").hide();
            }
        }

    }
    //Event
    $( window ).resize(function() {
        refreshHeader();
        refreshArrowUp();
    });
    $( window ).scroll(function() {
        refreshArrowUp();
    });
});

//Open media picker
var waitingMedia = false;
function media(path) {
    if (!waitingMedia) {
        waitingMedia = true;
        $.post(path, {})
            .done(function( data ) {
                $('body').prepend(data);
                waitingMedia = false;
            });
    }
}

//SwalAlert2 confirm submit form
$(".swa-confirm").on("click", function(e) {
    e.preventDefault();
    var form = $(this).parents('form');
    Swal.fire({
        title: form.attr("swa-title"),
        text: form.attr("swa-text"),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Supprimer',
        cancelButtonText: 'Annuler'
    }).then(function (result) {
        console.log(result);
        if (result.value) {
            form.submit();
        }
    });
});