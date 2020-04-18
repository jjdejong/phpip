<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
<style>
    @media  only screen and (max-width: 600px) {
        .inner-body {
            width: 100% !important;
        }

        .footer {
            width: 100% !important;
        }
    }

    @media  only screen and (max-width: 500px) {
        .button {
            width: 100% !important;
        }
    }
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
{{ $dest }}<BR>
Veuillez trouver ci-joint une liste de titres dont le renouvellement arrive à échéance prochainement. Je vous remercie de me transmettre vos instructions accompagnées du règlement correspondant, de préférence avant le {{ $instruction_date }}.<BR>

                <!-- Email Body -->
                        <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif;  background-color: #FFFFFF; margin: 0 auto; padding: 0; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; border:1px solid black; border-collapse: collapse;">
                            <!-- Body content -->
                        <thead>
                            <tr style="border:1px solid black; border-collapse: collapse;">
                                   <th>Titre</th><th>Année</th><th>Échéance</th><th>Taxe</th><th>Honoraires</th><th>Taux TVA</th><th>Total HT (€)</th><th>Total TTC (€)</th></tr>
                        </thead>
                        <tbody>
                                        @foreach ($renewals as $ren)
                                        <tr><td style="width:40%">{!! $ren['desc'] !!}</td><td align="center">{{ $ren['annuity'] }}</td><td align="center">{{ $ren['due_date'] }}</td><td align="right">{{ $ren['cost'] }}</td><td align="right">{{ $ren['fee'] }}</td><td align="right">{{ $ren['tx_tva'] }}</td><td align="right">{{ $ren['total_ht']  }}</td><td align="right">{{ $ren['total']  }}</td></tr>
                                        @endforeach
                                        <tr><td align="right" colspan="6"> Total (HT/TTC) :&nbsp;</td><td align="right">{{ $total_ht }}</td><td align="right">{{ $total }}</td></tr>
                        </tbody>
                                
                        </table>
Offre valide jusqu'au {{ $validity_date}}.<BR>
    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Sincères salutations,<br>{{ Auth::user()->name }}</p>
</body>
</html>
<table>