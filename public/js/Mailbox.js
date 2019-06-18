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
                    else if (error == 'sender_equals_recipient') {
                        $('#send-message').before('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Vous ne pouvez pas vous envoyer de message à vous même !</div>')
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
                $('#users-contacted>div').prepend($('.clicked-conversation'));
                $('#user-messages').scrollTop($('#user-messages').prop('scrollHeight'));

            }
        });
    });

    function refreshMessages() { 
        $.ajax({
            url : `${baseUrl}/get-new-messages/${$('.clicked-conversation').attr('data-user-id')}/from-message/${$('#user-messages>div:last-child').attr('data-message-id')}`,
            type: 'GET',
            dataType: 'json'
        })
        .done(function(response){
            let data = response;
            if (data.status === 'error') {
                let errors = data.errors;
                errors.forEach(error => {
                    if (error == 'user_not_found') {
                        $('h2').after('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Aucun utilisateur ne correspond à cet identifiant.</div>');  
                    }
                    else if (error == 'cannot_reach_database') {
                        $('h2').after('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Un problème est survenu lors de la tentative d\'accès à la base de données.</div>');  
                    }
                    else if (error == 'no_messages') {
                        $('h2').after('<div class="alert alert-danger messages col-12 p-1 mt-3" role="alert">Aucun message n\'a jamais été échangé avec cet utilisateur.</div>');  
                    }
                });
            }
            else if (data.status === 'success') {
                data.new_messages.forEach(message => {
                    let messageDiv;
                    if (message.sender_id == data.user_id) {
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
    }

    let refresh = setInterval(refreshMessages, 2000);

    function openMessages(e) {
        e.preventDefault();
        
        //Change line style
        $('.user-conversation').removeClass('clicked-conversation');
        let url;
        if ($(e.target).hasClass('user-conversation')) {
            $(e.target).addClass('clicked-conversation');
            url = $(e.target).attr('href');$('#send-new-message').attr('action', `${baseUrl}/send-message/user/${$(e.target).attr('data-user-id')}`);

        } else {
            $(e.target).parents('a').addClass('clicked-conversation');
            url = $(e.target).parent().attr('href');
            $('#send-new-message').attr('action', `${baseUrl}/send-message/user/${$(e.target).parent().attr('data-user-id')}`);
        } 

        clearInterval(refresh);
        $('#user-messages').attr('data-user-id', $(e.target).parent().attr('data-user-id'));
        
        $(e.target).siblings('.unread-notification').html('');

        $.ajax({
            url : url,
            type: 'GET',
            dataType: 'json'
        })
        .done(function(response){
            let data = response;
            if (data.status === 'success') {
                $('#user-messages').html('');
                data.messages.forEach(message => {
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
                    $('#user-messages').append(messageDiv);
                });
                $('#user-messages').scrollTop($('#user-messages').prop('scrollHeight'));
            }
        });

        refresh = setInterval(refreshMessages, 2000);
    }

    $('.user-conversation').on('click', openMessages);

    function refreshUnreadMessages() {
        $.ajax({
            url : `${baseUrl}/get-unread-messages`,
            type: 'GET',
            dataType: 'json'
        })
        .done(function(response){
            if (response.status == 'success'){
                response.new_messages.forEach(message => {
                    if(message.id > $('.user-conversation[data-last-message-id]').attr('data-last-message-id')){
                        if($(`.user-conversation[data-user-id = ${message.sender_id}]`).length > 0) {
                            $(`.user-conversation[data-user-id = ${message.sender_id}]`).remove();
                        }

                        $('.user-conversation[data-last-message-id]').removeAttr('data-last-message-id');
                        $('#users-contacted>div').prepend(`
                        <a class="row py-2 align-items-center user-conversation" href="http://localhost/projet-5/display-messages/${message.sender_id}" data-user-id="${message.sender_id}" data-last-message-id="${message.id}">
                            <div class="rounded-profile-picture col-2">
                            <img class="border border-secondary" src="${baseUrl}/public/images/profiles-pictures/${message.sender_profile_picture}" alt="">
                            </div>
                            <div class="col-5">
                                ${message.sender_username}
                            </div>
                            <div class="col-5 unread-notification">
                                <span class="badge badge-danger">Message(s) non-lu(s)</span>
                            </div>
                        </a>
                        `);

                        $(`.user-conversation[data-user-id="${message.sender_id}"]`).on('click', openMessages);
                    }
                });
            }
        });
    }
    
    let refreshUnread = setInterval(refreshUnreadMessages, 2000);
});

