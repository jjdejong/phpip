let contentSrc, // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal
    ceInitialContent, // Used for detecting changes of content-editable elements
    cTypeCode; // Used for toggling image file input in matter.classifiers
   
// Ajax fill an element from a url returning HTML
let fetchInto = async (url, element) => {
    response = await fetch(url);
    element.innerHTML = await response.text();
}

let reloadPart = async (url, partId) => {
    response = await fetch(url);
    let doc = new DOMParser().parseFromString(await response.text(), "text/html");
    document.getElementById(partId).innerHTML = doc.getElementById(partId).innerHTML;
}

// Perform REST operations with native JS
let fetchREST = async (url, method, body) => {
    response = await fetch(url, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
        },
        method: method,
        body: body
    });
    switch (response.status) {
        //   case 500:
        //     response.text().then(function (text) {
        //       alert("Unexpected result: " + text)
        //     });
        //     break;
        case 419:
            alert("Token expired. Refresh the page");
            location.reload();
            break;
        default:
            return response.json();
    }
}

// Ajax fill the opened modal
$("#ajaxModal").on("show.bs.modal", function (event) {
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
    if (e.target.matches('.sendDocument')) {
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

    if (e.target.matches('.chooseTemplate')) {
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
            submitModalForm('/matter', createMatterForm, null, e.target);
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
            submitModalForm('/task', addTaskForm, 'reloadModal', e.target);
            break;

        case 'deleteEvent':
            if (confirm("Deleting the event will also delete the linked tasks. Continue anyway?")) {
                fetchREST('/event/' + e.target.dataset.event_id, 'DELETE')
                    .then(() => fetchInto(contentSrc, ajaxModal.querySelector('.modal-body')));
            }
            break;

            // Specific processing of the event list modal
        case 'addEventSubmit':
            submitModalForm('/event', addEventForm, 'reloadModal', e.target);
            break;

            // Classifier list modal
        case 'addClassifierSubmit':
            submitModalForm('/classifier', addClassifierForm, 'reloadModal', e.target);
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
            submitModalForm('/matter/storeN', natMatterForm, null, e.target);
            break;

        case 'createFamilySubmit':
            submitModalForm('/matter/storeFamily', createMatterForm, null, e.target);
            break;

        case 'createActorSubmit':
            submitModalForm('/actor', createActorForm, null, e.target);
            break;

        case 'createUserSubmit':
            submitModalForm('/user', createUserForm, null, e.target);
            break;

        case 'createDActorSubmit':
            submitModalForm('/default_actor', createDActorForm, null, e.target);
            break;

        case 'createEventNameSubmit':
            submitModalForm('/eventname', createEventForm, null, e.target);
            break;

        case 'createCategorySubmit':
            submitModalForm('/category', createCategoryForm, null, e.target);
            break;

        case 'createRoleSubmit':
            submitModalForm('/role', createRoleForm, null, e.target);
            break;

        case 'createTypeSubmit':
            submitModalForm('/type', createTypeForm, null, e.target);
            break;

        case 'createRuleSubmit':
            submitModalForm('/rule', createRuleForm, null, e.target);
            break;

        case 'createFeeSubmit':
            submitModalForm('/fee', createFeeForm, null, e.target);
            break;

        case 'createClassSubmit':
            submitModalForm('/document', createClassForm, null, e.target);
            break;

        case 'createMemberSubmit':
            submitModalForm('/template-member', createMemberForm, null, e.target);
            break;

        case 'createClassifierTypeSubmit':
            submitModalForm('/classifier_type', createClassifierTypeForm, null, e.target);
            break;

        case 'sendDocument':
            submitModalForm('/document', sendDocumentForm, null, e.target);
            break;

        case 'addEventTemplateSubmit':
            submitModalForm('/event-class', addTemplateForm, 'reloadPartial', e.target);
            break;

        case 'addRuleTemplateSubmit':
            submitModalForm('/rule-class', addTemplateForm, 'reloadPartial', e.target);
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
            if (confirm("Deleting  " + e.target.dataset.message + ". Continue anyway?")) {
                fetchREST(e.target.dataset.url, 'DELETE')
                    .then(data => {
                        if (data.message) {
                            alert("Couldn't delete " + e.target.dataset.message + ". Check the dependencies. Database said: " + data.message);
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
        // Delay to finish potential autocompletion process
        setTimeout(() => {
            // Generic in-place edition of input fields
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
                            // Trigger a xhrsent event for whoever wants to refresh a list
                            var event = new Event('xhrsent', { bubbles: true });
                            e.target.dispatchEvent(event);
                        }
                        footerAlert.classList.remove("alert-danger");
                        footerAlert.innerHTML = "";
                    }
                })
                .catch(error => console.log(error));
        });
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
$('#ajaxModal').on("hidden.bs.modal", function (event) {
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
    } else {
        if (e.target.classList.contains('is-invalid')) {
            e.target.classList.remove('is-invalid');
        }
    }
});

// New autocomplete
const autocomplete = AutocompleteWidget();
document.body.addEventListener('focusin', event => {
    // Attach autocompletion widget to a corresponding element
    if (event.target.hasAttribute('data-ac')) {
        autocomplete.attachWidget(event.target);
    }
    // Store the initial value of a content editable element
    if (event.target.matches("[contenteditable]")) {
        ceInitialContent = event.target.innerText;
    }
});

function AutocompleteWidget() {
    let minLength = 1;
    let sourceUrl = '';
    let itemSelected = false;
    let suggestionList = document.createElement('div');
    suggestionList.classList.add('autocomplete-list');
  
    function attachWidget(input) {
        const inputRect = input.getBoundingClientRect();
        const modal = input.closest('.modal');
        const modalRect = modal ? modal.getBoundingClientRect() : { top: 0, left: 0 };
        suggestionList.style.left = inputRect.left + 'px';
        suggestionList.style.top = modalRect.top + inputRect.bottom + 'px';
        suggestionList.style.width = inputRect.width + 'px';
        document.body.appendChild(suggestionList);
        minLength = parseInt(input.getAttribute('data-aclength')) || 1;
        sourceUrl = input.getAttribute('data-ac');

        input.addEventListener('input', inputHandler);
        input.addEventListener('change', changeHandler);
    }
  
    const inputHandler = (event) => {
        const term = event.target.value;
        itemSelected = false;
        suggestionList.innerHTML = '';
        //document.body.removeChild(suggestionList);

        if (term.length >= minLength) {
            fetchSuggestions(sourceUrl, term)
            .then(suggestions => displaySuggestions(suggestions, event.target));
        }
    }

    async function fetchSuggestions(url, term) {
        const separator = url.includes('?') ? '&' : '?';
        const fetchUrl = url + separator + 'term=' + encodeURIComponent(term);
    
        try {
            const response = await fetch(fetchUrl);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching suggestions:', error);
            return [];
        }
    }
  
    function displaySuggestions(suggestions, input) {
        let listWidth = 100;
        suggestions.forEach(suggestion => {
            const listItem = document.createElement('div');
            listItem.classList.add('autocomplete-item');
            // Handle situation where the ajax data returns a label that should be displayed instead of the value
            if (suggestion.label) {
                listItem.textContent = suggestion.label;
            } else {
                listItem.textContent = suggestion.value;
            }
            listItem.dataset.key = suggestion.key;

            // Use "mousedown" rather than "click" to obtain immediate action when an item is selected
            listItem.addEventListener('mousedown', (event) => handleSelectedItem(event, suggestion, input), true);
            
            // Calculate an approximate width based on the number of characters
            const listItemWidth = suggestion.value.length * 8;
            listWidth = Math.max(listWidth, listItemWidth);
            suggestionList.appendChild(listItem);
        });
        suggestionList.style.width = listWidth + 'px';
        //document.body.appendChild(suggestionList);
    }

    const changeHandler = (event) => {
        // Delay to first handle a potential item selection that sets the itemSelected flag
        setTimeout(() => {
            // Clear the input if nothing is selected and the value was changed manually
            if (!itemSelected) {
                event.target.value = '';
                const targetName = event.target.getAttribute('data-actarget');
                if (targetName) {
                    const acTarget = event.target.parentNode.querySelector(`input[name="${targetName}"]`);
                    acTarget.value = '';
                }
            }
        });
    }

    function handleSelectedItem(event, selectedItem, input) {
        itemSelected = true;
        suggestionList.innerHTML = '';
        if (input.id == 'addCountry') {
            let newCountry = appendCountryTemplate.content.children[0].cloneNode(true);
            newCountry.id = 'country-' + selectedItem.key;
            newCountry.children[0].value = selectedItem.key;
            newCountry.children[1].value = selectedItem.value;
            ncountries.appendChild(newCountry);
            // Wait for the new country entry to be added to the DOM before resetting the input field
            setTimeout(() => {
                addCountry.value = "";
            }, 0);
        } else if (input.hasAttribute('data-actarget')) {
            // Used for static forms where the human readable value is displayed and the id is sent to the server via a hidden input field
            input.value = selectedItem.value;
            const targetName = input.getAttribute('data-actarget');
            if (targetName) {
                const acTarget = input.parentNode.querySelector(`input[name="${targetName}"]`);
                acTarget.value = selectedItem.key;
            }
            if (window.createMatterForm && input.dataset.actarget == 'category_code') {
                // We're in a matter creation form - fill caseref with corresponding suggested value
                fetchREST('/matter/new-caseref?term=' + selectedItem.prefix, 'GET')
                    .then(data => {
                        createMatterForm.caseref.value = data[0].value;
                    });
            }
        } else {
            // Used for content editable fields where the same field is used for sending the id to the server
            input.value = selectedItem.key;
        }

        event.preventDefault() // Prevent other click events from being triggered

        // Send event for further processing 
        const acCompleted = new CustomEvent('acCompleted', { detail: selectedItem });
        input.dispatchEvent(acCompleted);

        // Remove the suggestion list
        document.body.removeChild(suggestionList);
        
        // Set focus on next input by simulating a "Tab" press
        if (input.form) {
            const inputs = Array.from(input.form.querySelectorAll('input:not([type="hidden"])'));
            const currentIndex = inputs.indexOf(input);
            const nextIndex = (currentIndex + 1) % inputs.length;
            inputs[nextIndex].focus();
        } else {
            input.blur();
        }
    }
  
    return {
        attachWidget
    };
}
// End new autocomplete

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

// target: the URL to submit to, Form: the form element, after: optional further action, submitbutton: the button elemlent (optional)
var submitModalForm = (target, Form, after, submitbutton) => {
    submitbutton.insertAdjacentHTML('afterbegin', '<i class="spinner-border spinner-border-sm" role="status" />');
    formData = new FormData(Form);
    params = new URLSearchParams(formData);
    footerAlert.classList.remove("alert-danger");
    footerAlert.innerHTML = "";
    fetchREST(target, 'POST', formData)
        .then(data => {
            if (data.errors) {
                // Remove spinner if present
                if (spinner = submitbutton.getElementsByTagName('i')[0]) {
                    spinner.remove();
                }
                footerAlert.innerHTML = data.message;
                footerAlert.classList.add('alert-danger');
                processSubmitErrors(data.errors, Form);
            } else if (data.exception) {
                if (spinner = submitbutton.getElementsByTagName('i')[0]) {
                    spinner.remove();
                }
                footerAlert.innerHTML = data.message;
                footerAlert.classList.add('alert-danger');
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
            inputElt.value = '';
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