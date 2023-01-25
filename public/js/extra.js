var contentSrc, // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal
    ceInitialContent, // Used for detecting changes of content-editable elements
    cTypeCode; // Used for toggling image file input in matter.classifiers

// Ajax fill an element from a url returning HTML
var fetchInto = async (url, element) => {
    response = await fetch(url);
    element.innerHTML = await response.text();
}

var reloadPart = async (url, partId) => {
    response = await fetch(url);
    let doc = new DOMParser().parseFromString(await response.text(), "text/html");
    document.getElementById(partId).innerHTML = doc.getElementById(partId).innerHTML;
}

// Perform REST operations with native JS
var fetchREST = async (url, method, body) => {
    response = await fetch(url, {
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
    },
    method: method,
    body: body
    });
    switch (response.status) {
    case 500:
        response.text().then(function (text) {
        alert("Unexpected result: " + text)
        });
        break;
    case 419:
        alert("Token expired. Refresh the page");
        break;
    default:
        return response.json();
    }
}

// Ajax fill the opened modal
$("#ajaxModal").on("show.bs.modal", function(event) {
    var modalTrigger = event.relatedTarget;
    contentSrc = modalTrigger.href;
    this.querySelector('.modal-title').innerHTML = modalTrigger.title;
    if (modalTrigger.hasAttribute('data-size')) {
    this.querySelector('.modal-dialog').classList.add(modalTrigger.dataset.size);
    }
    fetchInto(contentSrc, this.querySelector('.modal-body'));
});

// Display actor dependencies in corresponding tab
$(app).on("show.bs.tab", "#actorUsedInToggle", function (e) {
    fetchInto(e.target.href, actorUsedIn);
});

// Process click events

