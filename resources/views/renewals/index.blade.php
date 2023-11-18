@extends('layouts.app')

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
        <legend>
            Manage renewals
            <a href="https://github.com/jjdejong/phpip/wiki/Renewal-Management" class="text-primary" target="_blank" title="Help">
                <svg width="16" height="16" fill="currentColor"><use xlink:href="#question-circle-fill"/></svg>
            </a>
            <a href="/logs" class="btn btn-info">View logs</a>
            <button id="clearFilters" type="button" class="btn btn-info float-right">&larrpl; Clear filters</button>
        </legend>
        <div class="tab-content">
            <div class="tab-pane {{ !$step && !$invoice_step ? 'active' : '' }}" id="p1">
                <div class="container text-end">
                    <div class="btn-group">
                        <button class="btn btn-info" type="button" id="callRenewals">{{ _i('Send call email') }}</button>
                        <button class="btn btn-info" type="button" id="renewalsSent">{{ _i('Call sent manually') }}</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $step == 2 ? 'active' : '' }}" id="p2">
                <div class="container text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-info" type="button" id="reminderRenewals">{{ _i('Send reminder email') }}</button>
                        <button class="btn btn-outline-info" type="button" id="lastReminderRenewals" title="{{ _i('Send reminder and enter grace period') }}">{{ _i('Send last reminder email') }}</button>
                        <button class="btn btn-info" type="button" id="instructedRenewals" title="{{ _i('Instructions received to pay') }}">{{ _i('Payment order received') }}</button>
                        <button class="btn btn-info" type="button" id="abandonRenewals" title="{{ _i('Abandon instructions received') }}">{{ _i('Abandon') }}</button>
                        <button class="btn btn-info" type="button" id="lapsedRenewals" title="{{ _i('Office lapse communication received') }}">{{ _i('Lapsed') }}</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $step == 4 ? 'active' : ''}}" id="p3">
                <div class="container text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-info" type="button" id="xmlRenewals" title="{{ _i('Generate xml files for EP or FR') }}">{{ _i('Download XML order to pay') }}</button>
                        <button class="btn btn-info" type="button" id='doneRenewals'>{{ _i('Paid') }}</button>
                    </div>
                </div>
            </div>
            @if (config('renewal.general.receipt_tabs'))
            <div class="tab-pane {{ $step == 6 ? 'active' : ''}}" id="p4">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="receiptRenewals">Official receipts received</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 8 ? 'active' : ''}}" id="p5">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="sendReceiptsRenewals">Receipts sent</button>
                </div>
            </div>
            @endif
            <div class="tab-pane {{ $step == 12 ? 'active' : ''}}" id="p6">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="lapsingRenewals">Lapse</button>
                </div>
            </div>
            <div class="tab-pane {{ $invoice_step == 1 ? 'active' : ''}}" id="p7">
                <div class="container text-end">
                    <div class="btn-group">
                        @if (config('renewal.invoice.backend') == 'dolibarr')
                        <button class="btn btn-info" type="button" id="invoiceRenewals">{{ _i('Generate invoice') }}</button>
                        @endif
                        <button class="btn btn-outline-info" type="button" id="renewalsExport">{{ _i('Export all') }}</button>
                        <button class="btn btn-info" type="button" id="renewalsInvoiced">{{ _i('Invoiced') }}</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $invoice_step == 2 ? 'active' : ''}}" id="p8">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="invoicesPaid">Paid</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 14 ? 'active' : ''}}" id="p9">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="sendLapsedRenewals">Lapse communication sent</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 10 ? 'active' : ''}}" id="p10">
                <div class="container text-end">
                    <button class="btn btn-secondary" type="button" disabled>Closed renewals</button>
                </div>
            </div>
            <div class="tab-pane lead {{ $invoice_step == 3 ? 'active' : ''}}" id="p11">
                <div class="container text-end">
                    <button class="btn btn-secondary" type="button" disabled>Paid invoices</button>
                </div>
            </div>
        </div>
        <nav class="mt-1">
            <div class="nav nav-tabs nav-fill" id="tabsGroup">
                <a class="nav-item nav-link {{ !$step && !$invoice_step ? 'active' : '' }}" href="#p1" data-bs-toggle="tab" data-step="0">First call</a>
                <a class="nav-item nav-link {{ $step == 2 ? 'active' : '' }}" href="#p2" data-bs-toggle="tab" data-step="2">Reminder</a>
                <a class="nav-item nav-link {{ $step == 4 ? 'active' : '' }}" href="#p3" data-bs-toggle="tab" data-step="4">Payment</a>
                @if (config('renewal.general.receipt_tabs'))
                <a class="nav-item nav-link {{ $step == 6 ? 'active' : '' }}" href="#p4" data-bs-toggle="tab" data-step="6">Receipts</a>
                <a class="nav-item nav-link {{ $step == 8 ? 'active' : '' }}" href="#p5" data-bs-toggle="tab" data-step="8">Receipts received</a>
                @endif
                <a class="nav-item nav-link {{ $step == 12 ? 'active' : '' }}" href="#p6" data-bs-toggle="tab" data-step="12">Abandoned</a>
                <a class="nav-item nav-link {{ $step == 14 ? 'active' : '' }}" href="#p9" data-bs-toggle="tab" data-step="14">Lapsed</a>
                <a class="nav-item nav-link {{ $step == 10 ? 'active' : '' }}" href="#p10" data-bs-toggle="tab" data-step="10">Closed</a>
                <a class="nav-item nav-link {{ $invoice_step == 1 ? 'active' : '' }}" href="#p7" data-bs-toggle="tab" data-invoice_step="1">Invoicing</a>
                <a class="nav-item nav-link {{ $invoice_step == 2 ? 'active' : '' }}" href="#p8" data-bs-toggle="tab" data-invoice_step="2">Invoiced</a>
                <a class="nav-item nav-link {{ $invoice_step == 3 ? 'active' : '' }}" href="#p11" data-bs-toggle="tab" data-invoice_step="3">Invoices paid</a>
            </div>
        </nav>
    </div>
    <div class="card-body pt-0">
        <table class="table table-striped table-sm">
            <thead>
                <tr class="row table-primary" id="filterFields">
                    <td class="col-2">
                        <input class="form-control form-control-sm" name="Name" value="{{ Request::get('Name') }}" placeholder="Client">
                    </td>
                    <td class="col-3">
                        <input class="form-control form-control-sm" name="Title" value="{{ Request::get('Title') }}" placeholder="Title">
                    </td>
                    <td class="col-1">
                        <input class="form-control form-control-sm" name="Case" value="{{ Request::get('Case') }}" placeholder="Matter">
                    </td>
                    <th class="col-3 text-center">
                        <div class="row">
                            <div class="col-2">
                                <input class="form-control form-control-sm px-0" name="Country" value="{{ Request::get('Country') }}" placeholder="Ctry">
                            </div>
                            <div class="col-2">
                                <input class="form-control form-control-sm px-0" name="Qt" value="{{ Request::get('Qt') }}" placeholder="Qt">
                            </div>
                            <div class="col-2">
                                <input id="grace" name="grace_period" type="checkbox" class="btn-check">
                                <label class="btn btn-outline-primary btn-sm" title="In grace period" for="grace">Grace</label>
                            </div>
                            <div class="col-3 p-1">
                                Cost
                            </div>
                            <div class="col-3 p-1">
                                Fee
                            </div>
                        </div>
                    </th>
                    <td class="col-2">
                        <div class="input-group">
                            <input type="date" class="form-control form-control-sm px-0" name="Fromdate" id="Fromdate" title="From selected date" value="{{ Request::get('Fromdate') }}">
                            <input type="date" class="form-control form-control-sm px-0" name="Untildate" id="Untildate" title="Until selected date" value="{{ Request::get('Untildate') }}">
                        </div>
                    </td>
                    <td class="col-1 text-center">
                        <input id="selectAll" type="checkbox" class="btn-check">
                        <label class="btn btn-outline-primary btn-sm" title="Select/unselect all" for="selectAll">&check;</label>
                    </td>
                </tr>
            </thead>
            <tbody id="renewalList">
                @if (count($renewals) == 0)
                <tr class="row text-danger">
                    The list is empty
                </tr>
                @else
                @foreach ($renewals as $task)
                    <tr class="row" data-resource="/task/{{ $task->id }}">
                        <td class="col-2">
                            {{ $task->client_name }}
                        </td>
                        <td class="col-3" nowrap>
                            {{ $task->short_title }}
                        </td>
                        <td class="col-1">
                            <a href="/matter/{{ $task->matter_id }}">
                                {{ $task->uid }}
                            </a>
                        </td>
                        <td class="col-3">
                            <div class="row">
                                <div class="col-2 text-center">
                                    {{ $task->country }}
                                </div>
                                <div class="col-2 text-center">
                                    {{ $task->detail }}
                                </div>
                                <div class="col-2 text-center">
                                    @if ($task->grace_period)
                                    <svg width="12" height="12" fill="currentColor"><use xlink:href="#hourglass-split"/></svg>
                                    @endif
                                </div>
                                <div class="col-3 text-end">
                                    {{ $task->cost }}
                                </div>
                                <div class="col-3 text-end">
                                    {{ $task->fee }}
                                </div>
                            </div>
                        </td>
                        <td class="col-2 text-center">
                            {{ Carbon\Carbon::parse($task->due_date)->isoFormat('L') }}
                            @if ($task->done)
                            <span class="text-success" title="Done">
                                <svg width="14" height="14" fill="currentColor"><use xlink:href="#check-circle-fill"/></svg>
                            </span>
                            @elseif ($task->due_date < now())
                            <span class="text-danger" title="Overdue">
                                <svg width="14" height="14" fill="currentColor"><use xlink:href="#exclamation-triangle-fill"/></svg>
                            </span>
                            @elseif ($task->due_date < now()->addWeeks(1))
                            <span class="text-warning" title="Urgent">
                                <svg width="14" height="14" fill="currentColor"><use xlink:href="#exclamation-triangle-fill"/></svg>
                            </span>
                            @endif
                        </td>
                        <td class="col-1 text-center">
                            <input id="{{ $task->id }}" class="clear-ren-task" type="checkbox">
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td>{{ $renewals->links() }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/renewal-index.js') }}" defer></script>
{{-- TODO: put this in the renewal-index.js file avoiding the blade directives --}}
<script>
    @if(config('renewal.invoice.backend') == 'dolibarr')
    invoiceRenewals.addEventListener("click", function (b) {
        msgAction = "invoicing";
        actionRenewals(b.target, msgAction, '/renewal/invoice/1')
    });
    @endif
    @if(config('renewal.general.receipt_tabs'))
        receiptRenewals.addEventListener("click", function (b) {
        msgAction = "registering receipt";
        actionRenewals(b.target, msgAction, '/renewal/receipt')
    });

    sendReceiptsRenewals.addEventListener("click", function (b) {
        msgAction = "closing renewals";
        actionRenewals(b.target, msgAction, '/renewal/closing')
    });
    @endif
</script>
@endsection
