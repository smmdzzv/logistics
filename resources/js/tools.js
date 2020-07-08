export function showBusySpinner() {
    $('#tMainSpinner').css('display', 'flex');
}

export function hideBusySpinner() {
    $('#tMainSpinner').fadeOut(400);
}

export function generateId() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

export function pad(number, size) {
    let s = number + "";
    while (s.length < size) s = s + '0';
    console.log(s, number)
    return s;
}
