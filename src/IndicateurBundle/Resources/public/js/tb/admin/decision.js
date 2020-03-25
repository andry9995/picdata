$(document).ready(function(){
    /**
     *  add
     */
    $(document).on('click','#js_id_add_tb_decision',function(){
        $.ajax({
            data: { action:0, indicateur:$('.indicateur_edited').attr('data-id') },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_decision'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('.indicateur_edited').click();
                show_info('SUCCES','CONDITION BIEN AJOUTEE AVEC SUCCES');
            }
        });
    });

    /**
     *  edit
     */
    $(document).on('change','.js_cl_tb_decision_input',function(){
        var tr = $(this).closest('tr'),
            condition = tr.find('.js_cl_condition').val().replace(/\s/g, ''),
            point = parseFloat(tr.find('.js_cl_point').val().trim()),
            decision = tr.attr('data-id'),
            commentaire = tr.find('.js_cl_commentaire').val().trim();

        if (isNaN(point)) point = 0;
        $.ajax({
            data: {
                action:1,
                decision:decision,
                condition:condition,
                point:point,
                commentaire: commentaire
            },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_decision'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                //btn.closest('tr').remove();
                show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
            }
        });
    });

    /**
     *  delete
     */
    $(document).on('click','.js_cl_delete_tb_decision',function(){
        var btn = $(this);
        $.ajax({
            data: { action:2, decision:btn.closest('tr').attr('data-id') },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_decision'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                btn.closest('tr').remove();
                show_info('SUCCES','CONDITION BIEN SUPPRIMEE AVEC SUCCES');
            }
        });
    });

    $(document).on('click','.js_cl_icon',function(){
        var li = $(this);

        $.ajax({
            data: { action:3, decision:li.closest('tr').attr('data-id'), icon:li.attr('data-icon') },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_decision'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                li.closest('ul').find('li').each(function(){
                    $(this).removeClass('active');
                });
                li.addClass('active');
                li.closest('.btn-group').find('.js_cl_i_icon').empty().removeClass().addClass('js_cl_i_icon ' + li.attr('data-icon'));

                if (li.attr('data-icon') === 'NA') li.closest('.btn-group').find('.js_cl_i_icon').text('NA');
                else li.closest('.btn-group').find('.js_cl_i_icon').addClass('fa-2x');

                //btn.closest('tr').remove();
                show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
            }
        });
    });
});