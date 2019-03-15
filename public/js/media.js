var deletableElement;
$(document).ready(function() {
    updateItem();
});

//Add Video
$('form.video_form').on('submit', (function (e) {
    e.preventDefault();
    showLoader();
    var form = $(this);
    $.ajax({
        type: "POST",
        url: form.attr('action'),
        data: form.serialize(),
        error: function(jqXHR, textStatus, errorMessage) {ajaxError()},
        success: function(html) {
            $('#video_modal').modal('hide');
            addMedia('video', html);
            updateItem();
            hideLoader();
        }
    });
}));

//Add Image
$('#'+fileInputId).change(function(){
    showLoader();
    var formData = new FormData();
    var url = $(this).attr('action');
    var files = document.getElementById($(this).attr('id')).files;
    //Add All File
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        if (file.type == 'image/jpeg') {
            formData.append(fileInputName, file, file.name);
        } else {
            hideLoader();
            Swal.fire({type: 'error', title: 'Oops...',
                text: "Seulement les images .jpg et .jpeg sont acceptées"
            });
            return;
        }
    }
    //Ajax request
    $.ajax({
        type: "POST",
        url: url,
        data: formData,
        processData: false,
        contentType: false,
        error: function(jqXHR, textStatus, errorMessage) {ajaxError()},
        success: function(html) {
            addMedia('image', html);
            updateItem();
            hideLoader();
        }
    });
});

//Delete Item (Image/video)
function updateItem() {
    if (deletableElement) {deletableElement.off()}
    deletableElement = $(".media .delete");
    deletableElement.bind('click', (function (e){
        showLoader();
        var item = $(this).parent().parent().parent();
        var url = $(this).attr('action');
        //Ajax request
        $.ajax({
            type: "POST",
            url: url,
            data: {
                _method: "DELETE"
            },
            error: function(jqXHR, textStatus, errorMessage) {ajaxError()},
            success: function(success) {
                if (success) {
                    deleteItem(item)
                }
                hideLoader();
            }
        });
    }));
}

//Add html ajax response
function addMedia(type, html) {
    $items = $('div[mediatype="'+type+'"]');
    if ($items.length > 0) {
        $items.last().after(html);
    } else {
        $('div[mediatype="new"]').before(html);
    }
}

//Popup on ajax error
function ajaxError() {
    hideLoader();
    Swal.fire({
        type: 'error',
        title: 'Oops...',
        text: "Une erreur est survenue (Réessayer plus tard) !"
    })
}