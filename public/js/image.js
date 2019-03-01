//////////////
// VARIABLE //
//////////////

var waitingMedia = false;
var media_picker = $('#media_picker');
var mediaItems = $('#media_items');
var loader = $('#ajax_loader');

var selectedImage = '.media_item.selected';
var selectedClass = 'selected';

var validationButton = $('.close_media[save=true]');
var cancelButton = $('.close_media[save=false]');

var isMultiple = false;
var isRequired = false;
var currentSelectValue = [];




//////////////
// FUNCTION //
//////////////

//Open media picker
function imagePicker(id, multiple, required) {
    if (!waitingMedia) {
        waitingMedia = true;
        showLoader();
        media_picker.attr('target', id);
        isMultiple = multiple;
        isRequired = required;
        $.post(urlImageList, {}).done(function( images ) {
            mediaItems.html('');


            currentSelectValue = $('#'+id).val() != null ? $('#'+id).val() : [];
            console.log(currentSelectValue);
            images.forEach(function(image) {
                console.log(image.id);
                select = currentSelectValue.includes(''+image.id+'') === true ? selectedClass : '';
                mediaItems.append('<img class="media_item card '+select+'" name="'+image.name+'" id="'+image.id+'" src="'+image.url+'">');
            });

            refrechImage();
            hideLoader();
            showImagePicker();
        })
        .fail(function() {
            hideLoader();
            hideImagePicker();
            Swal.fire(
                '',
                'Une erreur est survenue réessayez plus tard ...',
                'error'
            )
        });
    }
}

/* Show Loader */
function showLoader() {
    loader.css('display', 'flex');
    setTimeout(function(){ loader.css('opacity', '1'); }, 10);
}
function hideLoader() {
    loader.css('display', 'none');
    loader.css('opacity', '0');
}

/* Show Picker */
function showImagePicker() {
    waitingMedia = true;
    validationButton.prop( "disabled", false );
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
        if (!isMultiple) {
            var has = $(this).hasClass(selectedClass);
            $('.media_item').removeClass(selectedClass);
            if (!has || isRequired) {
                $(this).addClass(selectedClass);
            }
        } else {
            $(this).toggleClass(selectedClass);
        }


        if (isRequired) {
            if ($(selectedImage).length === 0) {
                validationButton.prop( "disabled", true );
            } else {
                validationButton.prop( "disabled", false );
            }
        }
    });
}



///////////
// EVENT //
///////////

//Close Picker
$(".close_media").click(function (){
    targetInput = $('#'+media_picker.attr('target'));
    if ($(this).attr('save') == "true") {
        targetInput.html('');
        getImages().forEach(function(image) {
            targetInput.append('<option value="'+image.id+'" selected="selected">'+image.name+'</option>');
        });
    }
    mediaItems.html('');
    hideImagePicker()
});


//Upload Image
$('#'+inputId+':file').change(function(){
    showLoader();
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
            hideLoader();
            return;
        }
    }
    //console.log(formData.getAll(inputName));
    $.ajax({
        type: "POST",
        url: urlImageAdd,
        data: formData,
        processData: false,
        contentType: false,
        error: function(jqXHR, textStatus, errorMessage) {
            hideLoader();
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: "Le ou les fichiers n'ont pas pus ètre traités !"
            })
        },
        success: function(data) {
            console.log(data);
            mediaItems.append(data);
            hideLoader();
            refrechImage();
        }
    });
});

//Delete Image
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
                text: "Une erreur est survenue (Réessayer plus tard) !"
            })
        },
        success: function(success) {
            if (success) {
                item.remove()
            } else {
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: "Cette image est utilisée !"
                })
            }
        }
    });
});