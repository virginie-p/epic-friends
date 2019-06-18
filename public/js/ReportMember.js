$(function(){

    $('button[form="report-member"]').on('click', function(e){
        
        e.preventDefault();
        $('.message').remove();

        let reportReason = $('[name="report-reason"]').val();
        let regex = /^[\s\n]+$/;
        
        if(regex.test(reportReason)){
            $('.form-group').after('<div class="alert alert-danger message" role="alert">Votre message ne peut pas être composé uniquement d\'espaces</div>');
        } 
        else if(reportReason === "") {
            $('.form-group').after('<div class="alert alert-danger message" role="alert">Vous devez renseigner une raison pour pouvoir signaler un utilisateur</div>');
        }
        else if (reportReason.length > 255) {
            $('.form-group').after('<div class="alert alert-danger message" role="alert">La raison de votre signalement ne peut excéder 255 caractères.</div>');
        }
        else {
            let form = $('#report-member')[0];
            let formData = new FormData(form);

            $.ajax({
            url : $('form#report-member').attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false
            })
            .done(function(response){
                if (response.status === 'error') {
                    let errors = response.errors;
                    errors.forEach(error => {
                        if(error == 'user_not_found') {
                            $('.form-group').after('<div class="alert alert-danger message" role="alert">L\'utilisateur que vous souhaitez signaler n\'a pas été trouvé dans nos bases.</div>');
                        }
                        else if(error == 'user_not_reported') {
                            $('.form-group').after('<div class="alert alert-danger message" role="alert">Le signalement de cet utilisateur n\'a pas fonctionné. Merci de bien vouloir réessayer.</div>');
                        }
                        else if (error == 'user_already_reported') {
                            $('.form-group').after('<div class="alert alert-danger message" role="alert">Vous ne pouvez pas signaler à nouveau cet utilisateur !</div>');
                        }
                    });
                }
                else if (response.status === 'success') {
                    $('[name="report-reason"]').val('');
                    $('#report-modal').modal('hide');
                    $('button[data-target="#report-modal"]').remove();
                    $('#profile-jumbotron').parent().prepend('<div class="alert alert-warning my-2" role="alert">Cet utilisateur a bien été signalé à nos équipes. Nous serons peut-être amenés à vous recontacter pour obtenir plus d\'informations</div>')
                }

            });
        }

        

    })

});