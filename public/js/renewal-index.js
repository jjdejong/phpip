var url = new URL(window.location.href);

function refreshList() {
    window.history.pushState('', 'phpIP', url);
    reloadPart(url, 'renewalList');
}

filterFields.addEventListener('input', debounce(e => {
    if (e.target.matches('.form-control')) {
        if (e.target.value.length === 0) {
            url.searchParams.delete(e.target.name);
        } else {
            url.searchParams.set(e.target.name, e.target.value);
        }
        url.searchParams.delete('page');
        refreshList();
    }
}, 500));

grace.onchange = e => {
    if (!e.target.checked) {
        url.searchParams.delete('grace_period');
    } else {
        url.searchParams.set('grace_period', "1");
    }
    refreshList();
}

selectAll.onchange = e => {
    if (e.target.checked) {
        // Check all checkboxes
        newValue = true;
    } else {
        // Uncheck all checkboxes
        newValue = false;
    }
    var boxes = document.getElementsByClassName('clear-ren-task');
    for (box of boxes) {
        box.checked = newValue;
    }
};

// Load list according to corresponding tab
tabsGroup.addEventListener("click", function (e) {
    url.searchParams.delete('step');
    url.searchParams.delete('invoice_step');
    url.searchParams.delete('page');
    if (e.target.hasAttribute('data-step')) {
        url.searchParams.set('step', e.target.dataset.step);
    }
    if (e.target.hasAttribute('data-invoice_step')) {
        url.searchParams.set('invoice_step', e.target.dataset.invoice_step);
    }
    // if (e.target.hasAttribute('href')) {
    //     url.searchParams.set('tab', e.target.getAttribute('href'));
    // }
    window.history.pushState('', 'phpIP', url);
    reloadPart(url, 'renewalList');
});

clearFilters.onclick = () => {
    for (key of url.searchParams.keys()) {
        if ((key != 'step') && (key != 'invoice_step')) {
            url.searchParams.delete(key);
        }
    }
    window.location.href = url.href;
};

doneRenewals.addEventListener("click", function (b) {
    msgAction = "resetting";
    actionRenewals(b.target, msgAction, '/renewal/done');
});

callRenewals.addEventListener("click", function (b) {
    msgAction = "call";
    actionRenewals(b.target, msgAction, '/renewal/call/1')
});

renewalsSent.addEventListener("click", function (b) {
    msgAction = "call";
    actionRenewals(b.target, msgAction, '/renewal/call/0')
});

renewalsExport.onclick = e => {
    // var tids = getSelected();
    // if (tids.length === 0) {
    //     alert("No renewals selected");
    //     return;
    // }
    // var task_ids = encodeURIComponent(JSON.stringify(tids));
    let exportUrl = '/renewal/export';
    e.preventDefault(); //stop the browser from following
    window.location.href = exportUrl;
};

renewalsInvoiced.addEventListener("click", function (b) {
    msgAction = "invoiced";
    actionRenewals(b.target, msgAction, '/renewal/invoice/0')
});

invoicesPaid.onclick = (b) => {
    msgAction = "paid";
    actionRenewals(b.target, msgAction, '/renewal/paid')
}

instructedRenewals.addEventListener("click", function (b) {
    msgAction = "for payment";
    actionRenewals(b.target, msgAction, '/renewal/topay')
});

lastReminderRenewals.addEventListener("click", function (b) {
    msgAction = "last call";
    actionRenewals(b.target, msgAction, '/renewal/lastcall')
});

reminderRenewals.addEventListener("click", function (b) {
    msgAction = "reminder";
    actionRenewals(b.target, msgAction, '/renewal/reminder')
});

abandonRenewals.addEventListener("click", function (b) {
    msgAction = "abandon renewals";
    actionRenewals(b.target, msgAction, '/renewal/abandon')
});

lapsedRenewals.addEventListener("click", function (b) {
    msgAction = "lapsed renewals";
    actionRenewals(b.target, msgAction, '/renewal/lapsing')
});

lapsingRenewals.addEventListener("click", function (b) {
    msgAction = "lapsed renewals";
    actionRenewals(b.target, msgAction, '/renewal/lapsing')
});

sendLapsedRenewals.addEventListener("click", function (b) {
    msgAction = "lapse communications sent";
    actionRenewals(b.target, msgAction, '/renewal/closing')
});

async function actionRenewals(button, msgAction, action_url) {
    // Active spinner
    button.insertAdjacentHTML('afterbegin', '<i class="spinner-border spinner-border-sm" role="status" />');
    var tids = getSelected();
    if (tids.length === 0) {
        var end = document.getElementById('Untildate').value;
        if (!end) {
            alert("No renewals selected for " + msgAction);
            // withdraw spinner and restore button
            button.removeChild(document.getElementsByClassName('spinner-border')[0]);
            return;
        }
        var begin = document.getElementById('Fromdate').value;
        var string = JSON.stringify({
            'begin': begin,
            'end': end
        });
    } else {
        var string = JSON.stringify({
            task_ids: tids
        });
    }
    context_url = new URL(window.location.href);
    await submitUpdate(string, action_url).catch(err => alert(err));
    window.history.pushState('', 'phpIP', context_url);
    reloadPart(context_url, 'renewalList');
    // withdraw spinner
    button.removeChild(document.getElementsByClassName('spinner-border')[0]);
}

function submitUpdate(string, url) {
    return new Promise(function (resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.head.querySelector("[name=csrf-token]").content);
        xhr.send(string);
        xhr.onload = function () {
            if (this.status === 200) {
                resolve(JSON.parse(this.responseText).success);
            }
            if (this.status === 419) {
                reject("Token expired. Refresh the page");
            }
            if (this.status === 404) {
                reject("No email template found - check that your templates match your client's language");
            } else {
                reject("Something went wrong");
            }
        }
    });
}

xmlRenewals.addEventListener("click", function () {
    var tids = getSelected();
    if (tids.length === 0) {
        alert("No renewals selected for order");
        return;
    }
    var string = JSON.stringify({
        task_ids: tids,
        clear: false
    });
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/renewal/order', true);
    xhr.setRequestHeader('Content-Type', 'application/json; charset=utf-8');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.head.querySelector("[name=csrf-token]").content);
    xhr.send(string);
    xhr.onload = function (e) {
        if (this.status == 200) {
            // Find file name
            var filename = xhr.getResponseHeader('Content-Disposition').split("filename=")[1];

            // The actual download by creating a link and clicking it programmatically
            var f = new File([xhr.response], filename, {
                type: xhr.getResponseHeader('Content-Disposition')
            });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(f);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else if (this.status == 501) {
            alert(JSON.parse(this.responseText).error)
        }
    }
});

function getSelected() {
    var tids = new Array();
    var boxes = document.getElementsByClassName('clear-ren-task');
    for (box of boxes) {
        if (box.checked) {
            tids.push(box.getAttribute('id'));
        }
    }
    return tids;
};