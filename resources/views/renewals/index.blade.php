@extends('layouts.app')

@section('script')

<script type="text/javascript">

  var url = new URL(window.location.href)

  function refreshList() {
    window.history.pushState('', 'phpIP', url);
    reloadPart(url, 'renewalList');
  }

  filterFields.addEventListener('input', debounce( e => {
    if (e.target.name !== "selectAll") {
        if (e.target.value.length === 0 || (e.target.name == "grace_period" && ! e.target.checked)) {
            url.searchParams.delete(e.target.name);
        }
        else {
            url.searchParams.set(e.target.name, e.target.value);
        }
        refreshList();
    }
  }, 500));

  selectAll.addEventListener('change', e => {
    if (e.target.checked) {
        // Check all checkboxes
        newValue = true;
    }
    else
    {
        // Uncheck all checkboxes
        newValue = false;
    }
    var boxes=document.getElementsByClassName('clear-ren-task');
    for (i = 0; i < boxes.length; i++) {
        boxes[i].checked = newValue;
    }
  });

    // Load list according to corresponding tab
    tabsGroup.addEventListener("click", function (e) {
        url.searchParams.delete('step');
        url.searchParams.delete('invoice_step');
        url.searchParams.delete('tab');
        if(e.target.hasAttribute('step')) {
            url.searchParams.set('step', e.target.getAttribute('step'));
        }
        if(e.target.hasAttribute('invoice_step')) {
            url.searchParams.set('invoice_step', e.target.getAttribute('invoice_step'));
        }
        if(e.target.hasAttribute('href')) {
            url.searchParams.set('tab', e.target.getAttribute('href'));
        }
        window.history.pushState('', 'phpIP', url);
        reloadPart(url, 'renewalList');
    });

    clearFilters.onclick = () => {
        var mySearchParams = url.searchParams;
        for ( key of mySearchParams.keys()) {
            if ( (key != 'step') && (key != 'invoice_step') && (key != 'tab')) {
                mySearchParams.delete(key);
            }
        }
        refreshList();
    };

    doneRenewals.addEventListener("click",function (b) {
            msgAction = "resetting";
            actionRenewals(b.target,msgAction, '/renewal/done');
    });

    callRenewals.addEventListener("click",function (b) {
            msgAction = "call";
            actionRenewals(b.target,msgAction,'/renewal/call/1')
    });

    renewalsSent.addEventListener("click",function (b) {
            msgAction = "call";
            actionRenewals(b.target,msgAction,'/renewal/call/0')
    });

    invoiceRenewals.addEventListener("click",function (b) {
            msgAction = "invoicing";
            actionRenewals(b.target,msgAction,'/renewal/invoice')
    });

    instructedRenewals.addEventListener("click",function (b) {
            msgAction = "for payment";
            actionRenewals(b.target,msgAction,'/renewal/topay')
    });

    lastReminderRenewals.addEventListener("click",function (b) {
            msgAction = "last call";
            actionRenewals(b.target,msgAction,'/renewal/lastcall')
    });

    reminderRenewals.addEventListener("click",function (b) {
            msgAction = "reminder";
            actionRenewals(b.target,msgAction,'/renewal/reminder')
    });

    receiptRenewals.addEventListener("click",function (b) {
            msgAction = "registering receipt";
            actionRenewals(b.target,msgAction,'/renewal/receipt')
    });

    sendReceiptsRenewals.addEventListener("click",function (b) {
            msgAction = "closing renewals";
            actionRenewals(b.target,msgAction,'/renewal/closing')
    });

    abandonRenewals.addEventListener("click",function (b) {
            msgAction = "abandon renewals";
            actionRenewals(b.target,msgAction,'/renewal/abandon')
    });

    lapsedRenewals.addEventListener("click",function (b) {
            msgAction = "lapsed renewals";
            actionRenewals(b.target,msgAction,'/renewal/lapsing')
    });

    lapsingRenewals.addEventListener("click",function (b) {
            msgAction = "lapsed renewals";
            actionRenewals(b.target,msgAction,'/renewal/lapsing')
    });

    sendLapsedRenewals.addEventListener("click",function (b) {
            msgAction = "lapse communications sent";
            actionRenewals(b.target,msgAction,'/renewal/closing')
    });

    async function actionRenewals(button, msgAction, action_url) {
        // Active spinner
        button.insertAdjacentHTML('afterbegin', '<i class="spinner-border spinner-border-sm" role="status" />');
        //
        var tids = getSelected();
        if (tids.length === 0) {
            var end = document.getElementById('Untildate').value;
            if(!end) {
                alert("No renewals selected nor limit date given for " + msgAction);
                // withdraw spinner and restore button
                button.removeChild(document.getElementsByClassName('spinner-border')[0]);
                return;
            }
            var begin = document.getElementById('Fromdate').value;
            var string = JSON.stringify({'begin':begin, 'end':end});
        }
        else
        {
            var string = JSON.stringify({task_ids: tids});
        }
        context_url = new URL(window.location.href);
        await submitUpdate(string, action_url).catch(err => alert(err));
        window.history.pushState('', 'phpIP', context_url);
        reloadPart(context_url,'renewalList');
        // withdraw spinner
        button.removeChild(document.getElementsByClassName('spinner-border')[0]);
    }

    function submitUpdate(string, url) {
        return new Promise(function (resolve, reject)  {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-type','application/json; charset=utf-8');
            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            xhr.onload = function () {
                if (this.status === 200) {
                    resolve(JSON.parse(this.responseText).success);
                }
                else if (this.status === 419) {
                    reject("Token expired. Refresh the page.");
                }
                else if (this.status === 501)
                {
                    reject(JSON.parse(this.responseText).error);
                }
                else
                {
                    reject("Something went wrong.\n");
                }
            }
            xhr.send(string);
        });
    }

    xmlRenewals.addEventListener("click",function () {
        var tids = getSelected();
        if (tids.length === 0) {
            alert("No renewals selected for order!");
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/renewal/order', true);
        xhr.responseType = 'arraybuffer';
        xhr.onload = function () {
            if (this.status === 200) {
                var filename = "";
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }
                var type = xhr.getResponseHeader('Content-Type');

                var blob;
                if (typeof File === 'function') {
                    try {
                        blob = new File([this.response], filename, { type: type });
                    } catch (e) { /* Edge */ }
                }
                if (typeof blob === 'undefined') {
                    blob = new Blob([this.response], { type: type });
                }

                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                    window.navigator.msSaveBlob(blob, filename);
                } else {
                    var URL = window.URL || window.webkitURL;
                    var downloadUrl = URL.createObjectURL(blob);

                    if (filename) {
                        // use HTML5 a[download] attribute to specify filename
                        var a = document.createElement("a");
                        // safari doesn't support this yet
                        if (typeof a.download === 'undefined') {
                            window.location = downloadUrl;
                        } else {
                            a.href = downloadUrl;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                        }
                    } else {
                        window.location = downloadUrl;
                    }

                    setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
                }
            }
        };
        var string = JSON.stringify({task_ids: tids, clear: false});
        xhr.setRequestHeader('Content-type','application/json; charset=utf-8');
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
        xhr.send(string);
    });

    function getSelected() {
        var tids = new Array();
        var boxes = document.getElementsByClassName('clear-ren-task');
        for (i = 0; i < boxes.length; i++) {
            if (boxes[i].checked) {
                tids.push(boxes[i].getAttribute('id'));
                }
        }
        return tids;
    };

