$(function() {
    $('#wModal-form').on('beforeSubmit', '#item-edit-form', function(e) {
        let $wmForm = $('#item-edit-form');
        $.ajax({
            url: $wmForm.attr('action'),
            type: 'POST',
            data: $wmForm.serialize(),
            success: function (res) {
                if (res.success) {
                    $('#wModal').modal('hide');
                    dataHandler.refreshListItems({id: res.id}); 
                    notifyMessageShow(res.message);
                    return false;
                }
                
                $('#wModal-form').html(res);
            },
            error: function(jqXHR, errMsg) {
                console.log(errMsg);
                alert(errMsg);
            }
        });
        return false;
    });
});