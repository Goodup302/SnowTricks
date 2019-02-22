//Open media picker
var waitingMedia = false;
function media(path, id, multiple) {
    if (!waitingMedia) {
        waitingMedia = true;
        $.post(path, {target: id})
            .done(function( data ) {
                $('body').prepend(data);
                waitingMedia = false;
                $('html, body').css('overflow', 'hidden');
            });
    }
}

$(document).ready(function() {
    var inputId = '{{ form.files.vars.id }}';
    var inputName = '{{ form.files.vars.full_name }}';
    var mediaItems = $('#media_items');
    $(".close_media").click(function (){
        $targetInput = $('#'+$('#media_picker').attr('target'));
        if ($(this).attr('save') == "true") {
            $targetInput.remove();
        }
        $('html, body').css('overflow', 'unset');
        $("#media_picker").remove();
    });

    $('#'+inputId+':file').change(function(){
        $(this).prop('disabled', true);
        var input = $(this);
        var formData = new FormData(document.getElementsByName('media')[0]);
        var files = document.getElementById(inputId).files;

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (file.type.match('image.*')) {
                formData.append(inputName, file, file.name);
            }
        }
        console.log(formData.getAll(inputName));
        $.ajax({
            type: "POST",
            url: 'image/add',
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
            }
        });
    });
});