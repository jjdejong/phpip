<?php

namespace App\Http\Controllers;

use App\Mail\sendCall;
use App\Models\MatterActors;
use App\Models\RenewalsLog;
use App\Models\Task;
use App\Models\Actor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Helpers\FormatHelper;

class RenewalController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $MyRenewals = $request->input('my_renewals');
        $filters = $request->except([
            'my_renewals',
            'page',
        ]);
        $step = $request->step;
        $invoice_step = $request->invoice_step;

        // Get list of active renewals
        $renewals = Task::renewals();
        if ($step == 0) {
            $renewals->where('matter.dead', 0);
        }
        if ($MyRenewals) {
            $renewals->where('assigned_to', Auth::user()->login);
        }
        $with_step = false;
        $with_invoice = false;
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value != '') {
                    switch ($key) {
                        case 'Title':
                            $renewals->where('tit.value', 'LIKE', "%$value%");
                            break;
                        case 'Case':
                            $renewals->where('caseref', 'LIKE', "$value%");
                            break;
                        case 'Qt':
                            $renewals->where('task.detail', 'LIKE', "$value%");
                            break;
                        case 'Fromdate':
                            $renewals->where('due_date', '>=', "$value");
                            break;
                        case 'Untildate':
                            $renewals->where('due_date', '<=', "$value");
                            break;
                        case 'Name':
                            $renewals->where(DB::raw('IFNULL(pa_cli.name, clic.name)'), 'LIKE', "$value%");
                            break;
                        case 'Country':
                            $renewals->where('matter.country', 'LIKE', "$value%");
                            break;
                        case 'grace':
                            $renewals->where('grace_period', "$value");
                            break;
                        case 'step':
                            $renewals->where('step', "$value");
                            if ($value != 0) {
                                $with_step = true;
                            }
                            break;
                        case 'invoice_step':
                            $renewals->where('invoice_step', "$value");
                            if ($value != 0) {
                                $with_invoice = true;
                            }
                            break;
                        default:
                            $renewals->where($key, 'LIKE', "$value%");
                            break;
                    }
                }
            }
        }
        // Only display pending renewals at the beginning of the pipeline (CHECK: $with_invoice may not be necessary)
        if (!($with_step || $with_invoice)) {
            $renewals->where('done', 0);
        }
        // Order by most recent renewals first in the "Closed" and "Invoice paid" steps
        if ($step == 10 || $invoice_step == 3) {
            $renewals->orderByDesc('due_date');
        }
        $renewals = $renewals->simplePaginate(config('renewal.general.paginate', 25));
        // Adjust the cost and fee of each renewal based un customized settings
        $renewals->transform(function ($ren) {
            $this->adjustFees($ren, $cost, $fee);
            $ren->cost = $cost;
            $ren->fee = $fee;

            return $ren;
        });
        $renewals->appends($request->input())->links(); // Keep URL parameters in the paginator links

        return view('renewals.index', compact('renewals', 'step', 'invoice_step'));
    }

    public function firstcall(Request $request, int $send)
    {
        $notify_type[0] = 'first';
        $rep = count($request->task_ids);
        if ($send == 1) {
            $rep = $this->_call($request->task_ids, $notify_type, false);
        }
        if (is_numeric($rep)) {
            // Move the renewal task to step 2 : reminder
            Task::whereIn('id', $request->task_ids)->update(['step' => 2]);

            return response()->json(['success' => 'Calls created for ' . $rep . ' renewals']);
        } else {
            return response()->json(['error' => $rep], 501);
        }
    }

    public function remindercall(Request $request)
    {
        $notify_type[0] = 'first';
        $notify_type[1] = 'warn';
        $rep = $this->_call($request->task_ids, $notify_type, true);
        if (is_numeric($rep)) {
            return response()->json(['success' => 'Calls sent for ' . $rep . ' renewals']);
        } else {
            return response()->json(['error' => $rep], 501);
        }
    }

    public function lastcall(Request $request)
    {
        $notify_type[0] = 'last';
        $rep = $this->_call($request->task_ids, $notify_type, true);
        if (is_numeric($rep)) {
            // Move the renewal task to grace_period 1
            Task::whereIn('id', $request->task_ids)->update(['grace_period' => 1]);

            return response()->json(['success' => 'Calls sent for ' . $rep . ' renewals']);
        } else {
            return response()->json(['error' => $rep], 501);
        }
    }

    private function adjustFees($renewal, &$cost, &$fee)
    {
        if ($renewal->grace_period && strtotime($renewal->done_date) < $renewal->due_date) {
            $fee_factor = config('renewal.validity.fee_factor', 1.0);
        } else {
            $fee_factor = 1.0;
        }

        if ($renewal->table_fee) {
            $this->adjustTableFees($renewal, $cost, $fee);
        } else {
            $this->adjustTaskFees($renewal, $cost, $fee);
        }
        
        $fee *= $fee_factor;
    }

    private function adjustTableFees($renewal, &$cost, &$fee)
    {
        if ($renewal->grace_period) {
            $cost = $renewal->sme_status ? $renewal->cost_sup_reduced : $renewal->cost_sup;
            $fee = $renewal->sme_status ? $renewal->fee_sup_reduced : $renewal->fee_sup;
        } else {
            $cost = $renewal->sme_status ? $renewal->cost_reduced : $renewal->cost;
            $fee = $renewal->sme_status ? $renewal->fee_reduced : $renewal->fee;
        }

        if ($renewal->discount > 1) {
            $fee = $renewal->discount;
        } else {
            $fee *= (1.0 - $renewal->discount);
        }
    }

    private function adjustTaskFees($renewal, &$cost, &$fee) 
    {
        $cost = $renewal->cost;
        $fee = $renewal->fee - config('renewal.invoice.default_fee', 145);
        
        if ($renewal->discount > 1) {
            $fee += $renewal->discount;
        } else {
            $fee += (1.0 - $renewal->discount) * config('renewal.invoice.default_fee', 145);
        }
    }

    private function _call($ids, $notify_type, $reminder)
    {
        if (empty($ids)) {
            return 'No renewal selected.';
        }

        $sum = 0;
        $newjob = RenewalsLog::max('job_id') + 1;

        for ($grace = 0; $grace < count($notify_type); $grace++) {
            // Get and process renewals for this grace period
            $renewalsData = $this->processRenewals($ids, $grace, $notify_type[$grace]);
            if (empty($renewalsData['renewals'])) {
                continue;
            }

            // Create logs for the processed renewals
            $logs = $this->processLogs($renewalsData['renewals'], $newjob, $notify_type[$grace]);
            if (!empty($logs)) {
                RenewalsLog::insert($logs);
            }

            // Send emails grouped by client
            $emailResult = $this->sendEmails($renewalsData, $notify_type[$grace], $reminder);
            if (is_string($emailResult)) {
                return $emailResult; // Return error message if email sending failed
            }

            $sum += count($renewalsData['renewals']);
        }

        return $sum;
    }

    private function processRenewals($ids, $grace, $notifyType)
    {
        $renewals = Task::renewals()
            ->whereIn('task.id', $ids)
            ->where('grace_period', $grace)
            ->orderBy('pa_cli.name')
            ->get();

        $processedRenewals = [];
        $clientGroups = [];
        $totals = [];

        foreach ($renewals as $ren) {
            $processedRenewal = $this->prepareRenewalData($ren, $grace);
            $processedRenewals[] = $ren; // Keep original model for logs

            // Group renewals by client for email sending
            $clientGroups[$ren->client_id][] = $processedRenewal;

            // Calculate totals for each client group
            if (!isset($totals[$ren->client_id])) {
                $totals[$ren->client_id] = ['total' => 0, 'total_ht' => 0];
            }

            // Use the processed values for totals
            $total = (float)str_replace([' ', ','], ['', '.'], $processedRenewal['total']);
            $total_ht = (float)str_replace([' ', ','], ['', '.'], $processedRenewal['total_ht']);
            $totals[$ren->client_id]['total'] += $total;
            $totals[$ren->client_id]['total_ht'] += $total_ht;
        }

        return [
            'renewals' => $processedRenewals,
            'clientGroups' => $clientGroups,
            'totals' => $totals
        ];
    }

    private function prepareRenewalData($ren, $grace)
    {
        if (empty($ren->language)) {
            $ren->language = 'fr';
        }

        $config_prefix = 'renewal.description.' . $ren->language;
                    $due_date = Carbon::parse($ren->due_date)->locale($ren->language);
        if ($grace) {
            $due_date->addMonths(6);
        }

        // Prepare basic renewal data
        $renewal = [
            'caseref' => $ren->caseref,
            'matter_id' => $ren->matter_id,
            'language' => $ren->language,
            'due_date' => $due_date->format('Y-m-d'),
            'due_date_formatted' => FormatHelper::formatDate($due_date, 'L'),
            'country' => match ($ren->language) {
                'fr' => $ren->country_FR,
                'de' => $ren->country_DE,
                default => $ren->country_EN,
            },
            'annuity' => intval($ren->detail)
        ];

        // Prepare description
        $desc = sprintf(config($config_prefix . '.line1'), $ren->uid, $ren->number);
        if ($ren->event_name == 'FIL') {
            $desc .= config($config_prefix . '.filed');
        }
        if ($ren->event_name == 'GRT' || $ren->event_name == 'PR') {
            $desc .= config($config_prefix . '.granted');
        }
                    $desc .= Carbon::parse($ren->event_date)->locale($ren->language)->isoFormat('LL');
        if ($ren->client_ref != '') {
            $desc .= '<BR>' . sprintf(config($config_prefix . '.line2'), $ren->client_ref);
        }
        if ($ren->title != '') {
            $desc .= '<BR>' . sprintf(
                config($config_prefix . '.line3'),
                $ren->title == '' ? $ren->short_title : $ren->title
            );
        }
        $renewal['desc'] = $desc;

        // Calculate fees
        $vat_rate = config('renewal.invoice.vat_rate', 0.2);
        $this->adjustFees($ren, $cost, $fee);

        $renewal['vat_rate'] = $vat_rate * 100;
        $renewal['cost'] = number_format($cost, 2, ',', ' ');
        $renewal['fee'] = number_format($fee, 2, ',', ' ');
        $renewal['tva'] = $fee * $vat_rate;
        $renewal['total_ht'] = number_format($fee + $cost, 2, ',', ' ');
        $renewal['total'] = number_format($fee * (1 + $vat_rate) + $cost, 2, ',', ' ');

        return $renewal;
    }

    private function processLogs($renewals, $newjob, $notifyType)
    {
        $logs = [];
        $from_grace = ($notifyType == 'last') ? 0 : null;
        $to_grace = ($notifyType == 'last') ? 1 : null;

        foreach ($renewals as $ren) {
            $logs[] = [
                'task_id' => $ren->id,
                'job_id' => $newjob,
                'from_step' => $ren->step,
                'to_step' => 2,
                'creator' => Auth::user()->login,
                'created_at' => now(),
                'from_grace' => $from_grace,
                'to_grace' => $to_grace
            ];
        }

        return $logs;
    }

    private function sendEmails($renewalsData, $notifyType, $reminder)
    {
        foreach ($renewalsData['clientGroups'] as $clientId => $renewals) {
            // Calculate validity and instruction dates
            $dueDate = Carbon::parse($renewals[0]['due_date']);

            $validityDate = $notifyType == 'last'
                ? FormatHelper::formatDate($dueDate->copy()->subDays(config('renewal.validity.before_last')), 'LL')
                : FormatHelper::formatDate($dueDate->copy()->subDays(config('renewal.validity.before')), 'LL');

            $instructionDate = $notifyType != 'last'
                ? FormatHelper::formatDate($dueDate->copy()->subDays(config('renewal.validity.instruct_before')), 'LL')
                : null;

            // Get contacts
            $contacts = MatterActors::select('email', 'name', 'first_name')
            ->where('matter_id', $renewals[0]['matter_id'])
            ->where('role_code', 'CNT')
            ->get();

            if ($contacts->isEmpty()) {
                $contact = Actor::where('id', $clientId)->first();
                if ($contact->email == '' && config('renewal.general.mail_recipient') == 'client') {
                    return 'No email address for ' . $contact->name;
                }
                $contacts = collect([$contact]);
            }

            // Prepare email data
            $recipient = config('renewal.general.mail_recipient') == 'client'
            ? $contacts
                : Auth::user();

            $dest = config('renewal.general.mail_recipient') == 'client'
            ? ($renewals[0]['language'] == 'en' ? 'Dear Sirs, ' : 'Bonjour, ')
            : collect($contacts)->pluck('email')->implode(', ');

            $reminderPrefix = $reminder
                ? ($renewals[0]['language'] == 'en' ? '[REMINDER] ' : '[RAPPEL] ')
                : '';

            // Send email
            Mail::to($recipient)
                ->cc(Auth::user())
                ->send(new SendCall(
                    $notifyType,
                    array_values($renewals), // Convert to indexed array to ensure proper collection handling
                    $validityDate,
                    $instructionDate,
                    number_format($renewalsData['totals'][$clientId]['total'], 2, ',', ' '),
                    number_format($renewalsData['totals'][$clientId]['total_ht'], 2, ',', ' '),
                    $reminderPrefix,
                    $dest
                ));
        }

        return true;
    }


    public function topay(Request $request)
    {
        if (isset($request->task_ids)) {
            Task::whereIn('id', $request->task_ids)->update(['step' => 4, 'invoice_step' => 1]);
            // For logs
            $newjob = RenewalsLog::max('job_id');
            $newjob++;
            $data = [];
            $date_now = now();
            foreach ($request->task_ids as $ren_id) {
                $log_line = [
                    'task_id' => $ren_id,
                    'job_id' => $newjob,
                    'from_step' => 2,
                    'to_step' => 4,
                    'from_invoice' => 0,
                    'to_invoice' => 1,
                    'creator' => Auth::user()->login,
                    'created_at' => $date_now,
                ];
                $data[] = $log_line;
            }
            RenewalsLog::insert($data);

            return response()->json(['success' => 'Marked as to pay']);
        } else {
            return response()->json(['error' => 'No renewal selected.']);
        }
    }

    public function invoice(Request $request, int $toinvoice)
    {
        if (isset($request->task_ids)) {
            $query = Task::renewals()->whereIn('task.id', $request->task_ids);
        } else {
            return response()->json(['error' => 'No renewal selected.']);
        }
        $num = 0;
        if (config('renewal.invoice.backend') == 'dolibarr' && $toinvoice) {
            $resql = $query->orderBy('client_name')->get();
            $previousClient = null;
            $firstPass = true;
            // get from config/renewal.php
            $apikey = config('renewal.api.DOLAPIKEY');
            if ($apikey == null) {
                return response()->json(['error' => 'Api is not configured']);
            }
            logger('Facturation dans Dolibarr');
            if ($resql) {
                $num = $resql->count();
                if ($num == 0) {
                    return response()->json(['error' => 'No renewal selected.']);
                } else {
                    $i = 0;
                    $earlier = '';
                    foreach ($resql as $ren) {
                        $client = $ren->client_name;
                        logger('Ligne ' . $i);
                        if ($firstPass) {
                            // retrouve la correspondance de société
                            $result = $this->_client($client, $apikey);
                            if (isset($result['error']) && $result['error']['code'] >= '404') {
                                return response()->json(['error' => "$client not found in Dolibarr.\n"]);
                            }
                            $firstPass = false;
                            $soc_res = $result[0];
                            $earlier = strtotime($ren['due_date']);
                        } else {
                            $earlier = min($earlier, strtotime($ren['due_date']));
                        }
                        $desc = "$ren->uid : Annuité pour l'année $ren->detail du titre $ren->number";
                        if ($ren->event_name == 'FIL') {
                            $desc .= ' déposé le ';
                        }
                        if ($ren->event_name == 'GRT' || $ren->event_name == 'PR') {
                            $desc .= ' délivré le ';
                        }
                        $desc .= FormatHelper::formatDate(Carbon::parse($ren->event_date), 'LL');
                        // TODO select preposition 'en, au, aux' according to country
                        $desc .= ' en ' . $ren->country_FR;
                        if ($ren->title != '') {
                            $desc .= "\nSujet : $ren->title";
                        }
                        if ($ren->client_ref != '') {
                            $desc .= " ($ren->client_ref)";
                        }
                        $desc .= "\nÉchéance le " . FormatHelper::formatDate(Carbon::parse($ren->due_date), 'LL');
                        // Détermine le taux de tva
                        if ($soc_res['tva_intra'] == '' || substr($soc_res['tva_intra'], 0, 2) == 'FR') {
                            $vat_rate = 0.2;
                        } else {
                            $vat_rate = 0.0;
                        }

                        $this->adjustFees($ren, $cost, $fee);
                        if ($cost != 0) {
                            $desc .= "\nHonoraires pour la surveillance et le paiement";
                        } else {
                            $desc .= "\nHonoraires et taxe";
                        }
                        $newlines[] = [
                            'desc' => $desc,
                            'product_type' => 1,
                            'tva_tx' => ($vat_rate * 100),
                            'remise_percent' => 0,
                            'qty' => 1,
                            'subprice' => $fee,
                            'total_tva' => $fee * $vat_rate,
                            'total_ttc' => $fee * (1.0 + $vat_rate),
                        ];
                        logger('Ajout ligne ' . $desc);
                        if ($cost != 0) {
                            // Ajout d'une deuxième ligne
                            $newlines[] = [
                                'product_type' => 1,
                                'desc' => 'Taxe',
                                'tva_tx' => 0.0,
                                'remise_percent' => 0,
                                'qty' => 1,
                                'subprice' => $cost,
                                'total_tva' => 0,
                                'total_ttc' => $cost,
                            ];
                        }
                        $previousClient = $client;
                        $i++;
                        if ($i < $num) {
                            $client = $resql[$i]->client_name;
                        }
                        if ($client != $previousClient || $i == $num) {
                            // Create invoice
                            $newprop = [
                                'socid' => $soc_res['id'],
                                'date' => time(),
                                'cond_reglement_id' => 1,
                                'mode_reglement_id' => 2,
                                'lines' => $newlines,
                                'fk_account' => config('renewal.api.fk_account'),
                            ];
                            $rc = $this->createInvoice($newprop, $apikey); // invoice creation
                            if ($rc[0] != 0) {
                                return response()->json(['error' => $rc[1]]);
                            }
                            $newlines = [];
                            $firstPass = true;
                        }
                    }
                }
            }
        }
        // Move the renewal task to step: invoiced
        Task::whereIn('id', $request->task_ids)->update(['invoice_step' => 2]);

        return response()->json(['success' => "Invoices created for $num renewals"]);
    }

    public function paid(Request $request)
    {
        if (!isset($request->task_ids)) {
            return response()->json(['error' => 'No renewal selected.']);
        }
        // Move the renewal task to step: invoice paid
        $num = Task::whereIn('id', $request->task_ids)->update(['invoice_step' => 3]);

        return response()->json(['success' => "$num invoices paid"]);
    }

    public function export(Request $request)
    {
        $export = Task::renewals()->where('invoice_step', 1)
            ->orderBy('pmal_cli.actor_id')->get();
        $export->transform(function ($ren) {
            $this->adjustFees($ren, $cost, $fee);
            $ren->cost = $cost;
            $ren->fee = $fee;

            return $ren;
        });
        $captions = config('renewal.invoice.captions');
        // array_push($captions, 'cost_calc', 'fee_calc');
        $export_csv = fopen('php://memory', 'w');
        fputcsv($export_csv, $captions, ';');
        foreach ($export->toArray() as $row) {
            fputcsv($export_csv, array_map('utf8_decode', $row), ';');
        }
        rewind($export_csv);
        $filename = now()->format('YmdHis') . '_invoicing.csv';

        return response()->stream(
            function () use ($export_csv) {
                fpassthru($export_csv);
            },
            200,
            ['Content-Type' => 'application/csv', 'Content-disposition' => 'attachment; filename=' . $filename]
        );
    }

    private function _client($client, $apikey)
    {
        // Search for client correspondence in Dolibarr
        $curl = curl_init();
        $httpheader = ['DOLAPIKEY: ' . $apikey];
        $data = ['sqlfilters' => '(t.nom:like:"' . $client . '%")'];
        // Get from config/renewal.php
        $url = config('renewal.api.dolibarr_url') . '/thirdparties?' . http_build_query($data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);
    }

    public function createInvoice($newprop, $apikey)
    {
        // Create invoice
        $curl = curl_init();
        $url = config('renewal.api.dolibarr_url') . '/invoices';
        curl_setopt($curl, CURLOPT_POST, 1);
        $httpheader = ['DOLAPIKEY: ' . $apikey];
        $httpheader[] = 'Content-Type:application/json';
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($newprop));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        $result = json_decode($result, true);

        if (isset($result['error'])) {
            // "Error creating the invoice.\n";
            return [-1, $result['error']];
        } elseif ($status = 0) {
            return [-1, 'Invoice API is not reachable'];
        } else {
            return [0, $result];
        }
    }

    /**
     * clear selected renewals.
     */
    public function done(Request $request)
    {
        if (isset($request->task_ids)) {
            $query = Task::renewals()->whereIn('task.id', $request->task_ids);
        } else {
            return response()->json(['error' => 'No renewal selected.']);
        }
        $resql = $query->get();

        $updated = 0;
        // For logs
        $newjob = RenewalsLog::max('job_id');
        $newjob++;
        $date_now = now();

        foreach ($resql as $ren) {
            $task = Task::find($ren->id);
            $task->done_date = now();
            $log_line = [
                'task_id' => $ren->id,
                'job_id' => $newjob,
                'from_step' => $ren->step,
                'to_step' => 6,
                'creator' => Auth::user()->login,
                'created_at' => $date_now,
            ];
            $task->step = 6;
            $returncode = $task->save();
            if ($returncode) {
                $updated++;
            }
            $data_log[] = $log_line;
        }
        RenewalsLog::insert($data_log);

        return response()->json(['success' => strval($updated) . ' renewals cleared']);
    }

    /**
     * register receipts.
     */
    public function receipt(Request $request)
    {
        if (isset($request->task_ids)) {
            $query = Task::renewals()->whereIn('task.id', $request->task_ids);
        } else {
            return response()->json(['error' => 'No renewal selected.']);
        }
        $resql = $query->get();

        // For logs
        $newjob = RenewalsLog::max('job_id');
        $newjob++;
        $data_log = [];

        $updated = 0;
        foreach ($resql as $ren) {
            $task = Task::find($ren->id);
            $task->step = 8;
            $returncode = $task->save();
            if ($returncode) {
                $updated++;
            }
            $log_line = [
                'task_id' => $ren->id,
                'job_id' => $newjob,
                'from_step' => $task->step,
                'to_step' => 8,
                'creator' => Auth::user()->login,
                'created_at' => now(),
            ];
            $data_log[] = $log_line;
        }
        RenewalsLog::insert($data_log);

        return response()->json(['success' => strval($updated) . ' receipts registered']);
    }

    /**
     * closing the task.
     */
    public function closing(Request $request)
    {
        if (isset($request->task_ids)) {
            $query = Task::renewals()->whereIn('task.id', $request->task_ids);
        } else {
            return response()->json(['error' => 'No renewal selected.']);
        }
        $resql = $query->get();

        // For logs
        $newjob = RenewalsLog::max('job_id');
        $newjob++;
        $data_log = [];
        $updated = 0;
        foreach ($resql as $ren) {
            $task = Task::find($ren->id);
            $log_line = [
                'task_id' => $ren->id,
                'job_id' => $newjob,
                'from_step' => $task->step,
                'to_step' => 10,
                'from_done' => $task->done,
                'to_done' => 1,
                'creator' => Auth::user()->login,
                'created_at' => now(),
            ];
            if ($task->done) {
                $task->step = -1;
            } else {
                $task->step = 10;
            }
            $returncode = $task->save();
            if ($returncode) {
                $updated++;
                $data_log[] = $log_line;
            }
        }
        RenewalsLog::insert($data_log);

        return response()->json(['success' => strval($updated) . ' closed']);
    }

    /**
     * Abandon. Now, we wait for lapse.
     */
    public function abandon(Request $request)
    {
        if (isset($request->task_ids)) {
            $query = Task::renewals()->whereIn('task.id', $request->task_ids);
        } else {
            return response()->json(['error' => 'No renewal selected.']);
        }
        $renewals = $query->get();
        // For logs
        $newjob = RenewalsLog::max('job_id');
        $newjob++;
        $data_log = [];

        $updated = 0;
        foreach ($renewals as $ren) {
            $task = Task::find($ren->id);
            $log_line = [
                'task_id' => $ren->id,
                'job_id' => $newjob,
                'from_step' => $task->step,
                'to_step' => 12,
                'from_done' => $task->done,
                'to_done' => 1,
                'creator' => Auth::user()->login,
                'created_at' => now(),
            ];
            $task->step = 12;
            $returncode = $task->save();
            // Insert "Abandoned" event
            $task->matter->events()->create(['code' => 'ABA', 'event_date' => now()]);
            if ($returncode) {
                $updated++;
                $data_log[] = $log_line;
            }
        }
        RenewalsLog::insert($data_log);

        return response()->json(['success' => strval($updated) . ' abandons registered']);
    }

    /**
     * Lapse communication received. We will send it soon.
     */
    public function lapsing(Request $request)
    {
        if (isset($request->task_ids)) {
            $query = Task::renewals()->whereIn('task.id', $request->task_ids);
        } else {
            return response()->json(['error' => 'No renewal selected.']);
        }
        $resql = $query->get();

        // For logs
        $newjob = RenewalsLog::max('job_id');
        $newjob++;

        $updated = 0;
        foreach ($resql as $ren) {
            $task = Task::find($ren->id);
            $task->step = 14;
            $returncode = $task->save();
            // Insert "Lapsed" event
            $task->matter->events()->create(['code' => 'LAP', 'event_date' => now()]);
            if ($returncode) {
                $updated++;
            }
            // For logs
            $data_log = [];
            $log_line = [
                'task_id' => $ren->id,
                'job_id' => $newjob,
                'from_step' => $task->step,
                'to_step' => 14,
                'creator' => Auth::user()->login,
                'created_at' => now(),
            ];
            $data_log[] = $log_line;
        }
        RenewalsLog::insert($data_log);

        return response()->json(['success' => strval($updated) . ' communications registered']);
    }

    /**
     * Generate order.
     */
    public function renewalOrder(Request $request)
    {
        $tids = $request->task_ids;
        $procedure = '';
        $prev_procedure = '';
        // For logs
        $newjob = RenewalsLog::max('job_id');
        $newjob++;
        $data_log = [];

        $clear = boolval($request->clear);
        $done_date = now()->isoFormat('L');
        $xml = new \SimpleXMLElement(config('renewal.xml.body'));
        if ($xml->header->sender->name == 'NAME') {
            $xml->header->sender->name = Auth::user()->name;
        }
        $xml->header->{'payment-reference-id'} = 'ANNUITY ' . now()->format('Ymd');
        $total = 0;
        $first = true;
        $renewals = Task::renewals()->whereIn('task.id', $tids)->get();
        foreach ($renewals as $renewal) {
            $procedure = $renewal->country;
            if ($first) {
                $prev_procedure = $procedure;
                $first = false;
            } else {
                if ($prev_procedure != $procedure) {
                    // The order can only be for once juridiction
                    return response()->json(['error' => 'More than one juridiction is selected'], 501);
                }
            }
            $country = $renewal->country;
            if ($country == 'EP') {
                // Use fee code from EPO
                $fee_code = '0' . strval(intval($renewal->detail) + 30);
            } else {
                $fee_code = $renewal->detail;
            }
            if ($renewal->grace_period) {
                $cost = $renewal->sme_status ? $renewal->cost_sup_reduced : $renewal->cost_sup;
            } else {
                $cost = $renewal->sme_status ? $renewal->cost_reduced : $renewal->cost;
            }
            $total += $cost;
            if ($renewal->origin == 'EP') {
                $number = preg_replace('/[^0-9]/', '', $renewal->pub_num);
                $country = 'EP';
            } else {
                $number = preg_replace('/[^0-9]/', '', $renewal->fil_num);
            }
            $fees = $xml->detail->addChild('fees');
            $fees->addAttribute('procedure', $procedure);
            $docid = $fees->addChild('document-id');
            $docid->addChild('country', $country);
            $docid->addChild('doc-number', $number);
            $docid->addChild('date', Carbon::parse($renewal->event_date)->isoFormat('YMMDD'));
            $docid->addChild('kind', 'application');
            $fees->addChild('file-reference-id', $renewal->uid);
            $fees->addChild('owner', $procedure == 'FR' ? $renewal->uid : $renewal->applicant_name);
            $fee = $fees->addChild('fee');
            $fee->addChild('type-of-fee', $fee_code);
            $fee->addChild('fee-sub-amount', $renewal->cost);
            $fee->addChild('fee-factor', '1');
            $fee->addChild('fee-total-amount', $renewal->cost);
            // $fee->addChild('fee-date-due', Carbon::parse($renewal->due_date)->isoFormat('YMMDD'));
            /* Produced XML:
            <fees procedure="$procedure">
                <document-id>
                    <country>$country</country>
                    <doc-number>$number</doc-number>
                    <date>' . $fmt->format(strtotime($renewal->event_date)) . '</date>
                    <kind>application</kind>
                </document-id>
                <file-reference-id>$renewal->uid</file-reference-id>
                <owner>$renewal->applicant_name</owner>
                <fee>
                    <type-of-fee>$fee_code</type-of-fee>
                    <fee-sub-amount>$renewal->cost</fee-sub-amount>
                    <fee-factor>1</fee-factor>
                    <fee-total-amount>$renewal->cost</fee-total-amount>
                </fee>
            </fees>'
            */
        }

        //$header = config('renewal.xml.header');
        if ($procedure == 'EP') {
            //$header = str_replace('DEPOSIT', config('renewal.xml.EP_deposit'), $header);
            $xml->header->{'mode-of-payment'}->{'deposit-account'}->{'account-no'} = config('renewal.xml.EP_deposit');
        }
        if ($procedure == 'FR') {
            //$header = str_replace('DEPOSIT', config('renewal.xml.FR_deposit'), $header);
            $xml->header->{'mode-of-payment'}->{'deposit-account'}->{'account-no'} = config('renewal.xml.FR_deposit');
        }
        //$footer = str_replace('TOTAL', $total, config('renewal.xml.footer'));
        $xml->trailer->{'batch-pay-total-amount'} = $total;
        //$footer = str_replace('COUNT', count($tids), $footer);
        $xml->trailer->{'total-records'} = count($tids);
        //$xml .= $footer;
        // This indents the produced xml
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $fd = fopen('php://memory', 'w');
        fwrite($fd, $dom->saveXML());
        rewind($fd);
        if ($clear) {
            $updated = 0;
            foreach ($renewals as $renewal) {
                $log_line = [
                    'task_id' => $renewal->id,
                    'job_id' => $newjob,
                    'from_step' => $renewal->step,
                    'to_step' => 6,
                    'from_done' => $renewal->done,
                    'to_done' => 1,
                    'creator' => Auth::user()->login,
                    'created_at' => now(),
                ];
                $data_log[] = $log_line;
                $task = Task::find($renewal->id);
                $task->done_date = $done_date;
                $task->step = 6;
                $returncode = $task->save();
                if ($returncode) {
                    $updated++;
                }
            }
            RenewalsLog::insert($data_log);
        }
        $filename = Now()->isoFormat('YMMDDHHmmss') . '_payment_order.xml';

        return response()->stream(
            function () use ($fd) {
                fpassthru($fd);
            },
            200,
            ['Content-Type' => 'application/xml', 'Content-Disposition' => 'attachment; filename=' . $filename]
        );
    }

    public function update(Request $request, Task $renewal)
    {
        $this->validate($request, [
            'cost' => 'nullable|numeric',
            'fee' => 'nullable|numeric',
        ]);

        $renewal->update($request->except(['_token', '_method']));

        return response()->json(['success' => 'Renewal updated']);
    }

    public function logs(Request $request)
    {
        // Get list of logs
        $logs = new RenewalsLog();
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value != '') {
                    switch ($key) {
                        case 'Matter':
                            $logs = $logs->whereHas('task', function ($query) use ($value) {
                                $query->whereHas('matter', function ($q2) use ($value) {
                                    $q2->where('uid', 'LIKE', "$value%");
                                });
                            });
                            break;
                        case 'Client':
                            $logs = $logs->whereHas('task', function ($query) use ($value) {
                                $query->whereHas('matter', function ($q2) use ($value) {
                                    $q2->whereHas('client', function ($q3) use ($value) {
                                        $q3->where('display_name', 'LIKE', "$value%");
                                    });
                                });
                            });
                            break;
                        case 'Job':
                            $logs = $logs->where('job_id', "$value");
                            break;
                        case 'User':
                            $logs = $logs->whereHas('creatorInfo', function ($query) use ($value) {
                                $query->where('name', 'LIKE', "$value%");
                            });
                            break;
                        case 'Fromdate':
                            $logs = $logs->where('created_at', '>=', "$value");
                            break;
                        case 'Untildate':
                            $logs = $logs->where('created_at', '<=', "$value");
                            break;
                    }
                }
            }
        }
        $logs = $logs->orderby('job_id')->simplePaginate(config('renewal.general.paginate', 25));

        return view('renewals.logs', compact('logs'));
    }
}
