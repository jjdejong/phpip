<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title></title>
  </head>
  <body>
    @if ($crash)
    <p><strong>The Renewr sync stopped before the end:</strong> {{ $crash }}</p>
    <p>The renewals of the patents not reached were not updated.</p>
    @endif

    @if ($new)
    <h3>New issues</h3>
    @foreach ($kinds as $kind => $label)
      @if (!empty($new[$kind]))
      <p><strong>{{ $label }}</strong></p>
      <ul>
        @foreach ($new[$kind] as $message)
        <li>{{ $message }}</li>
        @endforeach
      </ul>
      @endif
    @endforeach
    @endif

    <h3>All issues of this run</h3>
    <table border="1" cellspacing="0" cellpadding="5">
      <tr>
        <th>Issue</th>
        <th>Total</th>
        <th>New</th>
      </tr>
      @foreach ($kinds as $kind => $label)
      @if (!empty($issues[$kind]))
      <tr>
        <td>{{ $label }}</td>
        <td align="right">{{ count($issues[$kind]) }}</td>
        <td align="right">{{ count($new[$kind] ?? []) }}</td>
      </tr>
      @endif
      @endforeach
    </table>

    <p>
      Patents processed: {{ $stats['patsprocessed'] }}.
      Annuities updated: {{ $stats['updated'] }}, inserted: {{ $stats['inserted'] }}.
      Dead matters skipped: {{ $stats['dead'] }}.
    </p>
  </body>
</html>
