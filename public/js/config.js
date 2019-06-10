const baseUrl = 'http://localhost/projet-5';

$(function() {
    tinymce.init({
        forced_root_block : "",
        selector: '#mytextarea',
        plugins: "emoticons",
        menubar: false,
        toolbar: 'bold italic underline emoticons',
        branding: false,
        editor_css : baseUrl + '/public/css/main.css',
    });

    
});
