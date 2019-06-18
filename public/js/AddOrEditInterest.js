$(function(){
    $('#add-interest-button, #edit-interest-button').on('click', function(e){
        e.preventDefault();
        $('.message').remove();
        let url;
        let form;
        let action;

        if ($(e.target).attr('id') === 'add-interest-button') {
            url = $('form#add-interest').attr('action');
            form = $('#add-interest')[0];
            action = 'ajouté';
        } 
        else if ($(e.target).attr('id') === 'edit-interest-button') {
            url = $('form#edit-interest').attr('action');
            form = $('#edit-interest')[0];
            action = 'édité';
        }
        
        let formData = new FormData(form);

        $.ajax({
            url : url,
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false
        })
        .done(function(response){
            let data = response;
            if (data.status === 'error') {
                let errors = data.errors;
                errors.forEach(error => {
                    if (error == 'interest_name_just_blanks') {
                        $('h2').parent().after('<div class="alert alert-danger message" role="alert">Le nom d\'un centre d\'intérêt ne peut pas être composé uniquement d\'espaces.</div>');
                    }
                    else if (error == 'interest_name_too_long') {
                        $('h2').parent().after('<div class="alert alert-danger message" role="alert">Le nom d\'un centre d\'intérêt ne peut pas être composé de plus de 30 caractères.</div>');
                    }
                    else if (error == 'action_did_not_work') {
                        $('h2').parent().after(`<div class="alert alert-danger message" role="alert">Le centre d\'intérêt n\'a pas pu être ${action} en base. Merci de réessayer.</div>`);
                    }
                    else if (error == 'interest_not_found') {
                        $('h2').parent().after(`<div class="alert alert-danger message" role="alert">Le centre d\'intérêt que vous souhaitez modifier n\'existe pas en base.</div>`);
                    }

                });
            }
            else if (data.status === 'success') {
                if (action === 'ajouté') {
                    $('input[name="interest-name"]').val('');
                }
                $('h2').parent().after(`<div class="alert alert-success message" role="alert">Le centre d\'intérêt a bien été ${action}. Retournez à la liste pour visualiser l'ensemble des centres d\'intérêts.</div>`);
            }
        });

    });
});