app.addEventListener('click', (e) => {
    if(e.target.matches('.sendDocument')) {
    formData = new FormData(sendDocumentForm);
    fetchREST(e.target.closest('[data-resource]').dataset.resource, 'POST', formData)
        .then(data => {
        if (data.message) {
            alert(data.message);
        } else {
            document.location.href = data.mailto;
            e.target.closest('tr').remove();
        }
        });
    }

    if(e.target.matches('.chooseTemplate')) {
    var form_tr = document.getElementById('selectTrForm');
    if (form_tr == null) {
        var select = document.createElement('tr');
        select.setAttribute('id', 'selectTrForm');
        var currentTr = e.target.closest('tr');
        var parent = currentTr.parentNode;
        var next_sib = currentTr.nextSibling;
        if (next_sib) {
        parent.insertBefore(select, next_sib);
        } else {
        parent.appendChild(select);
        }
        fetchInto(e.target.dataset.url, select);
    } else {
        form_tr.remove();
    }
    }

    switch (e.target.id) {
    case 'createMatterSubmit':
        submitModalForm('/matter', createMatterForm);
        break;

    case 'deleteMatter':
        if (confirm("Deleting the matter. Continue anyway?")) {
        fetchREST(e.target.closest('[data-resource]').dataset.resource, 'DELETE')
            .then(data => {
            if (data.message) {
                alert(data.message);
            } else {
                location.href = document.referrer;
            }
            });
        }
        break;

        // Specific processing in the task list modal
    case 'addTaskToEvent':
        e.target.closest('tbody').insertAdjacentHTML('beforeend', addTaskFormTemplate.innerHTML);
        addTaskForm['trigger_id'].value = e.target.dataset.event_id;
        break;

    case 'addTaskSubmit':
        submitModalForm('/task', addTaskForm, 'reloadModal');
        break;

    case 'deleteEvent':
        if (confirm("Deleting the event will also delete the linked tasks. Continue anyway?")) {
        fetchREST('/event/' + e.target.dataset.event_id, 'DELETE')
            .then(() => fetchInto(contentSrc, ajaxModal.querySelector('.modal-body')));
        }
        break;

        // Specific processing of the event list modal
    case 'addEventSubmit':
        submitModalForm('/event', addEventForm, 'reloadModal');
        break;

        // Classifier list modal
    case 'addClassifierSubmit':
        submitModalForm('/classifier', addClassifierForm, 'reloadModal');
        break;

        // Generic processing of deletions
    case 'deleteTask':
    case 'deleteClassifier':
    case 'removeActor':
        fetchREST(e.target.closest('[data-resource]').dataset.resource, 'DELETE')
        .then(() => fetchInto(contentSrc, ajaxModal.querySelector('.modal-body')));
        break;

    case 'deleteTemplate':
        fetchREST(e.target.closest('[data-resource]').dataset.resource, 'DELETE')
        .then(() => fetchInto(contentSrc, app.querySelector('.reload-part')));
        break;

    case 'nationalizeSubmit':
        submitModalForm('/matter/storeN', natMatterForm);
        break;

    case 'createFamilySubmit':
        submitModalForm('/matter/storeFamily', createMatterForm);
        break;

    case 'createActorSubmit':
        submitModalForm('/actor', createActorForm);
        break;

    case 'createUserSubmit':
        submitModalForm('/user', createUserForm);
        break;

    case 'createDActorSubmit':
        submitModalForm('/default_actor', createDActorForm);
        break;

    case 'createEventNameSubmit':
        submitModalForm('/eventname', createEventForm);
        break;

    case 'createCategorySubmit':
        submitModalForm('/category', createCategoryForm);
        break;

    case 'createRoleSubmit':
        submitModalForm('/role', createRoleForm);
        break;

    case 'createTypeSubmit':
        submitModalForm('/type', createTypeForm);
        break;

    case 'createRuleSubmit':
        submitModalForm('/rule', createRuleForm);
        break;

    case 'createFeeSubmit':
        submitModalForm('/fee', createFeeForm);
        break;

    case 'createClassSubmit':
        submitModalForm('/document', createClassForm);
        break;

    case 'createMemberSubmit':
        submitModalForm('/template-member', createMemberForm);
        break;

    case 'createClassifierTypeSubmit':
        submitModalForm('/classifier_type', createClassifierTypeForm);
        break;

    case 'sendDocument':
        submitModalForm('/document', sendDocumentForm);
        break;

    case 'addEventTemplateSubmit':
        submitModalForm('/event-class', addTemplateForm,'reloadPartial');
        break;

    case 'addRuleTemplateSubmit':
        submitModalForm('/rule-class', addTemplateForm,'reloadPartial');
        break;

    case 'deleteActor':
    case 'deleteRule':
    case 'deleteEName':
    case 'deleteRole':
    case 'deleteType':
    case 'deleteDActor':
    case 'deleteClassifierType':
    case 'deleteCategory':
    case 'deleteClass':
    case 'deleteMember':
        if (confirm("Deleting  "+   e.target.dataset.message + ". Continue anyway?")) {
        fetchREST(e.target.dataset.url, 'DELETE')
            .then(data => {
            if (data.message) {
                alert("Couldn't delete " + e.target.dataset.message+ ". Check the dependencies. Database said: " + data.message);
                return false;
            } else {
                location.reload();
            }
            });
        }
        break;

    case 'regenerateTasks':
        if (confirm("Regenerating the tasks will delete all the existing automatically created tasks and renewals for this event.\nPast tasks will not be recreated - make sure they have been dealt with.\nContinue anyway?")) {
        fetchREST('/event/' + e.target.dataset.event_id + '/recreateTasks', 'POST')
            .then(() => fetchInto(contentSrc, ajaxModal.querySelector('.modal-body')));
        }
        break;
    }

    /* Various functions used here and there */

    // Nationalize modal
    if (e.target.matches('#ncountries .btn-outline-danger')) {
    e.target.parentNode.parentNode.remove();
    }

    // Highlight the selected list item and load panel
    if (e.target.hasAttribute('data-panel')) {
    e.preventDefault();
    let markedRow = e.target.closest('tbody').querySelector('.table-info');
    if (markedRow) {
        markedRow.classList.remove('table-info');
    }
    e.target.closest('tr').classList.add('table-info');
    contentSrc = e.target.href;
    let panel = document.getElementById(e.target.dataset.panel);
    fetchInto(e.target.href, panel);
    }
});

