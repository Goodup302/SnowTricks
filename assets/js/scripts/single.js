import $ from 'jquery'

$(document).ready(function() {
    var page = 1;

    let paginateElement = $('.pagination');
    let commentContainer = $('#comments');

    if (commentContainer.length) getComments();

    function getComments() {
        paginateElement.find('a[page]').each(function(){ $(this).removeClass('clicked') });
        paginateElement.find('a[page='+page+']').addClass('clicked');
        $.post(comments_url, {
            'page': page
        }, function (data) {
            console.log(data);
            commentContainer.html(data);
        })
    }
    paginateElement.on('click', 'a[page]', function (e) {
        if (page != $(this).attr('page')) {
            page = $(this).attr('page');
            getComments();
        } else {
            e.preventDefault();
        }
    });

    paginateElement.on('click', 'a[action]', function (e) {
        let action = $(this).attr('action');
        let nbPage = paginateElement.find('a[page]').length;
        if (action === "prev" && page > 1) {
            page--;
            getComments();
        } else if (action === "next" && page < nbPage) {
            page++;
            getComments();
        } else {
            e.preventDefault();
        }
    });
});
