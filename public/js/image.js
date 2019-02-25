var waitingMedia = false;
var media_picker = $('#media_picker');
var mediaItems = $('#media_items');

var inputId = 'image_files';
var inputName = 'image[files][]';
var selectedImage = '.media_item.selected';


////////////////////////////
///////// FUNCTION /////////
////////////////////////////

//Open media picker
function imagePicker(id, multiple) {
    if (!waitingMedia) {
        showImagePicker();
        media_picker.attr('target', id);
        media_picker.attr('multiple', multiple);
        $.post('/image', {}).done(function( images ) {
            console.log(images);
            mediaItems.html('');
            images.forEach(function(image) {
                mediaItems.append('<img class="media_item card" name="'+image.name+'" id="'+image.id+'" src="'+image.url+'">');
            });
            refrechImage()
        });
    }
}


function showImagePicker() {
    waitingMedia = true;
    media_picker.css('display', 'block');
    $('html, body').css('overflow', 'hidden');
}
function hideImagePicker() {
    $('html, body').css('overflow', 'unset');
    media_picker.css('display', 'none');
    waitingMedia = false;
}

function getImages() {
    var images = [];
    $(selectedImage).each(function( index ) {
        images[index] = {
            "id": $(this).attr('id'),
            "name": $(this).attr('name'),
            "url": $(this).attr('src')
        }
    });
    return images;
}
function refrechImage() {
    $(".media_item").click(function (){
        $(this).toggleClass('selected');
    });
}




/////////////////////////
///////// EVENT /////////
/////////////////////////

$(".close_media").click(function (){
    targetInput = $('#'+media_picker.attr('target'));
    if ($(this).attr('save') == "true") {
        targetInput.html('');
        getImages().forEach(function(image) {
            targetInput.append('<option value="'+image.id+'" selected="selected">'+image.name+'</option>');
        });
    }
    hideImagePicker()
});

$('#'+inputId+':file').change(function(){
    $(this).prop('disabled', true);
    var input = $(this);
    var formData = new FormData(document.getElementsByName('media')[0]);
    var files = document.getElementById(inputId).files;

    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        if (file.type == 'image/jpeg') {
            formData.append(inputName, file, file.name);
        } else {
            Swal.fire(
                '',
                'Seulement les images .jpg et .jpeg sont acceptées',
                'error'
            );
            input.prop('disabled', false);
            return;
        }
    }
    console.log(formData.getAll(inputName));
    $.ajax({
        type: "POST",
        url: '/image/add',
        data: formData,
        processData: false,
        contentType: false,
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage);
            input.prop('disabled', false);
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: "Le ou les fichiers n'ont pas pus ètre traités !"
            })
        },
        success: function(data) {
            console.log(data);
            mediaItems.append(data);
            input.prop('disabled', false);
            refrechImage()
        }
    });
});

$(".media_item .delete").click(function(){
    var item = $(this).parent();
    var id = $(this).attr('itemid');
    var url = $(this).attr('action');
    $.ajax({
        type: "POST",
        url: url,
        data: {
            id: id,
            _method: "DELETE"
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(errorMessage);
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: "Suppression impossible (Réessayer plus tard) !"
            })
        },
        success: function(data) {
            item.remove();
            refrechImage()
        }
    });
});
