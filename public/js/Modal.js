$(function() {

    const capitalize = (s) => {
      if (typeof s !== 'string') return ''
      return s.charAt(0).toUpperCase() + s.slice(1)
    }
    
    $('a[data-action="delete"]').click(function() {
        let elementAttrID =$(this).attr('id');
        let elementData = elementAttrID.split('-')
        let elementType = elementData[0];
        let elementID = elementData[1];
    
        if ($(this).attr('data-action') == 'delete'){
            let modal = new Modal( 
                'delete',
                elementType,
                elementID,
                'Confirmation de suppression',
                '<p>Je souhaite définitivement supprimer cet utilisateur.</p><div class="alert alert-danger" role="alert">Attention, toute suppression est irréversible !</div>'
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
            $('.form-check').append(this.message);
        }
    
        deleteSpecificities() {
            $('.modal').removeAttr('id');
            $('.modal-footer>a').removeAttr('href');
            $('#modal-title').empty();
            $('.form-check').empty();
        }
    }
    
});