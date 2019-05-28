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
        <form method="POST" action="/matter/search">
          @csrf
          <div class="input-group">
            <input type="text" class="form-control" id="matter-search" name="matter_search" placeholder="Search" autocomplete="off">
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

      <div id="ajaxModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Ajax title placeholder</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              Ajax body placeholder
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
  @yield('script')
  <script>
    var relatedUrl = ""; // Identifies what to display in the Ajax-filled modal. Updated according to the href attribute used for triggering the modal
    var resource = ""; // Identifies the REST resource for CRUD operations

    // Ajax fill an element from a url returning HTML
    var fetchInto = async (url, element) => {
      res = await fetch(url);
      element.innerHTML = await res.text();
    }

    // Ajax fill the opened modal and process
    $("#ajaxModal").on("show.bs.modal", function(event) {
      var modalTrigger = event.relatedTarget;
      relatedUrl = modalTrigger.href;
      resource = modalTrigger.dataset.resource;
      if (modalTrigger.hasAttribute('data-size')) this.querySelector('.modal-dialog').classList.add(modalTrigger.dataset.size);
      this.querySelector('.modal-title').innerHTML = modalTrigger.title;
      fetchInto(relatedUrl, this.querySelector('.modal-body'));

      // Process click events in the modal
      ajaxModal.addEventListener('click', (e) => {
        if (e.target.hasAttribute('data-ac')) {
          autocompleteJJ(e.target, e.target.dataset.ac, e.target.dataset.actarget);
        }
        if (e.target.id === 'createMatterSubmit') {
          formData = new FormData(createMatterForm);
          searchParams = new URLSearchParams(formData);
          /*searchParams.forEach( (value, key) => {
            if (value.length === 0) searchParams.delete(key);
          });*/

          fetch('/matter', {
            headers: {
              "X-Requested-With": "XMLHttpRequest",
              "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            method: 'POST',
            body: searchParams
          })
          .then(response => {
            if (!response.ok) {
              return response.json();
            } else {
              return response.text();
            }
          })
          .then(data => {
            if (data.errors) {
              // Form validation error notification
              Object.entries(data.errors).forEach(([key, value]) => {
                let inputElt = createMatterForm.elements[key];
                // "key" matches with both the element's name and id, so inputElt can contain two input elements (the hidden one with id=key and the visible one with name=key)
                if (inputElt.length) {
                  // If defined, inputElt contains two elements and we need the visible one (the second one)
                  inputElt = inputElt[1];
                }
                inputElt.placeholder = value;
                inputElt.classList.add('is-invalid');
              });
              footerAlert.innerHTML = data.message;
              footerAlert.classList.add('alert-danger');
            } else {
              // Redirect to the created matter (link returned by MatterController.store())
              location.href = data;
            }
          })
          .catch(error => {
            console.log(error);
          });
        };
      });
    });


    // Ajax reset modal to default when it is closed
    $("#ajaxModal").on("hidden.bs.modal", function(event) {
      this.querySelector('.modal-body').innerHTML = "Ajax body placeholder";
      this.querySelector('.modal-title').innerHTML = "Ajax title placeholder";
      this.querySelector('.modal-dialog').className = "modal-dialog";
      footerAlert.innerHTML = "";
      footerAlert.classList.remove('alert-danger');
    });

    var autocompleteJJ = (searchField, dataSource, targetField) => {
      /* "searchField" is the element receiving the user input,
       * "dataSource" is the Ajax resource URL, and
       * "targetField" is an (optional) input field name receiving the "id" value
       * The Ajax resource returns a list of JSON id/value pairs, sometimes a label
       * */

      // Start by removing stray result lists that can remain when clicking erratically
      if (tmp = document.getElementById('matchList')) tmp.remove();
      // Create a fresh result list attached to the current element
      searchField.insertAdjacentHTML('afterend', '<div id="matchList" class="dropdown-menu bg-light"></div>');
      var targetElement = "",
        items = "",
        selectedItem = "";
      if (targetField) {
        // The hidden input field is supposed to be the first
        targetElement = document.getElementsByName(targetField)[0];
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
              match => `<button class="dropdown-item py-1" type="button" id="${match.id ? match.id : match.value}" data-value="${match.value}">${match.label ? match.label : match.value}</button>`
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
        selectedItem = items.filter((item) => {
          return item.value === e.target.dataset.value;
        });
        searchField.value = selectedItem[0].value;
        if (targetField) {
          targetElement.value = selectedItem[0].id;
        }
        matchList.remove();
      };
    }
  </script>
</body>

</html>
