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

// Simple debounce
function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

// Ajax fill the opened modal
ajaxModal.addEventListener('show.bs.modal', event => {
    var modalTrigger = event.relatedTarget;
    contentSrc = modalTrigger.href;
    ajaxModal.querySelector('.modal-title').innerHTML = modalTrigger.title;
    if (modalTrigger.hasAttribute('data-size')) {
        ajaxModal.querySelector('.modal-dialog').classList.add(modalTrigger.dataset.size);
    }
    fetchInto(contentSrc, ajaxModal.querySelector('.modal-body'));
});

// Display actor dependencies in corresponding tab
app.addEventListener('show.bs.tab', e => {
    if (e.target.id === 'actorUsedInToggle') {
        fetchInto(e.target.href, actorUsedIn);
    }
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
        case 'createCountrySubmit':
            submitModalForm('/countries', createCountryForm, null, e.target);
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
        case 'deleteCountry':
        case 'deleteClass':
        case 'deleteMember':
            if (confirm("Deleting  " + e.target.dataset.message + ". Continue anyway?")) {
                fetchREST(e.target.dataset.url, 'DELETE')
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload();
                        } else if (data.message) {
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
        
        case 'updateCredentials':
            const form = e.target.form;
            const params = new URLSearchParams();
            
            // For login, only add if it's different from its default value
            const loginInput = form.elements['login'];
            if (loginInput.value !== loginInput.defaultValue) {
                params.append('login', loginInput.value);
            }
            
            // For password, only add if a new password is provided
            const passwordInput = form.elements['password'];
            if (passwordInput.value) {
                params.append('password', passwordInput.value);
                params.append('password_confirmation', form.elements['password_confirmation'].value);
            }
            
            // Don't submit if nothing to update
            if (params.toString() === '') {
                return;
            }
            
            // Show loading state
            e.target.insertAdjacentHTML('afterbegin', '<i class="spinner-border spinner-border-sm me-2" role="status" />');
            e.target.disabled = true;
            
            // Clear previous validation states
            form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                el.classList.remove('is-valid', 'is-invalid');
                const feedback = el.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.remove();
                }
            });
            
            fetchREST(form.dataset.resource, 'PUT', params)
                .then(data => {
                    // Remove loading spinner
                    const spinner = e.target.querySelector('.spinner-border');
                    if (spinner) spinner.remove();
                    e.target.disabled = false;

                    if (data.errors) {
                        // Show validation errors
                        Object.entries(data.errors).forEach(([key, value]) => {
                            const input = form.elements[key];
                            if (input) {
                                input.classList.add('is-invalid');
                                // Add error feedback
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = value[0];
                                input.parentNode.appendChild(feedback);
                            }
                        });
                    } else {
                        // Show success state for only the changed fields
                        Array.from(params.keys()).forEach(key => {
                            const input = form.elements[key];
                            if (input) input.classList.add('is-valid');
                        });
                        // Update login's default value if it was changed
                        if (params.has('login')) {
                            loginInput.defaultValue = loginInput.value;
                        }
                        // Clear password fields if they were used
                        if (params.has('password')) {
                            passwordInput.value = '';
                            form.elements['password_confirmation'].value = '';
                        }
                    }
                });
            break;
    }

    /* Various functions used here and there */

    // Nationalize modal
    if (e.target.matches('#ncountries .btn-outline-danger')) {
        e.target.parentNode.remove();
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

// Process the changes made in forms and fields throughout the application
app.addEventListener('change', e => {
    if (e.target.matches('.noformat')) {
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
                    } else if (data.message && data.status !== 'success') {
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
                        if (!window.ajaxPanel && contentSrc && !e.target.closest('.tab-content')) {
                            // Reload modal with updated content
                            fetchInto(contentSrc, ajaxModal.querySelector('.modal-body'));
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
ajaxModal.addEventListener('hidden.bs.modal', event => {
    ajaxModal.querySelector('.modal-body').innerHTML = '<div class="spinner-border" role="status"></div>';
    ajaxModal.querySelector('.modal-title').innerHTML = "Ajax title placeholder";
    ajaxModal.querySelector('.modal-dialog').className = "modal-dialog";
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

// Autocompletion functionality
let suggestionSelected = false;
const handleSelectedItem = function(selectedItem, input) {
    input.setAttribute('data-selected', selectedItem.value);
    const targetName = input.dataset.actarget;
    if (input.id == 'addCountry') {
        let newCountry = appendCountryTemplate.content.children[0].cloneNode(true);
        newCountry.id = 'country-' + selectedItem.key;
        newCountry.children[0].value = selectedItem.key;
        newCountry.children[1].value = selectedItem.value;
        ncountries.appendChild(newCountry);
        // Wait for the new country entry to be added to the DOM before resetting the input field
        setTimeout(() => {
            addCountry.value = "";
        }, 200);
    } else if (targetName) {
        // Used for static forms where the human readable value is displayed and the id is sent to the server via a hidden input field
        input.value = selectedItem.value;
        const acTarget = input.parentNode.querySelector(`input[name="${targetName}"]`);
        acTarget.value = selectedItem.key;

        if (window.createMatterForm && targetName == 'category_code') {
            // We're in a matter creation form - fill caseref with corresponding suggested value
            fetchREST('/matter/new-caseref?term=' + selectedItem.prefix, 'GET')
                .then(data => {
                    createMatterForm.caseref.value = data[0].value;
                });
        }
    } else {
        // Used for content editable fields where the same field is used for sending the id to the server
        input.value = selectedItem.key;
        input.addEventListener('xhrsent', e => {
            input.value = selectedItem.value;
        });
    }

    const acCompletedEvent = new CustomEvent('acCompleted', { detail: selectedItem });
    input.dispatchEvent(acCompletedEvent);

    if (input.form) {
        const inputs = Array.from(input.form.querySelectorAll('input:not([type="hidden"])'));
        const currentIndex = inputs.indexOf(input);
        const nextIndex = (currentIndex + 1) % inputs.length;
        // Give time for the blur event to fire when using the mouse, otherwise focus() doesn't work
        setTimeout( () => {
            inputs[nextIndex].focus();
        });
    }
};

const handleInput = async function(input, dropdown) {
    try {
        const url = new URL(input.getAttribute('data-ac'), window.location.origin);
        const term = input.value;
        const minLength = input.getAttribute('data-aclength') || 1;
        if (term.length < minLength) {
            dropdown.classList.remove('show');
            return;
        }
        url.searchParams.append('term', input.value);
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const suggestions = await response.json();
        if (!Array.isArray(suggestions)) {
            throw new Error('Invalid response format');
        }
        
        dropdown.innerHTML = '';
        if (suggestions.length === 0) {
            dropdown.classList.remove('show');
            return;
        }
        
        suggestions.forEach(suggestion => {
            const item = document.createElement('a');
            item.classList.add('dropdown-item');
            item.href = '#';
            item.textContent = suggestion.label || suggestion.value;
            item.addEventListener('mousedown', function(event) {
                suggestionSelected = true;
                handleSelectedItem(suggestion, input);
            });
            dropdown.appendChild(item);
        });
        dropdown.classList.add('show');
    } catch (error) {
        console.error('Autocomplete error:', error);
        dropdown.classList.remove('show');
    }
};

const addAutocomplete = function(input) {
    input.setAttribute('data-oldvalue', input.value);
    const dropdown = document.createElement('ul');
    dropdown.classList.add('dropdown-menu');
    dropdown.classList.add('py-0');
    //dropdown.setAttribute('id', 'ac-' + Math.random().toString(36).slice(2, 10));
    input.insertAdjacentElement('afterend', dropdown);
    input.addEventListener('input', function() {
        handleInput(input, dropdown);
    });

    // Remove the dropdown when the input loses focus
    input.addEventListener('blur', function() {
        // Fired after a "mousedown" event on the dropdown, but before a "click" event
        dropdown.innerHTML = '';
        dropdown.classList.remove('show');
    });

    // Clear/reestablish the input if it has changed without a selection in the dropdown
    input.addEventListener('change', function(event) {
        const actarget = input.getAttribute('data-actarget')
        // Clear the target if the input has been purposely cleared
        if (actarget && input.value.trim() === '') {
            const targetInput = document.querySelector(`input[name="${actarget}"]`);
            targetInput.value = '';
        }
        // Reestablish the old value if no selection has been made
        if (!suggestionSelected) {
            if (input.value !== '') {
                input.value = input.dataset.oldvalue || input.dataset.selected || '';
                // Do not propagate the change to the global change event listener
                event.stopPropagation();
            } // Else value has been purposely cleared and the global change listenr should handle it
        }
        suggestionSelected = false;
    });

    // Handle the suggestions with the keyboard
    input.addEventListener('keydown', function(event) {
        const dropdownItems = dropdown.querySelectorAll('.dropdown-item');
        const activeItem = dropdown.querySelector('.active');
        switch (event.key) {
            case 'Tab':
            case 'ArrowDown':
                event.preventDefault();
                if (activeItem) {
                    activeItem.classList.remove('active');
                    if (activeItem.nextElementSibling) {
                        activeItem.nextElementSibling.classList.add('active');
                    } else {
                        dropdownItems[0].classList.add('active');
                    }
                } else if (dropdownItems.length > 0) {
                    dropdownItems[0].classList.add('active');
                }
                break;
            case 'ArrowUp':
                event.preventDefault();
                if (activeItem) {
                    activeItem.classList.remove('active');
                    if (activeItem.previousElementSibling) {
                        activeItem.previousElementSibling.classList.add('active');
                    } else {
                        dropdownItems[dropdownItems.length - 1].classList.add('active');
                    }
                } else if (dropdownItems.length > 0) {
                    dropdownItems[dropdownItems.length - 1].classList.add('active');
                }
                break;
            case 'Enter':
                // No "event.preventDefault()" here!
                if (activeItem) {
                    activeItem.dispatchEvent(new Event('mousedown'));
                }
                break;
            case 'Escape':
                dropdown.innerHTML = '';
                dropdown.classList.remove('show');
                event.stopPropagation();
                break;
        }
    });
};

// Initialize existing autocomplete elements
const acInputs = document.querySelectorAll('[data-ac]');
acInputs.forEach(input => {
    addAutocomplete(input);
});

// Initialize dynamically added autocomplete elements
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        mutation.addedNodes.forEach(function(node) {
            if (node.nodeType === Node.ELEMENT_NODE) {
                const inputs = node.querySelectorAll('[data-ac]');
                inputs.forEach(input => {
                    // Attach only if not previously attached
                    if (!input.hasAttribute('data-oldvalue')) {
                        addAutocomplete(input);
                    }
                });
            }
        });
    });
});
observer.observe(document.body, { childList: true, subtree: true });
// End autocompletion functionality

// Process non-input content editable fields
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

// Reload country view after successful field updates
app.addEventListener('xhrsent', e => {
    const reloadPart = e.target.closest('.reload-part');
    if (reloadPart && reloadPart.dataset.resource) {
        fetchInto(reloadPart.dataset.resource, reloadPart);
    }
});

// Handle country name locale switching
app.addEventListener('change', e => {
    if (e.target.matches('#nameLocaleSelect')) {
        // For editable custom countries
        const selectedLocale = e.target.value;
        const mainInput = e.target.parentElement.querySelector('input[type="text"]');
        const nameInputs = document.getElementById('nameInputs');
        
        // Get the value for the selected locale from hidden inputs
        const sourceInput = nameInputs.querySelector(`input[data-locale="${selectedLocale}"]`);
        
        // Update main input name and value
        mainInput.name = `name[${selectedLocale}]`;
        mainInput.value = sourceInput ? sourceInput.value : '';
        
        // Keep the nameInputs hidden - we don't want to show all languages at once
        // Update the corresponding hidden input when the main input changes
        const updateHiddenInput = () => {
            const currentLocaleInput = nameInputs.querySelector(`input[data-locale="${selectedLocale}"]`);
            if (currentLocaleInput) {
                currentLocaleInput.value = mainInput.value;
            }
        };
        
        // Set up listener for changes to main input
        mainInput.removeEventListener('input', updateHiddenInput);
        mainInput.addEventListener('input', updateHiddenInput);
    } else if (e.target.matches('#nameLocaleSelectReadonly')) {
        // For read-only standard countries - just update the displayed name
        const selectedLocale = e.target.value;
        const nameSpan = e.target.parentElement.querySelector('span');
        const reloadPart = e.target.closest('.reload-part');
        
        // Fetch the country data with the new locale and update the display
        if (reloadPart && reloadPart.dataset.resource) {
            fetch(reloadPart.dataset.resource + '?locale=' + selectedLocale)
                .then(response => response.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newNameSpan = doc.querySelector('span.form-control-sm');
                    if (newNameSpan) {
                        nameSpan.textContent = newNameSpan.textContent;
                    }
                });
        }
    }
});

