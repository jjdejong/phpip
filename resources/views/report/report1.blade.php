<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
    <style>
        table, th, td {
            border-collapse: collapse;
            padding: 3px;
        }
        th {
            font-family: sans-serif;
            background-color: #cccccc;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
        h1 {
            background-color: #ccdddd;
        }
        h2 {
            background-color: #ddddcc;
        }
        .tdate {
            font-size: 80%;
            font-style: italic;
        }
        @media print {
            .pagebreak { 
                break-inside: avoid;
            }
        }
    </style>
    
    @php
    $previous_cat = "";
    $previous_family = "";
    @endphp
    @foreach ($matters as $matter)
        @if (  $matter['dead'] == 0 )
        @if ($matter['Cat'] !== $previous_cat)
            @if ($previous_cat != "")
                </tbody>
                </table>
                </div>
                <hr />
            @endif
            
            @switch( $matter['Cat'])
                @case('PAT')     
                    <h1>Patents</h1><br>
                        @break
                @case('TM')     
                    <h1>Trademarks</h1><br>
                        @break
                @case('DM')     
                    <h1>Design Patents</h1><br>
            @endswitch
            
            @php
                $previous_cat = $matter['Cat'];
                $new_cat = true;
            @endphp
        @endif
        
        @if (! str_starts_with($matter['Ref'], $previous_family) || $previous_family == "" || $new_cat)
            @if ($previous_family != "" && ! $new_cat)
                </tbody>
                </table>
                </div>
               <!-- <hr />-->
            @endif
            <hr />
            
            <div class="pagebreak">
            
            @if (  $matter['Title']  !== null)
                <h2>{!! $matter['Title'] !!}</h2><br>
            @endif
            @if ( $matter['Title2'] !== null)
                <h2>{!! $matter['Title2'] !!}</h2><br>
            @endif
            @if ( $matter['Title3'] !== null)
                <h2>{!! $matter['Title3'] !!}</h2><br>
            @endif
            
            Titulaire: {!! $matter['Applicant'] !!}<br>
            
            @if ( $matter['Inventor1'] !== "")
                Inventeur : {!! $matter['Inventor1'] !!}<br>
            @endif     
            
            <table class="inner-body" cellpadding="0" cellspacing="0">
                <!-- Body content -->
                <thead>
                    <tr>
                        <th>Your ref.</th>
                        <th>Country code</th>
                        <th>Filing</th>
                        <th>Publication</th>
                        <th>Grant</th>
                        <th>Status</th>
                        <th>Next renewal</th>
                        <th>Our ref.</th>
                    </tr>
                </thead>
                <tbody>
        @endif
        
        @php
            $previous_family = substr($matter['Ref'], 0, strpos($matter['Ref'], $matter['country']));
            $new_cat = false;
        @endphp
        <tr>
            <td>{!! $matter['ClRef'] !!}</td>
            <td>{!! $matter['country'] !!}</td>
            <td>{!! $matter['FilNo'] !!}<br /><span class="tdate">{!! empty($matter['Filed'])? "" : Carbon\Carbon::parse($matter['Filed'])->locale('en_US')->isoFormat('LL')  !!}</span></td>
                <td>{!! $matter['PubNo'] !!}<br /><span class="tdate">{!! empty($matter['Published'])? "" : Carbon\Carbon::parse($matter['Published'])->locale('en_US')->isoFormat('LL') !!}</span></td>
                <td>{!! $matter['GrtNo'] !!}<br /><span class="tdate">{!! empty($matter['Granted'])? "" : Carbon\Carbon::parse($matter['Granted'])->locale('en_US')->isoFormat('LL') !!}</span></td>
                <td>
                    @if (  $matter['dead'] == 1 )
                        Fermé : {!! $matter['Status'] !!}
                    @else
                        {!! $matter['Status'] !!}
                    @endif
                </td>
                <td><span class="tdate">{!! empty($matter['next'])? "" : Carbon\Carbon::parse($matter['next'])->locale('en_US')->isoFormat('LL') !!}</span></td>
            <td>{!! $matter['Ref'] !!}</td>
        </tr>
        @endif
    @endforeach
    </tbody>
    </table>
    </div>
    <hr />
</body>
</html>
                    
                    
