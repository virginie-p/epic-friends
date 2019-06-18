$(function() {
    $('a#delete-user, a#delete-interest').on('click', function(e){
        e.preventDefault();
        $('.messages').remove();

        $.ajax({
            url : $(e.target).attr('href'),
            type: 'GET',
            dataType: 'json',
            processData: false,
            contentType: false
        })
        .done(function(response){
    
            if (response.status === 'error') {
                let errors = response.errors;
                errors.forEach(error => {
                    if(error == 'user_not_found') {
                        $('h2').parent().after('<div class="alert alert-danger message" role="alert">L\'utilisateur que vous souhaitez supprimer n\'a pas été trouvé dans nos bases.</div>');
                    }
                    else if(error == 'user_not_deleted') {
                        $('h2').parent().after('<div class="alert alert-danger message" role="alert">La suppression de cet utilisateur n\'a pas fonctionné. Merci de bien vouloir réessayer.</div>');
                    }
                    else if (error == 'interest_not_found') {
                        $('h2').parent().after('<div class="alert alert-danger message" role="alert">L\'intérêt que vous souhaitez supprimer n\'a pas été trouvé dans nos bases.</div>');
                    }
                    else if(error == 'interest_not_deleted') {
                        $('h2').parent().after('<div class="alert alert-danger message" role="alert">La suppression de ce centre d\'intéret n\'a pas fonctionné. Merci de bien vouloir réessayer.</div>');
                    }
                });
            }
            else if (response.status === 'success') {
                $(`[data-user-id = ${response.member_deleted}], [data-interest-id = ${response.interest_deleted}]`).remove();
                $('h2').parent().after('<div class="alert alert-success message" role="alert">L\'utilisateur a bien été supprimé.</div>');

            }
        });
    });
});