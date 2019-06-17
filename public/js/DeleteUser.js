$(function() {
    $('a#delete-user').on('click', function(e){
        e.preventDefault();

        $.ajax({
            url : $('a#delete-user').attr('href'),
            type: 'GET',
            dataType: 'json',
            processData: false,
            contentType: false
        })
        .done(function(response){
            $('#deleteModal').modal('hide');
            if (response.status === 'error') {
                let errors = response.errors;
                errors.forEach(error => {
                    if(error == 'user_not_found') {
                        $('h2').parent().after('<div class="alert alert-danger message" role="alert">L\'utilisateur que vous souhaitez signaler n\'a pas été trouvé dans nos bases.</div>');
                    }
                    else if(error == 'user_not_deleted') {
                        $('h2').parent().after('<div class="alert alert-danger message" role="alert">La suppression de cet utilisateur n\'a pas fonctionné. Merci de bien vouloir réessayer.</div>');
                    }

                });
            }
            else if (response.status === 'success') {
                $(`[data-user-id = ${response.member_deleted}]`).remove();
                $('h2').parent().after('<div class="alert alert-success message" role="alert">L\'utilisateur a bien été supprimé.</div>');

            }
        });
    });
});