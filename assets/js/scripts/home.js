import $ from 'jquery'

$(document).ready(() => {
    let length = $('.tricks_card').length;
    let trickNumber = length;
    let offsetInterval = length;
    let buttonMoreTrick = $('#more_trick');

    buttonMoreTrick.on('click', () => {
        $.post('/trick_list', {
            'offset': trickNumber
        }, function (data) {
            trickNumber += offsetInterval;
            $('#tricks').append(data);
        })
    });
});