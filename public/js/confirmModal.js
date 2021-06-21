
function confirmModal(title, content, url) {
    
    $('#generic_modal .modal-title').html(title);
    
    $('#generic_modal .modal-body').html('<p>' + content + '</p> ');
    
    $('#generic_modal .modal-footer').html(
        '<a class="btn btn-danger" href="' + url + '">Yes I am</a> ' +
        '<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>'
    );

    $('#generic_modal').modal('show');
}