
$(document).ready(()=>{
    tHideSpinner();

    window.addEventListener('beforeunload', (event) => {
        tShowSpinner();
    });
});



function tShowSpinner() {
    $('#tMainSpinner').css('display', 'flex');
}

function tHideSpinner() {
    $('#tMainSpinner').fadeOut(400);
}

function getBaseUrl() {
    let url = window.location;
    return url.protocol + "//" + url.host
}