app.addEventListener("change", e => {
    if (e.target.matches(".noformat")) {
    // Generic in-place edition of input fields
    if (e.target.hasAttribute('data-ac')) {
        // Destroy autocomplete widget
        $(e.target).autocomplete('destroy');
    }
    let params = new URLSearchParams();
    if (e.target.type === 'checkbox') {
        if (e.target.checked) {
        e.target.value = 1;
        } else {
        e.target.value = 0;
        }
    }
    params.append(e.target.name, e.target.value);
    let resource = e.target.closest('[data-resource]').dataset.resource;
    fetchREST(resource, 'PUT', params)
        .then(data => {
            if (data.errors) {
            if (ajaxModal.matches('.show')) {
                footerAlert.innerHTML = Object.values(data.errors)[0];
                footerAlert.classList.add('alert-danger');
            } else {
                e.target.classList.remove('border-info', 'is-valid');
                e.target.classList.add('border-danger');
                e.target.value = Object.values(data.errors)[0];
            }
            } else if (data.message) {
            if (ajaxModal.matches('.show')) {
                footerAlert.innerHTML = data.message;
                footerAlert.classList.add('alert-danger');
            } else {
                e.target.classList.remove('border-info', 'is-valid');
                e.target.classList.add('border-danger');
                e.target.value = 'Invalid';
                console.log(data.message);
            }
            } else {
            if (!window.ajaxPanel && contentSrc.length !== 0 && !e.target.closest('.tab-content')) {
                // Reload modal with updated content
                fetchInto(contentSrc, ajaxModal.querySelector(".modal-body"));
            } else {
                // Don't reload but set border back to normal
                e.target.classList.remove('border-info', 'border-danger');
                e.target.classList.add('is-valid');
            }
            footerAlert.classList.remove("alert-danger");
            footerAlert.innerHTML = "";
            }
        })
        .catch(error => console.log(error));
    }
    // matter.classifiers addClassifierForm - replace input fields with file upload field when selecting an image type
    if (e.target.dataset.actarget === 'type_code' && e.target.value === 'Image') {
    for (elt of addClassifierForm.getElementsByClassName('hideForFile')) {
        elt.classList.add('d-none');
    }
    forFile.classList.remove('d-none');
    cTypeCode = 'IMG'
    }
    if (e.target.dataset.actarget === 'type_code' && e.target.value !== 'Image' && cTypeCode === 'IMG') {
    for (elt of addClassifierForm.getElementsByClassName('hideForFile')) {
        elt.classList.remove('d-none');
    }
    forFile.classList.add('d-none');
    cTypeCode = ''
    }
});

// Reset ajaxModal to default when it is closed
$('#ajaxModal').on("hidden.bs.modal", function(event) {
    this.querySelector('.modal-body').innerHTML = '<div class="spinner-border" role="status"></div>';
    this.querySelector('.modal-title').innerHTML = "Ajax title placeholder";
    this.querySelector('.modal-dialog').className = "modal-dialog";
    footerAlert.innerHTML = "";
    footerAlert.classList.remove('alert-danger');
});
// Process modified input fields
app.addEventListener("input", e => {
    // Mark the field
    if (e.target.matches(".noformat, textarea, [contenteditable]")) {
    e.target.classList.add("border", "border-info");
    } else if (e.target.matches('.filter')) {
        // Manage filters input fields in Email template selection box
    var url = new URL(window.location.origin + e.target.closest('[data-resource]').dataset.resource);
    if (e.target.value.length === 0) {
        url.searchParams.delete(e.target.name);
    } else {
        url.searchParams.set(e.target.name, e.target.value);
    }
        reloadPart(url, 'tableList');
    }
});

app.addEventListener("focusout", e => {
    if (e.target.matches("[contenteditable]") && e.target.innerText !== ceInitialContent) {
    let params = new URLSearchParams();
    params.append(e.target.dataset.name, e.target.innerText);
    let resource = e.target.closest('[data-resource]').dataset.resource;
    fetchREST(resource, 'PUT', params)
    .then(data => {
        e.target.classList.remove('border-info');
    })
    }
});

