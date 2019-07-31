import $ from 'jquery'

$("#edit_account_files").change(function () {
    let reader = new FileReader();
    let image = $(this).parent().find('img');
    reader.onload = (e) => {image.attr('src', e.target.result)};
    reader.readAsDataURL(this.files[0]);
});