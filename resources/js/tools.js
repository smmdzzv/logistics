export function showBusySpinner(){
    $('#tMainSpinner').css('display', 'flex');
}

export function hideBusySpinner(){
    $('#tMainSpinner').fadeOut(400);
}

export function generateId() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}
