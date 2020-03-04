export function showBusySpinner(){
    $('#tMainSpinner').css('display', 'flex');
}

export function hideBusySpinner(){
    $('#tMainSpinner').fadeOut(400);
}