</script>

@stop

@section('content')

<div class="row card-deck">
  <div class="col-12">
    <div class="card mt-1">
      <div class="card-header py-1">
        <div class="row">
          <div class="lead col-3">
            Manage renewals
          </div>
          <div class="col-3">
                <div class="button-group">
                    <button id="clearFilters" type="button" class="btn btn-primary">&larrpl; Clear filters</button>
                </div>
          </div>
          <div class="col-12">
            <div class="nav nav-pills" id="tabsGroup">
                <a class="nav-item nav-link {{ ($tab === '#p1' || empty($tab) ) ? 'active' : '' }}" href="#p1" data-toggle="tab" step="0">First call</a>
                <a class="nav-item nav-link {{ ($tab === '#p2' ) ? 'active' : '' }}" href="#p2" data-toggle="tab" step="2">Reminder</a>
                <a class="nav-item nav-link {{ ($tab === '#p3' ) ? 'active' : '' }}" href="#p3" data-toggle="tab" step="4">Payment</a>
                <a class="nav-item nav-link {{ ($tab === '#p4' ) ? 'active' : '' }}" href="#p4" data-toggle="tab" step="6">Receipts</a>
                <a class="nav-item nav-link {{ ($tab === '#p5' ) ? 'active' : '' }}" href="#p5" data-toggle="tab" step="8">Receipts received</a>
                <a class="nav-item nav-link {{ ($tab === '#p6' ) ? 'active' : '' }}" href="#p6" data-toggle="tab" step="12">Abandoned</a>
                <a class="nav-item nav-link {{ ($tab === '#p9' ) ? 'active' : '' }}" href="#p9" data-toggle="tab" step="14">Lapsed</a>
                <a class="nav-item nav-link {{ ($tab === '#p10' ) ? 'active' : '' }}" href="#p10" data-toggle="tab" step="10">Closed</a>
                <a class="nav-item nav-link {{ ($tab === '#p7' ) ? 'active' : '' }}" href="#p7" data-toggle="tab" invoice_step="1">Invoicing</a>
                <a class="nav-item nav-link {{ ($tab === '#p8' ) ? 'active' : '' }}" href="#p8" data-toggle="tab" invoice_step="2">Invoiced</a>
            </div>
          </div>
          <div class="tab-content">
            <div class="input-group tab-pane {{ ($tab === '#p1' || empty($tab) ) ? 'active' : '' }}" id="p1">
                <button class="btn btn-outline-primary" type="button" id="callRenewals">Send</button>
                <button class="btn btn-outline-primary" type="button" id="renewalsSent">Clear as sent</button>
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p2') ? 'active' : '' }}" id="p2">
                <button class="btn btn-outline-primary" type="button" id="reminderRenewals">Send reminder</button>
                <button class="btn btn-outline-primary" type="button" id="lastReminderRenewals" title="Send the reminder and enter in grace period">Send last reminder</button>
                <button class="btn btn-outline-primary" type="button" id="instructedRenewals" title="Instructions received to pay">Register order</button>
                <button class="btn btn-outline-primary" type="button" id="abandonRenewals" title="Instructions received to abandon">Abandon</button>
                <button class="btn btn-outline-primary" type="button" id="lapsedRenewals" title="The office said that the title is lapsed">Lapsed</button>
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p3' ) ? 'active' : ''}}" id="p3">
                <button class="btn btn-outline-primary" type="button" id="xmlRenewals" title="Generate xml files for EP or FR">Prepare order to pay</button>
                <button class="btn btn-outline-primary" type="button" id='doneRenewals'>Clear as paid</button>
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p4' ) ? 'active' : ''}}" id="p4">
                <button class="btn btn-outline-primary" type="button" id="receiptRenewals">Register receipt</button>
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p5' ) ? 'active' : ''}}" id="p5">
                <button class="btn btn-outline-primary" type="button" id="sendReceiptsRenewals">Receipts sent</button>
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p6' ) ? 'active' : ''}}" id="p6">
                <button class="btn btn-outline-primary" type="button" id="lapsingRenewals">Lapse</button>
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p7' ) ? 'active' : ''}}" id="p7">
                <button class="btn btn-outline-primary" type="button" id="invoiceRenewals">Invoice</button>
                <button class="btn btn-outline-primary" type="button" id="renewalsInvoiced">Clear as invoiced</button>
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p8' ) ? 'active' : ''}}" id="p8">
                Invoiced renewals
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p9' ) ? 'active' : ''}}" id="p9">
                    <button class="btn btn-outline-primary" type="button" id="sendLapsedRenewals">Lapse communication sent</button>
            </div>
            <div class="input-group tab-pane {{ ($tab === '#p10' ) ? 'active' : ''}}" id="p10">
            Closed renewals
            </div>
          </div>
        </div>
      </div>
      <div class="card-header py-1">
        <div class="row font-weight-bold">
          <div class="col-2">
            Client
          </div>
          <div class="col-3">
            Title
          </div>
          <div class="col-1">
            Matter
          </div>
          <div class="col-3">
            <div class="row">
                <div class="col-2">
                    Country
                </div>
                <div class="col-2">
                    Qt
                </div>
                <div class="col-2">
                    Grace
                </div>
                <div class="col-3">
                    Cost
                </div>
                <div class="col-3">
                    Fee
                </div>
            </div>
          </div>
          <div class="col-2">
            Due date (from/to)
          </div>
          <div class="col-1">
            Select
          </div>
        </div>
        <div class="row">
            <div class="input-group"  id="filterFields">
                <div class="col-2">
                    <input type="text" class="form-control form-control-sm" name="Name" value="{{ Request::get('Name') }}">
                </div>
                <div class="col-3">
                    <input type="text" class="form-control form-control-sm" name="Title" value="{{ Request::get('Title') }}">
                </div>
                <div class="col-1">
                    <input type="text" class="form-control form-control-sm" name="Case" value="{{ Request::get('Case') }}">
                </div>
                <div class="col-3">
                    <div class="row">
                        <div class="col-2">
                            <input type="text" class="form-control form-control-sm" name="Country" value="{{ Request::get('Country') }}">
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control form-control-sm" name="Qt" value="{{ Request::get('Qt') }}">
                        </div>
                        <div class="col-2">
                            <input  class="text-center" name="grace_period" id="grace" type="checkbox" value = "1" title="In grace period">
                        </div>
                        <div class="col-2">
                        </div>
                    </div>
                </div>
                <div class="col-1">
                    <input type="date" class="form-control form-control-sm" name="Fromdate" id="Fromdate" title="From selected date" value="{{ Request::get('Fromdate') }}">
                </div>
                <div class="col-1">
                    <input type="date" class="form-control form-control-sm" name="Untildate" id="Untildate" title="Until selected date" value="{{ Request::get('Untildate') }}">
                </div>
                <div class="col-1">
                    <input  class="text-center" name="selectAll" id="selectAll" type="checkbox" title="Select/unselect all">
                </div>
            </div>
        </div>
      </div>
      <div class="card-body pt-2" id="renewalList">
        @if (count($renewals) !== 0 )
        @foreach ($renewals as $task)
        <div class="row overlay" data-resource="/task/{{ $task->id }}">
          <div class="col-2">
              {{ $task->client_name }}
          </div>
          <div class="col-3">
              {{ $task->title }}
          </div>
          <div class="col-1">
            <a href="/matter/{{ $task->matter_id }}">
              {{ $task->caseref }}{{ $task->suffix }}
            </a>
          </div>
          <div class="col-3">
            <div class="row">
                <div class="col-2">
                    {{ $task->country }}
                </div>
                <div class="col-2">
                    {{ $task->detail }}
                </div>
                <div class="col-2">
                    {!! $task->grace_period ? "&#9888;" : "" !!}
                </div>
                <div class="col-3">
                    {{ $prices[$task->id]['cost']  }}
                </div>
                <div class="col-3">
                    {{ $prices[$task->id]['fee']  }}
                </div>
            </div>
          </div>
          @if ($task->due_date < date('Y-m-d'))
          <div class="col-2 text-danger">
            {{ date_format(date_create($task->due_date), 'd/m/Y') }}
          </div>
          @elseif ($task->due_date < date('Y-m-d', strtotime("+1 week")))
          <div class="col-2">
            <font color="purple">{{ date_format(date_create($task->due_date), 'd/m/Y') }}</font>
          </div>
          @else
          <div class="col-2">
            {{ date_format(date_create($task->due_date), 'd/m/Y') }}
          </div>
          @endif
          <div class="col-1">
            <input id="{{ $task->id }}" class="clear-ren-task" type="checkbox">
          </div>
        </div>
        @endforeach
        {{ $renewals->links() }}
        @else
        <div class="row text-danger">
          The list is empty
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@stop

@section('script')

@include('home-js')

@stop
