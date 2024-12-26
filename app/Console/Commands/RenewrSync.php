<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Matter;
use App\Models\Actor;
use App\Models\Task;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class RenewrSync extends Command
{
    protected $signature = 'app:renewr-sync {--demo : Run in demo mode}';
    protected $description = 'Process renewal portfolio data from Renewr';

    private $stats = [
        'updated' => 0,
        'inserted' => 0,
        'unrecognized' => 0,
        'patsprocessed' => 0,
        'annsprocessed' => 0
    ];

    public function handle()
    {
        $this->info('Starting portfolio renewal processing...');

        try {
            // Get Renewr actor ID
            $renewrActor = Actor::where('name', 'LIKE', 'Renewr%')->first();
            if (!$renewrActor) {
                throw new \Exception('No Renewr in actor table');
            }

            // Load portfolio data
            if ($this->option('demo')) {
                $portfolio = $this->loadDemoData();
            } else {
                $portfolio = $this->fetchFromApi();
            }

            DB::beginTransaction();

            foreach ($portfolio as $renewrPatent) {
                if (empty($renewrPatent->renewalEvents)) {
                    continue;
                }
                $this->stats['patsprocessed']++;

                $matter = $this->findAndValidateMatter($renewrPatent, $renewrActor);
                if (!$matter) {
                    continue;
                }

                $this->processRenewals($matter, $renewrPatent);
            }

            DB::commit();
            $this->displayStats();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Portfolio renewal processing failed: ' . $e->getMessage());
            $this->error('Portfolio renewal processing failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function fetchFromApi()
    {
        $headers = [
            'Accept: application/json',
            'X-API-KEY: ' . config('renewr.api_key')
        ];

        $ch = curl_init(config('renewr.url'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('API request failed: ' . curl_error($ch));
        }

        curl_close($ch);

        $data = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON Decode Error: " . json_last_error_msg());
        }

        if (empty($data)) {
            throw new \Exception("No portfolio data received from API");
        }

        return $data;
    }

    /**
     * Load demo data for testing purposes.
     * If a local data file exists, load it; otherwise, fetch data from the demo API using a jwt token.
     * @return array
     */
    private function loadDemoData()
    {
        // Check if a local data file exists
        $jsonFile = resource_path('scripts/data.json');
        if (is_readable($jsonFile)) {
            // Load data from local file
            $data = json_decode(file_get_contents($jsonFile));
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON Decode Error: " . json_last_error_msg());
            }

            if (empty($data)) {
                throw new \Exception("No portfolio data received");
            }
        } else {
            // Fetch jwt token
            $url = config('renewr.jwt_url');
            $data = [
                'client_id' => 'renewr-front',
                'grant_type' => 'password',
                'username' => config('renewr.demo_username'),
                'password' => config('renewr.demo_password')
            ];

            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data),
                ],
            ];

            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === false) {
                throw new \Exception("Failed to get token");
            }

            $bearer_token = json_decode($result)->access_token;

            // Fetch data
            $headers = [
                'Accept: application/json',
                'Authorization: Bearer ' . $bearer_token
            ];

            $ch = curl_init(config('renewr.url'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new \Exception('API request failed: ' . curl_error($ch));
            }

            curl_close($ch);

            $data = json_decode($response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON Decode Error: " . json_last_error_msg());
            }

            if (empty($data)) {
                throw new \Exception("No portfolio data received from API");
            }
        }

        return $data;
    }

    private function findAndValidateMatter($renewrPatent, $renewrActor)
    {
        if (!$renewrPatent->providerId) {
            $this->warn("No providerId for patent: $renewrPatent->clientCaseRef");
            return null;
        }

        $matter = Matter::with(['actorPivot' => function ($query) use ($renewrActor) {
            $query->where('actor_id', $renewrActor->id);
        }])->find($renewrPatent->providerId);

        if (!$matter) {
            $this->warn("No data for providerId: $renewrPatent->providerId");
            $this->stats['unrecognized']++;
            return null;
        }

        if ($matter->uid != $renewrPatent->clientCaseRef) {
            $this->warn("Provider ref. $renewrPatent->clientCaseRef does not match our ref. $matter->uid for providerId $renewrPatent->providerId");
            $this->stats['unrecognized']++;
        }

        if ($matter->country != $renewrPatent->country) {
            $this->error("Provider country $renewrPatent->country does not match our country $matter->country for providerId $renewrPatent->providerId");
            $this->stats['unrecognized']++;
            return null;
        }

        return $matter;
    }

    private function processRenewals($matter, $renewrPatent)
    {
        foreach ($renewrPatent->renewalEvents as $renewal) {
            $this->stats['annsprocessed']++;

            $task = $matter->tasks()
                ->where('task.code', 'REN')
                ->whereRaw('CAST(task.detail AS UNSIGNED) = ?', [$renewal->renewalYearNumber])
                ->first();

            if ($task) {
                if (!(config('renewr.skip_done') && $task->done)) {
                    $this->updateRenewal($task, $renewal, $renewrPatent);
                }
            } else {
                $this->insertNewRenewal($matter, $renewal, $renewrPatent);
            }
        }
    }

    private function updateRenewal($task, $renewal, $renewrPatent)
    {
        $updates = [];
        $serviceProviderFee = config('renewr.fee_calculation.renewr_fee');

        if ($renewal->renewalDate != $task->due_date->format('Y-m-d')) {
            $updates['due_date'] = $renewal->renewalDate;
        }

        if ($renewal->fees->invoiceCurrency && $task->currency != $renewal->fees->invoiceCurrency) {
            $updates['currency'] = $renewal->fees->invoiceCurrency;
        }

        $cost = $renewal->fees->totalFees - $serviceProviderFee;
        if (round($cost, 2) != round($task->cost ?? 0, 2)) {
            $updates['cost'] = $cost;
            $updates['notes'] = $renewal->instruction ?? 'Renewr init.';
        }

        $fee = $this->calculateFee($cost, $serviceProviderFee);
        if ($fee != $task->fee) {
            $updates['fee'] = $fee;
        }

        if (
            !empty($renewal->paymentDate)
            && (!$task->done_date || $renewal->paymentDate != $task->done_date->format('Y-m-d'))
            && $renewal->paymentDate != $renewal->renewalDate
            && $renewal->paymentDate < now()->format('Y-m-d')
        ) {
            $updates['done_date'] = $renewal->paymentDate;
            $updates['step'] = -1;
            if (!$task->invoice_step) {
                $updates['invoice_step'] = 1;
            }
        }

        if (!empty($updates)) {
            $updates['updated_at'] = now();
            $updates['updater'] = 'Renewr';

            $task->update($updates);

            $this->info("Updated " . collect($updates)->except(['updated_at', 'updater'])->map(function ($value, $key) {
                return "$key=$value";
            })->implode(', ') . " for renewal $renewal->renewalYearNumber in $renewrPatent->providerId ($renewrPatent->clientCaseRef)");

            $this->stats['updated']++;
        }
    }

    private function insertNewRenewal($matter, $renewal, $renewrPatent)
    {
        // Find trigger event
        $triggerEvent = Country::where('renewal_base', 'FIL')
            ->where('iso', $renewrPatent->country)->exists()
            ? $matter->filing->id
            : $matter->grant->id;

        if (!$triggerEvent) {
            $this->warn("Could not find trigger event for renewal $renewal->renewalYearNumber in $renewrPatent->clientCaseRef");
            return;
        }

        $serviceProviderFee = config('renewr.fee_calculation.renewr_fee');
        $cost = $renewal->fees->totalFees - $serviceProviderFee;
        $fee = $this->calculateFee($cost, $serviceProviderFee);

        $task = Task::create([
            'code' => 'REN',
            'detail' => $renewal->renewalYearNumber,
            'due_date' => $renewal->renewalDate,
            'currency' => $renewal->fees->invoiceCurrency ?? 'EUR',
            'cost' => $cost,
            'fee' => $fee,
            'notes' => $renewal->instruction ?? 'Renewr init.',
            'creator' => 'Renewr',
            'matter_id' => $matter->id,
            'trigger_id' => $triggerEvent,
        ]);

        if (
            !empty($renewal->paymentDate)
            && $renewal->paymentDate != $renewal->renewalDate
            && $renewal->paymentDate < now()->format('Y-m-d')
        ) {
            $task->update([
                'done_date' => $renewal->paymentDate,
                'step' => -1,
                'invoice_step' => 1
            ]);
        }
        $this->info("Inserted renewal $renewal->renewalYearNumber for $renewrPatent->providerId ($renewrPatent->clientCaseRef)");
        $this->stats['inserted']++;
    }

    private function calculateFee($cost, $serviceProviderFee)
    {
        $config = config('renewr.fee_calculation');

        // $cost already has $serviceProviderFee deduced
        if ($cost > $config['threshold'] - $serviceProviderFee) {
            return round(
                $config['our_fee'] +
                $serviceProviderFee +
                    ($config['above_percentage'] * $cost),
                2
            );
        } elseif ($cost != 0) {
            return round(
                $config['our_fee'] +
                $serviceProviderFee +
                    ($config['below_percentage'] -
                    ($config['progressive_factor'] * $cost)
                    ) * $cost,
                2
            );
        }
        return 0;
    }

    private function displayStats()
    {
        $this->info("\nAnnuities updated: {$this->stats['updated']}, inserted: {$this->stats['inserted']}, among processed: {$this->stats['annsprocessed']}");
        $this->info("Patents not recognized: {$this->stats['unrecognized']}, total processed: {$this->stats['patsprocessed']}");
    }
}
