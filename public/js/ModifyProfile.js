$(function()  {
    function ModifyProfileValidation(e) {
        e.preventDefault();
        tinyMCE.triggerSave(true,true);
        $('#modify-profile').find('.is-invalid').removeClass('is-invalid');
        $('label[for="profile-picture"], .selectize-input, label[for="profile-banner"]').removeAttr('style');
        $('.messages').remove();
        
        let form = $('#modify-profile')[0];
        let formData = new FormData(form);

        $.ajax({
            url : $('#modify-profile').attr('action'),
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
                        console.log('test');
                        window.location.href = baseUrl + '/404-not-found';
                    }
                    else if(error == 'gender_not_existing') {
                        $('select[name="gender"]').addClass('is-invalid');
                        $('select[name="gender"]').after('<div class="invalid-feedback messages">Le genre renseigné est incorrect.</div>')
                    }
                    else if(error == 'age_not_authorized') {
                        $('input[name="birthdate"]').addClass('is-invalid');
                        $('input[name="birthdate"]').after('<div class="invalid-feedback messages">Vous devez avoir plus de 18 ans pour pouvoir utiliser notre site.</div>');
                    }
                    else if(error == 'too_old') {
                        $('input[name="birthdate"]').addClass('is-invalid');
                        $('input[name="birthdate"]').after('<div class="invalid-feedback messages">Petit(e) malin(e), n\'essayez pas de nous faire croire que vous êtes aussi âgé(e) que Gandalf !</div>');
                    }
                    else if(error == 'county_not_existing') {
                        $('input[name="county"]').addClass('is-invalid');
                        $('input[name="county"]').after('<div class="invalid-feedback messages">Ce département n\'existe pas. Merci de sélectionner un des départements qui vous est proposé.</div>');
                    }
                    else if(error == 'interest_does_not_exists') {
                        $('.selectize-input').css('border', 'solid 1px #dc3545');
                        $('select[name="interests[]"]').parent().append('<div class="invalid-select messages">Un des intérêts renseigné n\'existe pas dans nos bases. Merci de bien vouloir sélectionner vos centres d\'intérêts depuis la liste prévue à cet effet.</div>');
                    }
                    else if(error == 'fav_quote_just_blanks') {
                        $('textarea[name="favorite-citation"]').addClass('is-invalid');
                        $('textarea[name="favorite-citation"]').after('<div class="invalid-feedback messages">Votre citation favorite ne peut pas être composée uniquement d\'espaces</div>');
                    }
                    else if(error == 'fav_quote_too_long') {
                        $('textarea[name="favorite-citation"]').addClass('is-invalid');
                        $('textarea[name="favorite-citation"]').after('<div class="invalid-feedback messages">Votre citation favorite ne peut excéder 255 caractères.</div>');
                    }
                    else if(error == 'fav_char_just_blanks') {
                        $('input[name="identified-as"]').addClass('is-invalid');
                        $('input[name="identified-as"]').after('<div class="invalid-feedback messages">Votre personnage fétiche ne peut pas être composé uniquement d\'espaces</div>');
                    }
                    else if(error == 'fav_char_too_long') {
                        $('input[name="identified-as"]').addClass('is-invalid');
                        $('input[name="identified-as"]').after('<div class="invalid-feedback messages">Votre personnage fétiche ne peut excéder 50 caractères.</div>');
                    }
                    else if(error == 'profile_picture_image_or_size_invalid') {
                        $('label[for="profile-picture"]').css('border', 'solid 1px #dc3545');
                        $('#select-profile-picture-help').after('<div class="invalid-select messages">Une erreur est survenue lors de l\'upload de votre photo de profil ou cette dernière fait plus de 2Mo.</div>')
                    }
                    else if (error == 'profile_picture_invalid_extension') {
                        $('label[for="profile-picture"]').css('border', 'solid 1px #dc3545');
                        $('#select-profile-picture-help').after('<div class="invalid-select messages">Ce type de fichier n\'est pas accepté. Seuls les fichiers .jpeg, .jpg et .png sont acceptés.</div>')
                    }
                    else if (error == 'profile_picture_file_not_moved') {
                        $('label[for="profile-picture"]').css('border', 'solid 1px #dc3545');
                        $('#select-profile-picture-help').after('<div class="invalid-select messages">Le fichier n\'a pas pu être enregistré sur le serveur. Merci de bien vouloir réessayer.</div>')
                    }
                    else if(error == 'banner_picture_image_or_size_invalid') {
                        $('label[for="profile-banner"]').css('border', 'solid 1px #dc3545');
                        $('#select-banner-picture-help').after('<div class="invalid-select messages">Une erreur est survenue lors de l\'upload de votre bannière ou cette dernière fait plus de 2Mo.</div>')
                    }
                    else if (error == 'banner_picture_invalid_extension') {
                        $('label[for="profile-banner"]').css('border', 'solid 1px #dc3545');
                        $('#select-banner-picture-help').after('<div class="invalid-select messages">Ce type de fichier n\'est pas accepté. Seuls les fichiers .jpeg, .jpg et .png sont acceptés.</div>')
                    }
                    else if (error == 'banner_picture_file_not_moved') {
                        $('label[for="profile-banner"]').css('border', 'solid 1px #dc3545');
                        $('#select-banner-picture-help').after('<div class="invalid-select messages">Le fichier n\'a pas pu être enregistré sur le serveur. Merci de bien vouloir réessayer.</div>')
                    }
                    else if (error == 'description_just_blanks') {
                        $('textarea[name="description"]').addClass('is-invalid');
                        $('textarea[name="description"]').after('<div class="invalid-feedback messages">Votre description ne peut pas être composée uniquement d\'espaces</div>');
                    }
                    else if (error == 'description_too_long') {
                        $('textarea[name="description"]').addClass('is-invalid');
                        $('textarea[name="description"]').after('<div class="invalid-feedback messages">Votre description ne peut pas excéder 2000 caractères</div>');
                    }
                    else if (error == 'modification_server_didnt_worked') {
                        $('form').prepend('<div class="alert alert-danger messages" role="alert">La modification n\'a pas pu être effectuée en base, merci de réessayer.</div>');
                    }
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                });
            }
            else if (data.status === 'success') {
                tinyMCE.get('mytextarea').setContent(data.description);
                $('form').prepend('<div class="alert alert-success messages" role="alert">Votre profil a bien été mis à jour.</div>');
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
        });

    }
    
    $('#modify-button').on('click', ModifyProfileValidation);
});