app.addEventListener("focusin", e => {
    if (e.target.matches("[contenteditable]")) {
    ceInitialContent = e.target.innerText;
    }

    if ( e.target.hasAttribute('data-ac') ) {
    // Process autocomplete fields
    var aclength = 1;
    if ( e.target.hasAttribute('data-aclength') ) {
        aclength = e.target.dataset.aclength;
    }
    if (e.target.closest('tr')) {
        e.target.closest('tr').classList.add('ui-front');
    }
    $(e.target).autocomplete({
        autoFocus: true,
        minLength: aclength,
        source: e.target.dataset.ac,
        // create: function(event, ui) {
        //   // Fires search immediately (but selection does not trigger blur or change)
        //   if ( aclength == 0 ) {
        //     $(this).autocomplete("search", "");
        //   }
        // },
        select: (event, ui) => {
        if (e.target.id == 'addCountry') {
            let newCountry = appendCountryTemplate.content.children[0].cloneNode(true);
            newCountry.id = 'country-' + ui.item.key;
            newCountry.children[0].value = ui.item.key;
            newCountry.children[1].value = ui.item.value;
            ncountries.appendChild(newCountry);
            // Wait for the new country entry to be added to the DOM before resetting the input field
            setTimeout(() => { addCountry.value = ""; }, 0);
        } else if ( e.target.hasAttribute('data-actarget') ) {
            // Used for static forms where the human readable value is displayed and the id is sent to the server via a hidden input field
            e.target.value = ui.item.value;
            e.target.form[e.target.dataset.actarget].value = ui.item.key;
            if (window.createMatterForm && e.target.dataset.actarget == 'category_code') {
            // We're in a matter creation form - fill caseref with corresponding new value
            fetchREST('/matter/new-caseref?term=' + ui.item.prefix, 'GET')
            .then(data => {
                createMatterForm.caseref.value = data[0].value;
            });
            }
        } else {
            // Used for content editable fields where the same field is used for sending the id to the server
            e.target.value = ui.item.key;
            e.target.blur();
        }
        },
        change: function(event, ui) {
        if (!ui.item) {
            // User has not selected anything
            e.target.value = "";
            if (e.target.form && e.target.hasAttribute('data-actarget')) {
            e.target.form[e.target.dataset.actarget].value = "";
            }
        }
        }
    });
    }
});

var submitModalForm = (target, Form, after) => {
    formData = new FormData(Form);
    params = new URLSearchParams(formData);
    fetchREST(target, 'POST', formData)
    .then(data => {
        if (data.errors) {
        footerAlert.innerHTML = data.message;
        footerAlert.classList.add('alert-danger');
        processSubmitErrors(data.errors, Form);
        } else if (data.redirect) {
        // Redirect to the created model (link returned by the controller store() function)
        location.href = data.redirect;
        } else {
        if (after === 'reloadModal') {
            fetchInto(contentSrc, ajaxModal.querySelector('.modal-body'));
        } else if (after === 'reloadPartial') {
            fetchInto(contentSrc, app.querySelector('.reload-part'));
            } else { // reloadPage
            location.reload();
        }
        }
    })
    .catch(error => {
        console.log(error);
    });
}

var processSubmitErrors = (errors, Form) => {
    Object.entries(errors).forEach(([key, value]) => {
    let inputElt = Form.querySelector('[data-actarget="' + key + '"]');
    if (!inputElt) {
        inputElt = Form.elements[key];
    }
    if (inputElt.type === 'file') {
        footerAlert.append(' ' + value[0]);
    } else {
        inputElt.placeholder = key + ' is required';
    }
    inputElt.classList.add('is-invalid');
    });
}

// Drag and drop sorting functionality (see roleActors)
var dragItem;

ajaxModal.addEventListener('dragstart', e => {
    e.dataTransfer.dropEffect = "move";
    e.dataTransfer.setData("text/plain", null);
    dragItem = e.target.parentNode;
    e.target.classList.replace('bg-light', 'bg-info');
});

ajaxModal.addEventListener('dragover', e => {
    let destination = e.target.closest(dragItem.tagName);
    if (destination) {
    if (dragItem.rowIndex > destination.rowIndex) {
        destination.parentNode.insertBefore(dragItem, destination);
    } else {
        destination.parentNode.insertBefore(dragItem, destination.nextSibling);
    }
    }
});

ajaxModal.addEventListener('drop', e => {
    e.preventDefault();
});

ajaxModal.addEventListener('dragend', e => {
    for (tr of dragItem.parentNode.children) {
    if (tr.rowIndex != tr.dataset.n) {
        let display_order = tr.querySelector('[name="display_order"]');
        display_order.value = tr.rowIndex;
        tr.dataset.n = tr.rowIndex;
        let params = new URLSearchParams();
        params.append('display_order', display_order.value);
        fetchREST(tr.dataset.resource, 'PUT', params);
    };
    }
    dragItem = "";
});