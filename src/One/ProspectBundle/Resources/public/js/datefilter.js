/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Initialise les champs date
 * @returns {undefined}
 */
function initDateField() {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'fr',
        autoClose: true,
        todayHighlight: true,
        toggleActive: true
    });
}

/**
 * Ouvre le datepicker au clic du calendrier
 * @param {dom} el
 * @returns {undefined}
 */
function openDatepicker(el) {
    var parent = $(el).parent('div.date');
    parent.find('input').focus();
}

/**
 * Toutes les dates
 * @returns {undefined}
 */
function setAllDates() {
    $('#startdate, .startdate').val('');
    $('#enddate, .enddate').val('');
}

/**
 * Aujourd'hui
 * @returns {undefined}
 */
function setToday() {
    $('#startdate, .startdate').val(moment().startOf('day').format('L'));
    $('#enddate, .enddate').val(moment().endOf('day').format('L'));
}

/**
 * Semaine dernière
 * @returns {undefined}
 */
function setLastWeek() {
    $('#startdate, .startdate').val(moment().subtract(1, 'weeks').startOf('week').format('L'));
    $('#enddate, .enddate').val(moment().subtract(1, 'weeks').endOf('week').format('L'));
}

/**
 * Cette semaine
 * @returns {undefined}
 */
function setThisWeek() {
    $('#startdate, .startdate').val(moment().startOf('week').format('L'));
    $('#enddate, .enddate').val(moment().endOf('week').format('L'));
}

/**
 * Semaine prochaine
 * @returns {undefined}
 */
function setNextWeek() {
    $('#startdate, .startdate').val(moment().add(1, 'weeks').startOf('week').format('L'));
    $('#enddate, .enddate').val(moment().add(1, 'weeks').endOf('week').format('L'));
}

/**
 * Mois dernier
 * @returns {undefined}
 */
function setLastMonth() {
    $('#startdate, .startdate').val(moment().subtract(1, 'months').startOf('month').format('L'));
    $('#enddate, .enddate').val(moment().subtract(1, 'months').endOf('month').format('L'));
}

/**
 * Ce mois
 * @returns {undefined}
 */
function setThisMonth() {
    $('#startdate, .startdate').val(moment().startOf('month').format('L'));
    $('#enddate, .enddate').val(moment().endOf('month').format('L'));
}

/**
 * Mois prochain
 * @returns {undefined}
 */
function setNextMonth() {
    $('#startdate, .startdate').val(moment().add(1, 'months').startOf('month').format('L'));
    $('#enddate, .enddate').val(moment().add(1, 'months').endOf('month').format('L'));
}

/**
 * Années dernière
 * @returns {undefined}
 */
function setLastYear() {
    $('#startdate, .startdate').val(moment().subtract(1, 'years').startOf('year').format('L'));
    $('#enddate, .enddate').val(moment().subtract(1, 'years').endOf('year').format('L'));
}

/**
 * Cette année
 * @returns {undefined}
 */
function setThisYear() {
    $('#startdate, .startdate').val(moment().startOf('year').format('L'));
    $('#enddate, .enddate').val(moment().endOf('year').format('L'));
}

function setPeriod(period) {
    $('#period').val(period);
    if (period === 'all' || period === '') {
        setAllDates();
    } else if (period === 'today') {
        setToday();
    } else if (period === 'lastweek') {
        setLastWeek();
    } else if (period === 'thisweek') {
        setThisWeek();
    } else if (period === 'nextweek') {
        setNextWeek();
    } else if (period === 'lastmonth') {
        setLastMonth();
    } else if (period === 'thismonth') {
        setThisMonth();
    } else if (period === 'nextmonth') {
        setNextMonth();
    } else if (period === 'lastyear') {
        setLastYear();
    } else if (period === 'thisyear') {
        setThisYear();
    }
    initDateField();
}