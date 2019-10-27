$(document).ready(() => {
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

//Orders
async function getUnpaidOrders(clientId) {
    let action = '/orders/' + clientId + '/unpaid';
    return axios.get(action);
}

async function getActiveOrders(clientId) {
    let action = '/orders/' + clientId + '/active';
    return axios.get(action);
}

async function getOrderItems(orderId) {
    let action = `/order/${orderId}/items`;
    return axios.get(action);
}
