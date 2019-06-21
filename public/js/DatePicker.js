$(function(){
    let currDate = new Date();
    let currYear = currDate.getFullYear();
    let setDate = currDate.setFullYear(currYear-99);
    let minDate = moment(setDate);

    let today = new Date();
    let today_FR = moment(today);

    let birthdate = $('#datetimepicker5').attr('data-value');

    $('#datetimepicker5').datetimepicker({
        format: 'L',
        locale: 'fr',
        minDate: minDate,
        maxDate: today_FR,
        date: birthdate,
        widgetPositioning : {
            horizontal: 'right'
        }
    });
});