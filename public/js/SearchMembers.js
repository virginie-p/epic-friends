$(function(){

    class Research {

        search(form, offset) {
            const formData = new FormData(form);
            formData.append('offset', offset);

            $.ajax({
                url : $('#search-members').attr('action'),
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
                    this.displayErrors(errors);
                }
                else if (data.status === 'success') {
                    this.displayFirstResults(data);
                    this.loadMoreResults(data, formData);
                }
            }.bind(this));
        }

        displayErrors(errors, searchType) {
            $('#help-search-text').remove();
            errors.forEach(error => {
                if(error == 'gender_not_existing') {
                    $('select[name="gender"]').addClass('is-invalid');
                    $('select[name="gender"]').after('<div class="invalid-feedback messages">Le genre renseigné est incorrect.</div>')
                }
                else if(error == 'county_not_existing') {
                    $('input[name="county"]').addClass('is-invalid');
                    $('input[name="county"]').after('<div class="invalid-feedback messages">Ce département n\'existe pas. Merci de sélectionner un des départements qui vous est proposé.</div>');
                }
                else if(error == 'min_age_not_matching') {
                    $('input[name="age_range"]').addClass('is-invalid');
                    $('input[name="age_range"]').after('<div class="invalid-feedback messages">L\'âge minimum demandé n\'est pas correct.</div>');
                }
                else if(error == 'max_age_not_matching') {
                    $('input[name="age_range"]').addClass('is-invalid');
                    $('input[name="age_range"]').after('<div class="invalid-feedback messages">L\'âge maximum demandé n\'est pas correct.</div>');
                }
                else if(error == 'interest_does_not_exists') {
                    $('.selectize-input').css('border', 'solid 1px #dc3545');
                    $('select[name="interests[]"]').parent().append('<div class="invalid-select messages">Un des intérêts renseigné n\'existe pas dans nos bases. Merci de bien vouloir sélectionner les centres d\'intérêts depuis la liste prévue à cet effet.</div>');
                }
                else if(error == 'research_do_not_return_anything') {
                    $('#search-results').append(`
                    <div class="container pt-3">
                        <div class="alert alert-danger messages" role="alert">
                            Nous n\'avons trouvé aucun résultat ${searchType === 'load-more' ? 'supplémentaire' : ''} correspondant à votre recherche.
                        </div>
                    </div>`);
                }
            });
        }

        displayFirstResults(data){
            $('#help-search-text').remove();
            $('#search-results>.container-fluid').append('<div class="row justify-content-center text-center pt-3" id="searched-members"></div>');
            this.createMembersCards(data);
        }

        createMembersCards(data) {
            data.results.forEach(result => {
                $('#searched-members').append(`
                    <div class="card col-lg-3 col-md-5 p-0 mx-4 mb-4 member" >
                        <img src="${baseUrl}/public/images/profiles-pictures/${result.profile_picture}" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">${result.username}</h5>
                            <p class="card-text">${result.age} ans</p>
                            <a class="btn btn-primary" href="${baseUrl}member/${result.id}" role="button">Voir le profil</a>
                        </div>
                    </div>`);      
            });
        }

        loadMoreResults(data, formData){
            if ($('.member').length < data.results_number[0]) {
                let $loadMore = $(`<div id="load-more">`).addClass("row justify-content-center").html(`<a class="btn btn-primary mx-auto" href="#" role="button">
                Je veux en voir plus !</a>`);
                
                $('#searched-members').after($loadMore);

                $loadMore.on('click', function(e){
                    e.preventDefault();
                    if ($('.member').length == data.results_number[0]) {
                        $('#load-more').remove();
                    }
                    $('#search-engine').find('.is-invalid').removeClass('is-invalid');
                    $('.messages').remove();

                    let offset = $('.member').length;
                    formData.set('offset', offset);

                    $.ajax({
                        url : $('#search-members').attr('action'),
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false
                    })
                    .done(function(response){
                        let data = response;
                        let searchType = 'load-more';
                        if (data.status === 'error') {
                            let errors = data.errors;
                            this.displayErrors(errors, searchType);
                        }
                        else if (data.status === 'success') {
                            this.createMembersCards(data);
                        }
                    }.bind(this));
                }.bind(this));
            }
        }
    }


    $('#search-button').on('click', function(e){
        e.preventDefault();
        $('#search-engine').find('.is-invalid').removeClass('is-invalid');
        $('.messages').remove();
        $('#searched-members').remove();
        $('#load-more').remove();

        let form = $('#search-members')[0];
        let offset = 0;

        let research = new Research();
        research.search(form, offset);
        
    });
});

