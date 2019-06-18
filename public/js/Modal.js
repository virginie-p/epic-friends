$(function() {
    
    $('a[data-action="delete"]').click(function() {
        let elementAttrID =$(this).attr('id');
        let elementData = elementAttrID.split('-')
        let elementType = elementData[0];
        let elementID = elementData[1];
        let message;

        if ($(this).attr('data-type') == 'centre d\'intérêt'){
            message = 'ce centre d\'intérêt';
        }
        else {
            message = 'cet utilisateur';
        }
    
        if ($(this).attr('data-action') == 'delete'){
            let modal = new Modal( 
                'delete',
                elementType,
                elementID,
                'Confirmation de suppression',
                message
            );
            
            modal.addSpecificities();
    
            $('button[type="submit"], button[data-dismiss="modal"], .modal').click(function(e){
                if(e.currentTarget === e.target){
                    modal.deleteSpecificities();
                }
            });
    
        } 
    });
    
    class Modal {
        constructor(action, type, id, title, message) {
            this.action = action;
            this.type = type;
            this.title = title;
            this.message = message;
            this.id = id;
        }
        
        addSpecificities() {
            $('.modal').attr('id', this.action + 'Modal');
            $('.modal-footer>a').attr('href', (`${baseUrl}/${this.action}/${this.type}/${this.id}`));
            $('#modal-title').append(this.title);
            $('.form-check').append(`<p>Je souhaite définitivement supprimer ${this.message}.</p><div class="alert alert-danger" role="alert">Attention, toute suppression est irréversible !</div>`);
        }
    
        deleteSpecificities() {
            $('.modal').removeAttr('id');
            $('.modal-footer>a').removeAttr('href');
            $('#modal-title').empty();
            $('.form-check').empty();
        }
    }
    
});