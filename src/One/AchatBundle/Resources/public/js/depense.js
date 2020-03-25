function selectToRemoveDepense(el) {
    var uid = $(el).attr('id');
    var id = $(el).attr('class');
    $('#depense-to-remove').val(uid+':'+id);

    //Réinitialise
    $('.depense-list tbody tr').css('background', '#FFFFFF');
    $('.depense-list tbody tr').css('color', '#676a6c');

    $('.depense-list tbody tr#'+uid).css('background', '#1CB394');
    // $('.depense-list tbody tr#'+uid).css('color', '#FFFFFF');

    $('.depense-list tbody tr input').css('color', '#676a6c');
    $('.article-list tbody tr select').css('color', '#676a6c');
    $('.article-list tbody tr textarea').css('color', '#676a6c');
}


function removeDepense() {
    var item = $('#depense-to-remove').val();
    var uid = item.split(':')[0];
    var depenseid = parseInt(item.split(':')[1]);
    if (depenseid) {
        var output = '<input type="hidden" name="deleted-depenses[]" value="'+depenseid+'" />';
        $('#depenses-deleted').append(output);
    }
    $('.depense-list tbody tr#'+uid).remove();
    $('#depense-to-remove').val('');
    updateAmountTTC();
}

function addDepenseLine() {

    $.ajax({
        url: Routing.generate('one_depense_achat_add'),
        type: 'POST',
        dateType: 'json',
        data: {dossierId: $('#dossier').val()},
        success: function (response) {
            $('.depense-list tbody').append(response);
            $('#montant-ht').attr('readonly', true);
            $('#montant-ttc').attr('readonly', true);
            updateAmountTTC();
            closeModal();
        }
    });

}