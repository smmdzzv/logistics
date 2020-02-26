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

async function getUnpaidOrderItems(orderId) {
    let action = `/order/${orderId}/unpaid-items`;
    return axios.get(action);
}

async function getOrderPayments(orderId) {
    let action = `/order/${orderId}/payments`;
    return axios.get(action);
}

function disableForm(){
    $( "input" ).prop( "disabled", true );
}


//Switch focus on enter
// register jQuery extension
jQuery.extend(jQuery.expr[':'], {
    focusable: function (el, index, selector) {
        return $(el).is('a, button, :input, [tabindex]');
    }
});

$(document).on('keypress', 'input,select', function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$(document).on('keyup', 'input,select', function (e) {
    if (e.which == 13) {
        e.preventDefault();
        // Get all focusable elements on the page
        switchFocus(this)
    }
});

function switchFocus(el) {
    // Get all focusable elements on the page
    let $canfocus = $(':focusable');
    let index = $canfocus.index(el) + 1;
    if(isHidden($canfocus.eq(index)) || isDummy($canfocus.eq(index)))
        index++;
    if (index >= $canfocus.length) index = 0;
    $canfocus.eq(index).focus();
}

function isHidden(el) {
    return $(el).hasClass('d-none');
}

function isDummy(el) {
    return $(el).hasClass('dummy');
}

function groupBy(list, keyGetter) {
    const map = new Map();
    list.forEach((item) => {
        const key = keyGetter(item);
        const collection = map.get(key);
        if (!collection) {
            map.set(key, [item]);
        } else {
            collection.push(item);
        }
    });
    return map;
}
