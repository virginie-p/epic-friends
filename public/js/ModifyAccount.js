$(function() {
    $('#modify-account-button').on('click', function(e) {
        e.preventDefault();
        $('#modify-account').find('.is-invalid').removeClass('is-invalid');
        $('.messages').remove();

        let form = $('#modify-account')[0];
        let formData = new FormData(form);

        $.ajax({
            url : $('#modify-account').attr('action'),
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
                    if (error == 'user_not_found') {
                        window.location.href = baseUrl + '/404-not-found';
                    }
                    else if (error == 'modification_server_didnt_worked') {
                        $('form').prepend('<div class="alert alert-danger messages" role="alert">La modification n\'a pas pu être effectuée en base, merci de réessayer.</div>');
                    }
                    else if (error == 'username_already_used') {
                        $('input[name="username"]').addClass('is-invalid');
                        $('input[name="username"]').after('<div class="invalid-feedback messages">Ce pseudo est déjà attribué à un utilisateur.</div>')
                    }
                    else if (error == 'username_not_matching_regex') {
                        $('input[name="username"]').addClass('is-invalid');
                        $('input[name="username"]').after('<div class="invalid-feedback messages">Merci de bien vouloir respecter le format attendu pour votre pseudo.</div>')
                    }
                    else if (error == 'username_too_long') {
                        $('input[name="username"]').addClass('is-invalid');
                        $('input[name="username"]').after('<div class="invalid-feedback messages">Votre pseudo ne peut pas faire plus de 20 caractères.</div>')
                    }
                    else if (error == 'passwords_not_identical') {
                        $('input[name="password-confirmation"], input[name="new-password"]').addClass('is-invalid');
                        $('input[name="password-confirmation"]').after('<div class="invalid-feedback messages">Les mots de passe renseignés ne sont pas identiques.</div>')
                    }
                    else if (error == 'password_not_matching_regex') {
                        $('input[name="new-password"], input[name="password-confirmation"]').addClass('is-invalid');
                        $('input[name="new-password"]').after('<div class="invalid-feedback messages">Merci de bien vouloir respecter le format attendu pour votre mot de passe.</div>')
                    }
                    else if (error == 'email_already_used') {
                        $('input[name="email"]').addClass('is-invalid');
                        $('input[name="email"]').after('<div class="invalid-feedback messages">Un compte avec cette adresse email est déjà existant.</div>')
                    }
                    else if (error == 'email_invalid') {
                        $('input[name="email"]').addClass('is-invalid');
                        $('input[name="email"]').after('<div class="invalid-feedback messages">Merci de bien vouloir renseigner une adresse email.</div>')
                    }
                });
            }
            else if (data.status === 'success') {
                $('form').prepend('<div class="alert alert-success messages" role="alert">Votre profil a bien été mis à jour.</div>');
                $('#hello-text').text('Bonjour ' + data.username + ' !');
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
        });
    });
});