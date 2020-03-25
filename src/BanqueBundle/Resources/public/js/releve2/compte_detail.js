/**
 * Created by SITRAKA on 28/11/2018.
 */
$(document).ready(function(){
    $(document).on('click','.cl_compte_detail',function(){
        var id = $(this).attr('data-id'),
            type = parseInt($(this).attr('data-type'));

        var exercices = [],
            mois = [],
            cloture = parseInt($('#id_cloture_dossier').attr('data-cloture')),
            titre = 'Grand Livre';
        if (isNaN(cloture)) cloture = 12;
        var periodes = get_periodes(cloture);
        exercices.push($('#exercice').val());
        show_compte_details(id,type,exercices,mois,periodes,titre);
        eb_set_class_table(exercices);
    });

    $(document).on('click','.pi',function(){
        var image_id = $(this).parent().find('.ip').text().trim();
        if(image_id === '') show_info('ERREUR','CET PIECE N EST PAS ACCESSIBLE','error');
        else show_image_pop_up(image_id);
    });
});

function compte_formatter(v)
{
    if (v === null) return '';
    else if (Array.isArray(v) && v.length > 1)
    {
        var qtip = "<table class='table'>";
        for (var i = 0; i < v.length; i++)
        {
            qtip += '<tr>';
                qtip += '<td>'+v[i].l+'</td>';
                qtip += '<td>'+v[i].i+'</td>';
            qtip += '</tr>';
        }
        qtip += '</table>';
        return '<span class="pointer qtip_new" title="'+qtip+'">multiple</span>';
    }
    else if (Array.isArray(v) && v.length === 1)
        v = v[0];

    return '<span class="pointer cl_compte_detail" data-id="'+v.id+'" data-type="'+v.t+'">'+(v.l.toString().trim() === '' ? '-' : v.l)+'</span>';
}

function get_periodes(cloture)
{
    var periodes = [],
        first_mois = cloture;

    while (periodes.length < 12)
    {
        first_mois++;
        if (first_mois === 13) first_mois = 1;
        var mois_str = ((first_mois < 10) ? '0' : '') + first_mois.toString();
        periodes.push({'libelle':get_mois_libelle(first_mois),'moiss':[mois_str]});
    }

    return periodes;
}

function get_mois_libelle(mois)
{
    var moisLibelle = [
        'JAN',
        'FEV',
        'MAR',
        'AVR',
        'MAI',
        'JUI',
        'JUL',
        'AOU',
        'SEP',
        'OCT',
        'NOV',
        'DEC'
    ];

    return moisLibelle[mois - 1];
}

function gl_col_mod(w)
{
    var colSolde = $('#id_simple').is(':checked');
    var colM = [];
    if(typeof w !== 'undefined')
    {
        colM.push({ name:'p', index:'p', width:  w * 10 / 100, hidden: true,sorttype:'integer' });
        colM.push({ name:'de', index:'de', width:  w * 10 / 100, sortable: false });
        colM.push({ name:'j', index:'j', width:  w * 3 / 100, sortable: false });
        colM.push({ name:'pi', index:'pi', width:  w * 10 / 100, classes: 'pi', sortable: false});
        colM.push({ name:'ip', index:'ip', hidden:true, width:  0, classes:'ip' });
        colM.push({ name:'l', index:'l', width:  w * 35 / 100, sortable: false });

        if (!colSolde)
        {
            colM.push({ name:'d', index:'d', width:  w * 10 / 100, align:'right', sortable: false, formatter: function (v) { return number_format(v,2,',','\x20');} });
            colM.push({ name:'c', index:'c', width:  w * 10 / 100, align:'right', sortable: false, classes:'text-danger', formatter: function (v) { return number_format(v,2,',','\x20');} });
        }

        colM.push({ name:'lt', index:'lt', width:  w * 2 / 100, sortable: false });

        if (!colSolde)
        {
            colM.push({ name:'sd', index:'sd', width:  w * 10 / 100, sortable: false, align:'right',formatter: function (v) { return number_format(v,2,',','\x20');} });
            colM.push({ name:'sc', index:'sc', width:  w * 10 / 100, sortable: false, align:'right', classes:'text-danger', formatter: function (v) { return number_format(v,2,',','\x20');} });
        }
        else
        {
            colM.push({ name:'s', index:'s', width:  w * 10 / 100, sortable: false, classes:'cl_solde', align:'right',formatter: function (v) { return number_format(v,2,',','\x20');} });
        }
        colM.push({ name:'cp', index:'cp', hidden:true, width:  0 });
    }
    else
    {
        colM = [
            'N',
            'Date',
            'Jnl',
            'Piece',
            '',
            'Libelle'];

        if (!colSolde)
        {
            colM.push('Debit');
            colM.push('Credit');
        }

        colM.push('L');
        if (!colSolde)
        {
            colM.push('solde Debit');
            colM.push('solde Credit');
        }
        else colM.push('solde');
        colM.push('Compte');
    }
    return colM;
}

function eb_set_class_table(exercices)
{
    var i;
    //compte cliquable
    $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_compte"]')
        .addClass(eb_get_class_compte()+' js_show_detail_compte');
    $('.js_show_detail_compte').each(function(){
        $(this)
            .attr('data-id',$(this).closest('tr').find('td[aria-describedby="js_eb_table_to_grid_js_eb_id_compte"]').text())
            .attr('data-type',$(this).closest('tr').find('td[aria-describedby="js_eb_table_to_grid_js_eb_est_tiers"]').text());
    });

    $('#jqgh_js_eb_table_to_grid_js_eb_compte').addClass(eb_get_class_compte());
    //piece
    $('#js_eb_table_to_grid tr td span.js_show_image').each(function(){
        $(this).parent().attr('data-id_image',$(this).attr('data-id_image'))
            .addClass(eb_get_class_piece() + ' pointer js_show_image')
            .html($(this).html().trim());
    });
    $('#jqgh_js_eb_table_to_grid_js_eb_piece').addClass(eb_get_class_piece());
    //solde credit
    for(i = 0;i<exercices.length;i++)
    {
        $('#jqgh_js_eb_table_to_grid_js_eb_solde_credit_' + exercices[i]).addClass(eb_get_class_credit());
        $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_solde_credit_'+ exercices[i] +'"]').addClass(eb_get_class_credit());
    }
    //credit
    $('#jqgh_js_eb_table_to_grid_js_eb_credit').addClass(eb_get_class_credit());
    $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_credit"]').addClass(eb_get_class_credit());

    $('.cl_solde').each(function(){
        if (parseInt($(this).text()) < 0) $(this).addClass('text-danger');
    });
}

function eb_get_class_solde()
{
    return 'fa-check';
}

function eb_get_class_compte()
{
    return 'text-success pointer';
}

function eb_get_class_credit()
{
    return 'text-danger';
}

function eb_get_class_piece()
{
    return 'text-info pointer';
}
