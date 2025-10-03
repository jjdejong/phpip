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
            {{ __('Manage renewals') }}
            <a href="https://github.com/jjdejong/phpip/wiki/Renewal-Management" class="text-primary" target="_blank" title="{{ __('Help') }}">
                <svg width="16" height="16" fill="currentColor"><use xlink:href="#question-circle-fill"/></svg>
            </a>
            <a href="/logs" class="btn btn-info">{{ __('View logs') }}</a>
            <button id="clearFilters" type="button" class="btn btn-info float-right">&larrpl; {{ __('Clear filters') }}</button>
        </legend>
        <div class="tab-content">
            <div class="tab-pane {{ !$step && !$invoice_step ? 'active' : '' }}" id="p1">
                <div class="container text-end">
                    <div class="btn-group">
                        <button class="btn btn-info" type="button" id="callRenewals">{{ __('Send call email') }}</button>
                        <button class="btn btn-info" type="button" id="renewalsSent">{{ __('Call sent manually') }}</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $step == 2 ? 'active' : '' }}" id="p2">
                <div class="container text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-info" type="button" id="reminderRenewals">{{ __('Send reminder email') }}</button>
                        <button class="btn btn-outline-info" type="button" id="lastReminderRenewals" title="{{ __('Send reminder and enter grace period') }}">{{ __('Send last reminder email') }}</button>
                        <button class="btn btn-info" type="button" id="instructedRenewals" title="{{ __('Instructions received to pay') }}">{{ __('Payment order received') }}</button>
                        <button class="btn btn-info" type="button" id="abandonRenewals" title="{{ __('Abandon instructions received') }}">{{ __('Abandon') }}</button>
                        <button class="btn btn-info" type="button" id="lapsedRenewals" title="{{ __('Office lapse communication received') }}">{{ __('Lapsed') }}</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $step == 4 ? 'active' : ''}}" id="p3">
                <div class="container text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-info" type="button" id="xmlRenewals" title="{{ __('Generate xml files for EP or FR') }}">{{ __('Download XML order to pay') }}</button>
                        <button class="btn btn-info" type="button" id='doneRenewals'>{{ __('Paid') }}</button>
                    </div>
                </div>
            </div>
            @if (config('renewal.general.receipt_tabs'))
            <div class="tab-pane {{ $step == 6 ? 'active' : ''}}" id="p4">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="receiptRenewals">{{ __('Official receipts received') }}</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 8 ? 'active' : ''}}" id="p5">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="sendReceiptsRenewals">{{ __('Receipts sent') }}</button>
                </div>
            </div>
            @endif
            <div class="tab-pane {{ $step == 12 ? 'active' : ''}}" id="p6">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="lapsingRenewals">{{ __('Lapse') }}</button>
                </div>
            </div>
            <div class="tab-pane {{ $invoice_step == 1 ? 'active' : ''}}" id="p7">
                <div class="container text-end">
                    <div class="btn-group">
                        @if (config('renewal.invoice.backend') == 'dolibarr')
                        <button class="btn btn-info" type="button" id="invoiceRenewals">{{ __('Generate invoice') }}</button>
                        @endif
                        <button class="btn btn-outline-info" type="button" id="renewalsExport">{{ __('Export all') }}</button>
                        <button class="btn btn-info" type="button" id="renewalsInvoiced">{{ __('Invoiced') }}</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane {{ $invoice_step == 2 ? 'active' : ''}}" id="p8">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="invoicesPaid">{{ __('Paid') }}</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 14 ? 'active' : ''}}" id="p9">
                <div class="container text-end">
                    <button class="btn btn-info" type="button" id="sendLapsedRenewals">{{ __('Lapse communication sent') }}</button>
                </div>
            </div>
            <div class="tab-pane {{ $step == 10 ? 'active' : ''}}" id="p10">
                <div class="container text-end">
                    <button class="btn btn-secondary" type="button" disabled>{{ __('Closed renewals') }}</button>
                </div>
            </div>
            <div class="tab-pane lead {{ $invoice_step == 3 ? 'active' : ''}}" id="p11">
                <div class="container text-end">
                    <button class="btn btn-secondary" type="button" disabled>{{ __('Paid invoices') }}</button>
                </div>
            </div>
        </div>
        <nav class="mt-1">
            <div class="nav nav-tabs nav-fill" id="tabsGroup">
                <a class="nav-item nav-link {{ !$step && !$invoice_step ? 'active' : '' }}" href="#p1" data-bs-toggle="tab" data-step="0">{{ __('First call') }}</a>
                <a class="nav-item nav-link {{ $step == 2 ? 'active' : '' }}" href="#p2" data-bs-toggle="tab" data-step="2">{{ __('Reminder') }}</a>
                <a class="nav-item nav-link {{ $step == 4 ? 'active' : '' }}" href="#p3" data-bs-toggle="tab" data-step="4">{{ __('Payment') }}</a>
                @if (config('renewal.general.receipt_tabs'))
                <a class="nav-item nav-link {{ $step == 6 ? 'active' : '' }}" href="#p4" data-bs-toggle="tab" data-step="6">{{ __('Receipts') }}</a>
                <a class="nav-item nav-link {{ $step == 8 ? 'active' : '' }}" href="#p5" data-bs-toggle="tab" data-step="8">{{ __('Receipts received') }}</a>
                @endif
                <a class="nav-item nav-link {{ $step == 12 ? 'active' : '' }}" href="#p6" data-bs-toggle="tab" data-step="12">{{ __('Abandoned') }}</a>
                <a class="nav-item nav-link {{ $step == 14 ? 'active' : '' }}" href="#p9" data-bs-toggle="tab" data-step="14">{{ __('Lapsed') }}</a>
                <a class="nav-item nav-link {{ $step == 10 ? 'active' : '' }}" href="#p10" data-bs-toggle="tab" data-step="10">{{ __('Closed') }}</a>
                <a class="nav-item nav-link {{ $invoice_step == 1 ? 'active' : '' }}" href="#p7" data-bs-toggle="tab" data-invoice_step="1">{{ __('Invoicing') }}</a>
                <a class="nav-item nav-link {{ $invoice_step == 2 ? 'active' : '' }}" href="#p8" data-bs-toggle="tab" data-invoice_step="2">{{ __('Invoiced') }}</a>
                <a class="nav-item nav-link {{ $invoice_step == 3 ? 'active' : '' }}" href="#p11" data-bs-toggle="tab" data-invoice_step="3">{{ __('Invoices paid') }}</a>
            </div>
        </nav>
    </div>
    <div class="card-body pt-0">
        <table class="table table-striped table-sm">
            <thead>
                <tr class="row table-primary" id="filterFields">
                    <td class="col-2">
                        <input class="form-control form-control-sm" name="Name" value="{{ Request::get('Name') }}" placeholder="{{ __('Client') }}">
                    </td>
                    <td class="col-3">
                        <input class="form-control form-control-sm" name="Title" value="{{ Request::get('Title') }}" placeholder="{{ __('Title') }}">
                    </td>
                    <td class="col-1">
                        <input class="form-control form-control-sm" name="Case" value="{{ Request::get('Case') }}" placeholder="{{ __('Matter') }}">
                    </td>
                    <th class="col-3 text-center">
                        <div class="row">
                            <div class="col-2">
                                <input class="form-control form-control-sm px-0" name="Country" value="{{ Request::get('Country') }}" placeholder="{{ __('Ctry') }}">
                            </div>
                            <div class="col-2">
                                <input class="form-control form-control-sm px-0" name="Qt" value="{{ Request::get('Qt') }}" placeholder="{{ __('Qt') }}">
                            </div>
                            <div class="col-2">
                                <input id="grace" name="grace_period" type="checkbox" class="btn-check">
                                <label class="btn btn-outline-primary btn-sm" title="{{ __('In grace period') }}" for="grace">{{ __('Grace') }}</label>
                            </div>
                            <div class="col-3 p-1">
                                {{ __('Cost') }}
                            </div>
                            <div class="col-3 p-1">
                                {{ __('Fee') }}
                            </div>
                        </div>
                    </th>
                    <td class="col-2">
                        <div class="input-group">
                            <input type="date" class="form-control form-control-sm px-0" name="Fromdate" id="Fromdate" title="{{ __('From selected date') }}" value="{{ Request::get('Fromdate') }}">
                            <input type="date" class="form-control form-control-sm px-0" name="Untildate" id="Untildate" title="{{ __('Until selected date') }}" value="{{ Request::get('Untildate') }}">
                        </div>
                    </td>
                    <td class="col-1 text-center">
                        <input id="selectAll" type="checkbox" class="btn-check">
                        <label class="btn btn-outline-primary btn-sm" title="{{ __('Select/unselect all') }}" for="selectAll">&check;</label>
                    </td>
                </tr>
            </thead>
            <tbody id="renewalList">
                @if (count($renewals) == 0)
                <tr class="row text-danger">
                    {{ __('The list is empty') }}
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
                            {{ \Carbon\Carbon::parse($task->due_date)->isoFormat('L') }}
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
