$(function(){
    let currDate = new Date();
    let currYear = currDate.getFullYear();
    let minDate = currDate.setFullYear(currYear-99);

    let today = new Date();
    let today_FR = today.toDateString();
    let birthdate = $('#datetimepicker5').attr('data-value');

    $('#datetimepicker5').datetimepicker({
        format: 'DD/MM/YYYY',
        locale: 'fr', 
        minDate: minDate,
        maxDate: today_FR,
        date: birthdate,
        widgetPositioning : {
            horizontal: 'right'
        }
    });
});