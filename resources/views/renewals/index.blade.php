@extends('layouts.app')

@section('script')
<script type="text/javascript">

    var url = new URL(window.location.href);

    function refreshList() {
        window.history.pushState('', 'phpIP', url);
        reloadPart(url, 'renewalList');
    }

    filterFields.addEventListener('input', debounce( e => {
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

    @if (config('renewal.invoice.backend') == 'dolibarr')
    invoiceRenewals.addEventListener("click", function (b) {
        msgAction = "invoicing";
        actionRenewals(b.target, msgAction, '/renewal/invoice/1')
    });
    @endif

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

    @if (config('renewal.general.receipt_tabs'))
    receiptRenewals.addEventListener("click", function (b) {
        msgAction = "registering receipt";
        actionRenewals(b.target, msgAction, '/renewal/receipt')
    });

    sendReceiptsRenewals.addEventListener("click", function (b) {
        msgAction = "closing renewals";
        actionRenewals(b.target, msgAction, '/renewal/closing')
    });
    @endif

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
            if(!end) {
                alert("No renewals selected for " + msgAction);
                // withdraw spinner and restore button
                button.removeChild(document.getElementsByClassName('spinner-border')[0]);
                return;
            }
            var begin = document.getElementById('Fromdate').value;
            var string = JSON.stringify({'begin':begin, 'end':end});
        } else {
            var string = JSON.stringify({task_ids: tids});
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
            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
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
        var string = JSON.stringify({task_ids: tids, clear: false});
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/renewal/order', true);
        xhr.setRequestHeader('Content-Type', 'application/json; charset=utf-8');
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
        xhr.send(string);
        xhr.onload = function(e) {
            if (this.status == 200) {
                // Find file name
                var filename = xhr.getResponseHeader('Content-Disposition').split("filename=")[1];

                // The actual download by creating a link and clicking it programmatically
                var f = new File([xhr.response], filename, { type: xhr.getResponseHeader('Content-Disposition') });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(f);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else if (this.status == 501){
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
</script>
@endsection

@section('style')
<style>
  input:not(:placeholder-shown) {
    border-color: green;
  }
</style>
@endsection

@section('content')

<div class="card">
    <div class="card-header py-1">
        <span class="lead">
            Manage renewals
        </span>
        <a href="https://github.com/jjdejong/phpip/wiki/Renewal-Management" target="_blank" title="Help" class="btn btn-outline-primary btn-sm">?</a>
        <a href="/logs" class="btn btn-info">View logs</a>
        <button id="clearFilters" type="button" class="btn btn-info float-right">&larrpl; Clear filters</button>
    </div>
    <div class="card-header py-1">
        <nav class="mt-1">
            <div class="nav nav-pills justify-content-center" id="tabsGroup">
                <a class="nav-item nav-link {{ !$step && !$invoice_step ? 'active' : '' }}" href="#p1" data-toggle="tab" data-step="0">First call</a>
                <a class="nav-item nav-link {{ $step == 2 ? 'active' : '' }}" href="#p2" data-toggle="tab" data-step="2">Reminder</a>
                <a class="nav-item nav-link {{ $step == 4 ? 'active' : '' }}" href="#p3" data-toggle="tab" data-step="4">Payment</a>
                @if (config('renewal.general.receipt_tabs'))
                <a class="nav-item nav-link {{ $step == 6 ? 'active' : '' }}" href="#p4" data-toggle="tab" data-step="6">Receipts</a>
                <a class="nav-item nav-link {{ $step == 8 ? 'active' : '' }}" href="#p5" data-toggle="tab" data-step="8">Receipts received</a>
                @endif
                <a class="nav-item nav-link {{ $step == 12 ? 'active' : '' }}" href="#p6" data-toggle="tab" data-step="12">Abandoned</a>
                <a class="nav-item nav-link {{ $step == 14 ? 'active' : '' }}" href="#p9" data-toggle="tab" data-step="14">Lapsed</a>
                <a class="nav-item nav-link {{ $step == 10 ? 'active' : '' }}" href="#p10" data-toggle="tab" data-step="10">Closed</a>
                <a class="nav-item nav-link {{ $invoice_step == 1 ? 'active' : '' }}" href="#p7" data-toggle="tab" data-invoice_step="1">Invoicing</a>
                <a class="nav-item nav-link {{ $invoice_step == 2 ? 'active' : '' }}" href="#p8" data-toggle="tab" data-invoice_step="2">Invoiced</a>
                <a class="nav-item nav-link {{ $invoice_step == 3 ? 'active' : '' }}" href="#p11" data-toggle="tab" data-invoice_step="3">Invoices paid</a>
            </div>
        </nav>
        <div class="tab-content mt-1">
            <div class="tab-pane {{ !$step && !$invoice_step ? 'active' : '' }}" id="p1">
                <div class="container text-right">
                    <div class="btn-group">
                        <button class="btn btn-info" type="button" id="callRenewals">Send call email</button>
                        <button class="btn btn-info" type="button" id="renewalsSent">Call sent manually</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $step == 2 ? 'active' : '' }}" id="p2">
                <div class="container text-right">
                    <div class="btn-group">
                        <button class="btn btn-outline-info" type="button" id="reminderRenewals">Send reminder email</button>
                        <button class="btn btn-outline-info" type="button" id="lastReminderRenewals" title="Send reminder and enter grace period">Send last reminder email</button>
                        <button class="btn btn-info" type="button" id="instructedRenewals" title="Instructions received to pay">Payment order received</button>
                        <button class="btn btn-info" type="button" id="abandonRenewals" title="Abandon instructions received">Abandon</button>
                        <button class="btn btn-info" type="button" id="lapsedRenewals" title="Office lapse communication received">Lapsed</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $step == 4 ? 'active' : ''}}" id="p3">
                <div class="container text-right">
                    <div class="btn-group">
                        <button class="btn btn-outline-info" type="button" id="xmlRenewals" title="Generate xml files for EP or FR">Download XML order to pay</button>
                        <button class="btn btn-info" type="button" id='doneRenewals'>Paid</button>
                    </div>
                </div>
            </div>
            @if (config('renewal.general.receipt_tabs'))
            <div class="tab-pane {{ $step == 6 ? 'active' : ''}}" id="p4">
                <div class="container text-right">
                    <button class="btn btn-info" type="button" id="receiptRenewals">Official receipts received</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 8 ? 'active' : ''}}" id="p5">
                <div class="container text-right">
                    <button class="btn btn-info" type="button" id="sendReceiptsRenewals">Receipts sent</button>
                </div>
            </div>
            @endif
            <div class="tab-pane {{ $step == 12 ? 'active' : ''}}" id="p6">
                <div class="container text-right">
                    <button class="btn btn-info" type="button" id="lapsingRenewals">Lapse</button>
                </div>
            </div>
            <div class="tab-pane {{ $invoice_step == 1 ? 'active' : ''}}" id="p7">
                <div class="container text-right">
                    <div class="btn-group">
                        @if (config('renewal.invoice.backend') == 'dolibarr')
                        <button class="btn btn-info" type="button" id="invoiceRenewals">Generate invoice</button>
                        @endif
                        <button class="btn btn-outline-info" type="button" id="renewalsExport">Export all</button>
                        <button class="btn btn-info" type="button" id="renewalsInvoiced">Invoiced</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $invoice_step == 2 ? 'active' : ''}}" id="p8">
                <div class="container text-right">
                    <button class="btn btn-info" type="button" id="invoicesPaid">Paid</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 14 ? 'active' : ''}}" id="p9">
                <div class="container text-right">
                    <button class="btn btn-info" type="button" id="sendLapsedRenewals">Lapse communication sent</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 10 ? 'active' : ''}}" id="p10">
                <div class="container text-right">
                    <button class="btn btn-secondary" type="button" disabled>Closed renewals</button>
                </div>
            </div>
            <div class="tab-pane lead {{ $invoice_step == 3 ? 'active' : ''}}" id="p11">
                <div class="container text-right">
                    <button class="btn btn-secondary" type="button" disabled>Paid invoices</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-header py-1">
        <div class="row font-weight-bold">
            <div class="input-group"  id="filterFields">
                <div class="col-2">
                    <input type="text" class="form-control form-control-sm" name="Name" value="{{ Request::get('Name') }}" placeholder="Client">
                </div>
                <div class="col-3">
                    <input type="text" class="form-control form-control-sm" name="Title" value="{{ Request::get('Title') }}" placeholder="Title">
                </div>
                <div class="col-1">
                    <input type="text" class="form-control form-control-sm" name="Case" value="{{ Request::get('Case') }}" placeholder="Matter">
                </div>
                <div class="col-3">
                    <div class="row">
                        <div class="col-2">
                            <input type="text" class="form-control form-control-sm" name="Country" value="{{ Request::get('Country') }}" placeholder="Ctry">
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control form-control-sm" name="Qt" value="{{ Request::get('Qt') }}" placeholder="Qt">
                        </div>
                        <div class="col-2">
                            <div class="btn-group-toggle" data-toggle="buttons" title="In grace period">
                                <label class="btn btn-outline-primary btn-sm">
                                    <input id="grace" name="grace_period" type="checkbox">Grace
                                </label>
                            </div>
                        </div>
                        <div class="col-3 py-2">
                            Cost
                        </div>
                        <div class="col-3 py-2">
                            Fee
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <input type="date" class="form-control form-control-sm" name="Fromdate" id="Fromdate" title="From selected date" value="{{ Request::get('Fromdate') }}">
                        <input type="date" class="form-control form-control-sm" name="Untildate" id="Untildate" title="Until selected date" value="{{ Request::get('Untildate') }}">
                    </div>
                </div>
                <div class="col-1 px-2">
                    <div class="btn-group-toggle" data-toggle="buttons" title="Select/unselect all">
                        <label class="btn btn-outline-primary btn-sm">
                            <input id="selectAll" type="checkbox">&check;
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body pt-2" id="renewalList">
        @if (count($renewals) == 0)
        <div class="row text-danger">
            The list is empty
        </div>
        @else
        <table class="table table-striped table-sm mb-1">
            @foreach ($renewals as $task)
            <tr class="row overlay" data-resource="/task/{{ $task->id }}">
                <td class="col-2">
                    {{ $task->client_name }}
                </td>
                <td class="col-3">
                    {{ $task->short_title }}
                </td>
                <td class="col-1">
                    <a href="/matter/{{ $task->matter_id }}">
                    {{ $task->uid }}
                    </a>
                </td>
                <td class="col-3">
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
                            {{ $task->cost }}
                        </div>
                        <div class="col-3">
                            {{ $task->fee }}
                        </div>
                    </div>
                </td>
                <td class="col-2 text-center">
                    {{ Carbon\Carbon::parse($task->due_date)->isoFormat('L') }}
                    @if ($task->done)
                    <div class="badge badge-success" title="Done">&check;</div>
                    @elseif ($task->due_date < now())
                    <div class="badge badge-danger" title="Overdue">&nbsp;!&nbsp;</div>
                    @elseif ($task->due_date < now()->addWeeks(1))
                    <div class="badge badge-warning" title="Urgent">&nbsp;!&nbsp;</div>
                    @endif
                </td>
                <td class="col-1 px-3">
                    <input id="{{ $task->id }}" class="clear-ren-task" type="checkbox">
                </td>
            </tr>
            @endforeach
        </table>
        {{ $renewals->links() }}
        @endif
    </div>
</div>
@endsection
