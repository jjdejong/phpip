<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
@php
    app()->setLocale($language);
@endphp
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
    <!-- Email Body -->
    <p>{{ $dest }}</p>
    <p>{!! $template->body !!}</p>
    <table class="inner-body" cellpadding="0" cellspacing="0">
        <!-- Body content -->
        <thead>
            <tr>
                <th>{{ __('Title') }}</th><th>{{ __('Jurisdiction') }}</th><th>{{ __('Year') }}</th><th>{{ __('Due date') }}</th><th>{{ __('Fee') }}</th><th>{{ __('Service charge') }}</th>
                <th>{{ __('Total excl. VAT') }} (€)</th>
                @if (config('renewal.general.vat_column'))
                <th>{{ __('VAT rate') }}</th>
                <th>{{ __('Total incl. VAT') }} (€)</th>
                @endif
                <th>{{ __('Decision') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($renewals as $ren)
            <tr>
                <td style="width:40%">{!! $ren['desc'] !!}</td>
                <td style="text-align: center;">{{ $ren['country'] }}</td>
                <td style="text-align: center;">{{ $ren['annuity'] }}</td>
                <td style="text-align: center;">{{ $ren['due_date'] }}</td>
                <td style="text-align: right;">{{ $ren['cost'] }}</td>
                <td style="text-align: right;">{{ $ren['fee'] }}</td>
                <td style="text-align: right;">{{ $ren['total_ht']  }}</td>
                @if (config('renewal.general.vat_column'))
                <td style="text-align: right;">{{ $ren['vat_rate'] }}</td>
                <td style="text-align: right;">{{ $ren['total']  }}</td>
                @endif
                <td style="text-align: center;"></td>
            </tr>
            @endforeach
            <tr>
                <td style="text-align: right;" colspan="6">{{ __('Total') }}&nbsp;:</td>
                <td style="text-align: right;">{{ $total_ht }}</td>
                @if (config('renewal.general.vat_column'))
                <td></td>
                <td style="text-align: right;">{{ $total }}</td>
                @endif
                <td></td>
            </tr>
        </tbody>
    </table>
    <p>{{ __('Offer valid until') }} {{ $validity_date}}.</p>
    <p>{{ __('Best regards') }},</p>
    <p>{{ Auth::user()->name }}</p>
</body>
</html>
