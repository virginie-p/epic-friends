$(function() {

    $("#subscribe-button").click(function(e){
        e.preventDefault();
        $('.form-group>.messages-subscription').remove();
        $('.messages-subscription').empty();

        let form = $('#subscribe-form')[0];
        let formData = new FormData(form);

        $.ajax({
            url : $('#subscribe-form').attr('action'),
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
                errors.forEach(element => {
                    if (element == 'missing_fields') {
                        $('.messages-subscription').prepend('<div class="alert alert-danger" role="alert">Un ou plusieurs champs ne sont pas renseignés, tous les champs doivent être renseignés afin de procéder à la création du compte.</div>');
                    }
                    else if (element == 'image_or_size_invalid') {
                        $('#select-profile-picture-help').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Votre image dépasse la taille maximum autorisée par le serveur (2Mo)</div>');
                    }
                    else if (element == 'upload_problem') {
                        $('.messages-subscription').prepend('<div class="alert alert-danger" role="alert">Le compte n\'a pas pu être créé en BDD.</div>');
                    }
                    else if (element == 'passwords_not_identical') {
                        $('input[name="password-confirmation"]').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Les deux mots de passe renseignés ne sont pas identiques.</div>');
                    }
                    else if (element == 'username_not_matching_regex') {
                        $('input[name="subscribe-username"]').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Attention, votre login doit être composé de 6 caractères minimum (incluant points, tirets et caractères alphanumériques).</div>');
                    }
                    else if (element == 'username_already_used') {
                        $('input[name="subscribe-username"]').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Ce login est déjà attribué à un autre utilisateur.</div>');
                    }
                    else if (element == 'email_invalid') {
                        $('input[name="email"]').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Merci de renseigner une adresse email valide.</div>');
                    }
                    else if (element == 'email_already_used') {
                        $('input[name="email"]').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Cette adresse a déjà été utilisée pour créer un compte.</div>'); 
                    }
                    else if (element == 'password_not_matching_regex') {
                        $('input[name="password-confirmation"]').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Le format du mot de passe n\'est pas respecté</div>');
                    }
                    else if (element == 'age_not_authorized') {
                        $('input[name="birthdate"]').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Vous n\'avez pas l\'âge requis pour vous inscrire.</div>');
                    }
                    else if (element == 'invalid_extension') {
                        $('#select-profile-picture-help').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Ce type de fichier n\'est pas accepté. Seuls les fichiers .jpeg, .jpg et .png sont acceptés.</div>');  
                    }
                    else if (element == 'file_not_moved') {
                        $('#select-profile-picture-help').after('<div class="alert alert-danger mt-2 messages-subscription" role="alert">Le fichier n\'a pas pu être enregistré sur le serveur. Merci de bien vouloir réessayer.</div>');  
                    }
                });
            }
            else if (data.status === 'success') {
                $('.messages-subscription').prepend('<div class="alert alert-success" role="alert">Votre compte a bien été créé.</div>');
                $('#subscribe-form').find('input').val('');
            }
        });
    });

    $('#subscribeForm').on('hidden.bs.modal', function(){
        $('.form-group>.messages-subscription').remove();
        $('.messages-subscription').empty();
        $('#subscribeForm input').val('');
    });

});