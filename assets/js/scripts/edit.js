import $ from 'jquery'

//Event list
var deletableElement;
var thumbnailElement;
//
var selectThumbnail = $('#'+fileThumbnailId);
var currentThumbnailId;


/********
* EVENT *
********/

$(document).ready(function() {
    updateItem();
});

//Add Video
$('form.video_form').on('submit', (function (e) {
    e.preventDefault();
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
        }
    });
}));

//Add Image
$('#'+fileInputId).change(function(){
    var formData = new FormData();
    var url = $(this).attr('action');
    var files = document.getElementById($(this).attr('id')).files;
    if (files.length > 10) {
        Swal.fire({type: 'error', title: 'Oops...',
            text: "Vous ne pouvez pas ajouter autant d'images"
        });
        return;
    }
    //Add All File
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        if (file.type == 'image/jpeg') {
            formData.append(fileInputName, file, file.name);
        } else {
            Swal.fire({type: 'error', title: 'Oops...',
                text: "Seulement les images .jpg et .jpeg sont acceptées"
            });
            return;
        }
    }
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
        }
    });
});

function updateItem() {

    //Delete Item (Image/video)
    if (deletableElement) {deletableElement.off()}
    deletableElement = $(".media .delete");
    deletableElement.bind('click', (function (e) {
        var item = $(this).parent().parent().parent();
        var type = item.attr('mediatype');
        var id = $(this).attr('data-id');
        var url = $(this).attr('action');
        //Ajax request
        $.ajax({
            type: "POST",
            url: url,
            data: {_method: "DELETE"},
            error: function(jqXHR, textStatus, errorMessage) {ajaxError()},
            success: function(success) {
                if (success) {
                    deleteItem(item);
                    if (type == 'image') {
                        var option = getThumbnailOption(id);
                        if (option.val() == currentThumbnailId) {
                            resetThumbnailImg();
                        }
                        option.remove();
                    }
                }
            }
        });
    }));

    //Define thumbnail of trick
    if (thumbnailElement) {thumbnailElement.off()}
    thumbnailElement = $(".media .thumbnail_button");
    thumbnailElement.bind('click', (function (e){
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        if (getThumbnailOption(id).length === 0) {
            selectThumbnail.append('<option value="'+id+'">'+name+'</option>');
        }
        setThumbnail($(this).attr('data-id'))
    }));
}

//Delete thumbnail of trick
$('#single_thumbnail .delete').click(function(){
    setThumbnail(0)
});



/*************
*  FUNCTION  *
*************/

//Add html ajax response
function setThumbnail(id) {
    selectThumbnail.find('option').each(function(){
        $(this).removeAttr("selected");
    });
    if (id > 0) {
        let option = getThumbnailOption(id);
        option.attr('selected', 'selected');
        currentThumbnailId = id;
        $('img.edit_thumbnail').attr('src', uploadImageUrl+option.html())
    } else {
        resetThumbnailImg()
    }
}

//set img of thumbnail to default image
function resetThumbnailImg() {
    $('img.edit_thumbnail').attr('src', emptyImageUrl);
    currentThumbnailId = 0;
}

//get option of thumbnail select by id
function getThumbnailOption(id) {
    return selectThumbnail.find('option[value='+id+']');
}

//Add html ajax response
function addMedia(type, html) {
    let $items = $('div[mediatype="'+type+'"]');
    if ($items.length > 0) {
        $items.last().after(html);
    } else {
        $('div[mediatype="new"]').before(html);
    }
}