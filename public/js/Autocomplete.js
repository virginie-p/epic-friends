$(function() {
    function displaySuggestions(input, liste, suggestionsId, suggestionElt)  {
        $('#' + suggestionsId).empty();

        $('#department').off();

        let $departmentNames = [];
        
        liste.forEach(department => {
            $departmentNames.push(department.nom);
            let $suggestion = $('<div>').addClass(suggestionElt).text(department.nom);
            $('#' + suggestionsId).append($suggestion);

            $suggestion.on('click', (e) => {
                $(input).val($(e.target).text());
                $('#' + suggestionsId).remove();
            });
        });

        $('#department').on('click', e => {
            if ($(e.currentTarget).id !== suggestionsId) {
                $('#' + suggestionsId).remove();
            }

            if ($.inArray($('#department-search').val(), $departmentNames) === -1 ) {
                $('#department-search').val('');
            }
            
        });
    }

    $('#department-search').on('input', function(e) {
        $('#suggestions-list').remove();
        $('#department').append('<div id="suggestions-list"></div>');
        let position = document.getElementById('department-search').offsetLeft;
        
        $('#suggestions-list').css('width', $(e.target).outerWidth());
        $('#suggestions-list').css('left', position);

        $.get(`https://geo.api.gouv.fr/departements?nom=${$('#department-search').val()}`, function(response) {
            if(response.length != 0) {
                displaySuggestions(e.target, response, 'suggestions-list', 'suggestion');
                $('#suggestions-list').css('top', ($('#department-search').parent().outerHeight()));
            }
            else {
                $('#suggestions-list').remove();
            }
            
        }, 'JSON');

    
    });

});