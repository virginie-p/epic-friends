$(function() {
    tinymce.init({
        forced_root_block : "",
        selector: '#mytextarea',
        menubar: false,
        toolbar: 'bold italic underline emoticons',
        branding: false,
        editor_css : baseUrl + '/public/css/main.css',
    });

    tinymce.init({
        forced_root_block : "",
        selector: '#message',
        menubar: false,
        entity_encoding : "raw",
        toolbar: 'bold italic underline emoticons',
        branding: false,
        editor_css : baseUrl + '/public/css/main.css',
    });
});