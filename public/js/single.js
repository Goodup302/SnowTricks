$(document).ready(function() {
    var page = 1;

    paginateElement = $('.pagination');
    commentContainer = $('#comments');

    getComments();

    function getComments() {
        $.post(comments_url, {
            'page': page
        }, function (data) {
            console.log(data);
            commentContainer.html(data);
        })
    }

    paginateElement.on('click', 'a[page]', function () {
        paginateElement.find('a[page]').each(function(){ $(this).removeClass('clicked') });
        $(this).addClass('clicked');
        page = $(this).attr('page');
        getComments();
    });
});
