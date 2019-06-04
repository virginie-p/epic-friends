$(function(){
    let currDate = new Date();
    let currYear = currDate.getFullYear();
    let minDate = currDate.setFullYear(currYear-99);

    let today = new Date();
    let today_FR = today.toDateString();


    $('#datetimepicker5').datetimepicker({
        format: 'DD/MM/YYYY',
        locale: 'fr', 
        minDate: minDate,
        maxDate: today_FR, 
        useCurrent: false, 
        widgetPositioning : {
            horizontal: 'right'
        }
    });
});