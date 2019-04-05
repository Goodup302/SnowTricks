$(document).ready(function() {
    var page = 1;

    paginateElement = $('.pagination');
    commentContainer = $('#comments');

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
    paginateElement.on('click', 'a[page]', function () {
        page = $(this).attr('page');
        getComments();
    });

    paginateElement.on('click', 'a[action]', function () {
        action = $(this).attr('action');
        nbPage = paginateElement.find('a[page]').length;
        if (action === "prev" && page > 1) {
            page--;
        } else if (action === "next" && page < nbPage) {
            page++;
        }
        getComments();
    });
});
