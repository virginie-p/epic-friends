$(function(){

    $('#send-message').on('click', function(e){
        e.preventDefault();
        tinyMCE.triggerSave(true,true);
        $('.messages').remove();

        let form = $('#send-new-message')[0];
        let formData = new FormData(form);

        $.ajax({
            url : $('#send-new-message').attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false
        })
        .done(function(response){
            let data = response;
            if (data.status === 'error') {
                let errors = data.errors;
                errors.forEach(error => {
                    if(error ==  'message_not_filled') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous n\'avez pas renseigné de message à envoyer.</div>');
                    }
                    else if(error == 'user_not_found') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous ne pouvez pas envoyer de message à cet utilisateur.</div>');
                    }
                    else if(error == 'message_just_blanks') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous ne pouvez pas envoyer un message composé uniquement d\'espaces.</div>');
                    }
                    else if(error == 'message_too_long') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous ne pouvez pas envoyer un message composé de plus de 2000 caractères.</div>');
                    }
                });
            }
            else if (data.status === 'success') {

            }
        });
    });

    $('#user-message').setInterval(() => {

        $.ajax({
            url : baseUrl + '/refresh-messages/',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false
        })
        .done(function(response){
            let data = response;
            if (data.status === 'error') {
                let errors = data.errors;
                errors.forEach(error => {
                    if(error ==  'message_not_filled') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous n\'avez pas renseigné de message à envoyer.</div>');
                    }
                    else if(error == 'user_not_found') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous ne pouvez pas envoyer de message à cet utilisateur.</div>');
                    }
                    else if(error == 'message_just_blanks') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous ne pouvez pas envoyer un message composé uniquement d\'espaces.</div>');
                    }
                    else if(error == 'message_too_long') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous ne pouvez pas envoyer un message composé de plus de 2000 caractères.</div>');
                    }
                });
            }
            else if (data.status === 'success') {

            }
        });
        
    }, 1000);

});

