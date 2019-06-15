$(function(){
    $('#user-messages').scrollTop($('#user-messages')[0].scrollHeight);

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
                tinyMCE.get('message').setContent('');
            }
        });
    });

    setInterval(() => {
       
        $.ajax({
            url : `${baseUrl}/get-new-messages/${$('#user-messages').attr('data-user-id')}/from-message/${$('#user-messages>div:last-child').attr('data-message-id')}`,
            type: 'GET',
            dataType: 'json'
        })
        .done(function(response){
            let data = response;
            if (data.status === 'error') {
                let errors = data.errors;
                errors.forEach(error => {
                    
                });
            }
            else if (data.status === 'success') {
                data.new_messages.forEach(message => {
                    let messageDiv;

                    if (message.sender_id === data.user_id) {
                        messageDiv = `
                        <div class="row justify-content-end align-items-center"  data-message-id="${message.id}">
                            <div class="p-1 m-3 rounded user-message text-left">
                                ${message.message}
                            </div>
                            <div class="p-2 date">
                                ${message.creation_date}
                            </div>
                        </div>`;
                    }        
                    else {
                        messageDiv = `
                        <div class="row justify-content-start align-items-center" data-message-id="${message.id}">
                            <div class="p-2 date">
                                ${message.creation_date}
                            </div>
                            <div class="p-1 m-3 rounded border clicked-member text-left">
                                ${message.message}
                            </div>
                        </div>`;
                    }               

                    if(($('#user-messages').scrollTop() + $('#user-messages').innerHeight()) === $('#user-messages').prop('scrollHeight')) { 
                        $('#user-messages').append(messageDiv);
                        $('#user-messages').scrollTop($('#user-messages').prop('scrollHeight'));
                    }
                    else {
                        $('#user-messages').append(messageDiv);
                    }
                });
            }
        });  
    }, 2000);


    $('.user-conversation').on('click', function(e) {
        e.preventDefault();
        
    });
});

