<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'phpIP') }}</title>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}"></script>

  <!-- Fonts
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
        -->

  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  @yield('style')
</head>

<body>
  <div id="app">
    <nav class="navbar navbar-expand-md navbar-dark bg-primary mb-1">
      <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
          {{ config('app.name', 'phpIP') }}
        </a>
        @auth
        <form class="form-inline" method="POST" action="/matter/search">
          @csrf
          <div class="input-group">
            <input type="search" class="form-control" id="matter-search" name="matter_search" placeholder="Search" autocomplete="off">
            <div class="input-group-append">
              <select class="custom-select btn btn-info" id="matter-option" name="search_field">
                <option value="Ref" selected>Case reference</option>
                <option value="Responsible">Responsible</option>
              </select>
              <button class="btn btn-info" type="submit">Go</button>
            </div>
          </div>
        </form>
        @endauth
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Left Side Of Navbar -->
          <ul class="navbar-nav mr-auto">

          </ul>

          <!-- Right Side Of Navbar -->
          <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
            @guest
            <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
            <!-- <li><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li> -->
            @else
            <li><a class="nav-link" href={{ route('home') }}>Dashboard</a></li>
            <li class="nav-item dropdown">
              <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Matters <span class="caret"></span>
              </a>

              <ul class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="{{ url('/matter/') }}">All</a>
                <a class="dropdown-item" href="{{ url('/matter?display_with=PAT') }}">Patents</a>
                <a class="dropdown-item" href="{{ url('/matter?display_with=TM') }}">Trademarks</a>
                <a class="dropdown-item" href="/matter/create?operation=new" data-target="#ajaxModal" data-toggle="modal" data-size="modal-sm" title="Create Matter">New</a>
              </ul>
            </li>

            <li class="nav-item dropdown">
              <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Tables <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="{{ url('/rule/') }}">Edit rules</a>
                <a class="dropdown-item" href="{{ url('/eventname/') }}">Edit event names</a>
                <a class="dropdown-item" href="{{ url('/actor/') }}">Actors</a>
              </ul>
            </li>

            <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }} <span class="caret"></span>
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </div>
            </li>
            @endguest
          </ul>
        </div>
      </div>
    </nav>
    <main class="container">
      @yield('content')

      <datalist id="ajaxDatalist"></datalist>

      <div id="ajaxModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Ajax title placeholder</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
            <div class="modal-footer">
              <span id="footerAlert" class="alert float-left"></span>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  <script>
    var contentSrc = "", // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal
      acList;

    // Ajax fill an element from a url returning HTML
    var fetchInto = async (url, element) => {
      res = await fetch(url);
      element.innerHTML = await res.text();
    }

    var reloadPart = async (url, partId) => {
      res = await fetch(url);
      let doc = new DOMParser().parseFromString(await res.text(), "text/html");
      document.getElementById(partId).innerHTML = doc.getElementById(partId).innerHTML;
    }

    // Perform REST operations with native JS
    var fetchREST = async (url, method, body) => {
      res = await fetch(url, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        method: method,
        body: body
      });
      return res.json();
    }

    // Ajax fill the opened modal
    $("#ajaxModal").on("show.bs.modal", function(event) {
      var modalTrigger = event.relatedTarget;
      contentSrc = modalTrigger.href;
      this.querySelector('.modal-title').innerHTML = modalTrigger.title;
      if (modalTrigger.hasAttribute('data-size')) this.querySelector('.modal-dialog').classList.add(modalTrigger.dataset.size);
      fetchInto(contentSrc, this.querySelector('.modal-body'));
    });

    // Display actor dependencies in corresponding tab
    $(app).on("show.bs.tab", "#actorUsedInToggle", function (e) {
      fetchInto(e.target.href, actorUsedIn);
    });

    // Run JS specific to modals when they are shown
    /*$("#ajaxModal").on("shown.bs.modal", function(event) {

    });*/

    // Process click events
    app.addEventListener('click', (e) => {
      switch (e.target.id) {
        case 'createMatterSubmit':
          submitModalForm('/matter', createMatterForm, true);
          break;

        case 'deleteMatter':
          if (confirm("Deleting the matter. Continue anyway?")) {
            fetchREST(e.target.closest('[data-resource]').dataset.resource, 'DELETE')
              .then((data) => {
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
          submitModalForm('/task', addTaskForm);
          break;

        case 'deleteTask':
          fetchREST(e.target.closest('[data-resource]').dataset.resource, 'DELETE')
            .then(() => fetchInto(contentSrc, ajaxModal.querySelector('.modal-body')));
          break;

        case 'deleteEvent':
          if (confirm("Deleting the event will also delete the linked tasks. Continue anyway?")) {
            fetchREST('/event/' + e.target.dataset.event_id, 'DELETE')
              .then(() => fetchInto(contentSrc, ajaxModal.querySelector('.modal-body')));
          }
          break;

          // Specific processing of the event list modal
        case 'addEventSubmit':
          submitModalForm('/event', addEventForm);
          break;

          // Classifier list modal
        case 'addClassifierSubmit':
          submitModalForm('/classifier', addClassifierForm);
          break;

        case 'deleteClassifier':
          fetchREST(e.target.closest('[data-resource]').dataset.resource, 'DELETE')
            .then(() => fetchInto(contentSrc, ajaxModal.querySelector('.modal-body')));
          break;

          // Specific processing in the actor/role list modal
        case 'removeActor':
          fetchREST(e.target.closest('[data-resource]').dataset.resource, 'DELETE')
            .then(() => fetchInto(contentSrc, ajaxModal.querySelector('.modal-body')));
          break;

          // Nationalize modal
        case 'nationalizeSubmit':
          submitModalForm('/matter/storeN', natMatterForm, true);
          break;

        case 'addCountry':
          $(e.target).autocomplete({
            minLength: 2,
            source: "/country/autocomplete",
            select: function(event, ui) {
              var newCountry = appendCountryTemplate.content.children[0];
              newCountry.id = 'country-' + ui.item.key;
              newCountry.children[0].value = ui.item.key;
              newCountry.children[1].value = ui.item.value;
              ncountries.appendChild(newCountry);
            },
            close: function(event, ui) {
              e.target.value = "";
            }
          });
          break;

          // Actor create and show modals
        case 'createActorSubmit':
          submitModalForm('/actor', createActorForm);
          break;

        case 'deleteActor':
          if (confirm("Deleting the actor " + e.target.dataset.id + ". Continue anyway?")) {
            fetchREST('/actor/' + e.target.dataset.id, 'DELETE')
              .then((data) => {
                if (data.message) {
                  alert("Couldn't delete actor. Check the dependencies. Database said: " + data.message);
                  return false;
                } else {
                  location.href = document.referrer;
                }
              });
          }
          break;
      }

      // Various functions used here and there
      if (e.target.matches('#ncountries .btn-outline-danger')) {
        e.target.parentNode.parentNode.remove();
      }

      if (e.target.hasAttribute('data-panel')) {
        e.preventDefault();
        let markedRow = e.target.closest('tbody').querySelector('.table-info');
        if (markedRow) markedRow.classList.remove('table-info');
        e.target.closest('tr').classList.add('table-info');
        contentSrc = e.target.href;
        let panel = document.getElementById(e.target.dataset.panel);
        fetchInto(e.target.href, panel);
      }

    });

    // Generic in-place edition of input fields
    app.addEventListener("change", e => {
      if (e.target && e.target.matches(".noformat")) {
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
        if (e.target.matches('.titleItem')) { // Handle titles in matter.show
          if (e.target.value.trim().length === 0) {
            fetchREST(resource, 'DELETE');
          } else {
            fetchREST(resource, 'PUT', params);
          }
          reloadPart(window.location.pathname, 'titlePanel');
        } else { // Handle generic input fields
          fetchREST(resource, 'PUT', params)
            .then(data => {
              if (data.errors) {
                footerAlert.innerHTML = Object.values(data.errors)[0];
                footerAlert.classList.add('alert-danger');
              } else {
                if (window.ajaxPanel) {
                  reloadPart(contentSrc, e.target.closest('.tab-pane').id);
                } else if (contentSrc.length !== 0) {
                  fetchInto(contentSrc, ajaxModal.querySelector(".modal-body"));
                }
                footerAlert.classList.remove("alert-danger");
                footerAlert.innerHTML = "";
              }
            })
            .catch(error => console.log(error));
        }
      }
    });

    // Reset ajaxModal to default when it is closed
    $('#ajaxModal').on("hidden.bs.modal", function(event) {
      this.querySelector('.modal-body').innerHTML = '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>';
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
      }

      // Process datalist-based autocompletion
      if (e.target.hasAttribute('data-ac')) {
        if (e.inputType === "insertReplacementText") {
          // An item was selected in the list - finalize autocompletion
          let selected = acList.find(item => {
            return item.value === e.target.value;
          });
          if (e.target.hasAttribute('data-actarget')) {
            actarget = e.target.form[e.target.dataset.actarget];
            actarget.value = selected.key;
          }
          ajaxDatalist.innerHTML = "";
          e.target.blur();
          e.target.dispatchEvent(new CustomEvent("ac-done", {
            bubbles: true,
            detail: selected
          }));
        } else {
          // Character has been typed, generate list
          acQuery(e.target);
        }
      }
    });

    //ajaxModal.addEventListener('ac-done', item => console.log(item.detail));

    /* Custom autocomplete function using native JS
     * "searchField" is the element receiving the user input,
     * "dataSource" is the Ajax resource URL, and
     * "targetName" is an (optional) input field name receiving the "id" value
     * The Ajax resource returns a list of JSON key/value pairs, sometimes a label and other data
     * */
    /*var autocompleteJJ = (searchField, dataSource, targetName) => {
      // Start by removing stray result lists that can remain when clicking erratically
      if (tmp = document.getElementById('matchList')) tmp.remove();
      // Create a fresh result list attached to the current element
      searchField.insertAdjacentHTML('afterend', '<div id="matchList" class="dropdown-menu bg-light"></div>');
      var targetElement = "",
        items = "",
        selectedItem = "";
      if (targetName) {
        // The hidden input field is supposed to be the first
        targetElement = searchField.form[targetName];
      }
      // Get items
      var getItems = async (term) => {
        if (term.length > 0) {
          let res = await fetch(dataSource + '?term=' + term);
          items = await res.json();
          if (items.length === 0) {
            $('#matchList').dropdown('hide');
          } else {
            $('#matchList').dropdown('show');
            let html = items.map(
              match => `<button class="dropdown-item py-1" type="button" id="${match.key ? match.key : match.value}" data-value="${match.value}">${match.label ? match.label : match.value}</button>`
            ).join('');
            matchList.innerHTML = html;
          }
        } else {
          $('#matchList').dropdown('hide');
        }
      };

      searchField.oninput = () => getItems(searchField.value);
      matchList.onclick = (e) => {
        // Retrieve complete selected item, in case it contains more than id, value or label
        selectedItem = items.find((item) => {
          return item.value === e.target.dataset.value;
        });
        if (targetName) {
          // Used for static forms where the human readable value is displayed and the id is sent to the server via a hidden input field
          searchField.value = selectedItem.value;
          targetElement.value = selectedItem.key;
        } else {
          // Used for content editable fields where the same field is used for sending the id to the server
          searchField.value = selectedItem.key;
        }
        matchList.remove();
      };
    }*/

    /*var autocompleteJQ = (searchField, dataSource, targetName) => {
      let targetElement = "";
      if (targetName) {
        // The hidden input field is supposed to be the first
        targetElement = searchField.form[targetName];
      }
      $(searchField).autocomplete({
        minLength: 2,
        source: dataSource,
        change: function(event, ui) {
          if (!ui.item) {
            searchField.value = "";
          }
        },
        select: function(event, ui) {
          if (targetName) {
            // Used for static forms where the human readable value is displayed and the id is sent to the server via a hidden input field
            searchField.value = ui.item.value;
            targetElement.value = ui.item.key;
          } else {
            // Used for content editable fields where the same field is used for sending the id to the server
            searchField.value = ui.item.key;
          }
          searchField.blur(); // Removing focus causes the "change" event to trigger and submit the value via the default functionality attached to the "input.noformat" fields
        }
      });
    }*/

    var submitModalForm = (target, Form) => {
      formData = new FormData(Form);
      params = new URLSearchParams(formData);
      fetchREST(target, 'POST', params)
        .then(data => {
          if (data.errors) {
            processSubmitErrors(data.errors, Form);
            footerAlert.innerHTML = data.message;
            footerAlert.classList.add('alert-danger');
          } else if (data.redirect) {
            // Redirect to the created model (link returned by the controller store() function)
            location.href = data.redirect;
          } else {
            fetchInto(contentSrc, ajaxModal.querySelector('.modal-body'));
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
        inputElt.placeholder = key + ' is required';
        inputElt.className += ' is-invalid';
      });
    }

    // Generates autocompletion list
    var acQuery = async (target) => {
      // clear any previously loaded options in the datalist
      target.list.innerHTML = "";
      // minimum number of characters before we start to generate suggestions
      var min_characters = 1;
      if (target.value.length < min_characters) {
        return false;
      } else {
        let res = await fetch(target.dataset.ac + '?term=' + target.value);
        acList = await res.json();
        acList.forEach(item => {
          // Create a new <option> element
          let option = document.createElement('option');
          option.innerHTML = item.value;
          if (!target.hasAttribute('data-actarget')) {
            option.value = item.key;
          }
          // attach the option to the datalist element
          target.list.appendChild(option);
        });
      }
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
  </script>
  @yield('script')
</body>

</html>
