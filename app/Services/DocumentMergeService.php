<?php

namespace App\Services;

use App\Models\Matter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentMergeService
{
    private Matter $matter;

    public function setMatter(Matter $matter)
    {
        $this->matter = $matter;

        return $this;
    }

    public function merge($filePath)
    {
        $data = $this->collectData();
        $template = new TemplateProcessor($filePath);
        Settings::setOutputEscapingEnabled(true);
        $template->setValues($data['simple']);
        $this->setComplexValues($template, $data['complex']);
        Settings::setOutputEscapingEnabled(false);
        $template->setValue('nl', '<w:br/>');
        return $template;
    }

    private function collectData()
    {
        $selects = collect([
            'id' => $this->matter->id,
            'File_Ref' => $this->matter->uid,
            'Alt_Ref' => $this->matter->alt_ref,
            'Country' => $this->matter->country,
            'File_Category' => $this->matter->category_code,
            'Filing_Date' => $this->matter->events->where('code', 'FIL')
                ->first()
                ?->event_date->isoFormat('L'),
            'Filing_Number' => $this->matter->events->where('code', 'FIL')
                ->first()
                ?->detail,
            'Pub_Date' => $this->matter->events->where('code', 'PUB')
                ->first()
                ?->event_date->isoFormat('L'),
            'Pub_Number' => $this->matter->events->where('code', 'PUB')
                ->first()
                ?->detail,
            'Priority' => $this->matter->prioritiesFromView
                ->map(fn($priority) => $priority->country . $priority->detail . ' - ' . $priority->event_date->isoFormat('L'))->implode("\n"),
            'Grant_Date' => $this->matter->events->where('code', 'GRT')
                ->first()
                ?->event_date->isoFormat('L'),
            'Grant_Number' => $this->matter->events->where('code', 'GRT')
                ->first()
                ?->detail,
            'Registration_Date' => $this->matter->events->where('code', 'REG')
                ->first()
                ?->event_date->isoFormat('L'),
            'Registration_Number' => $this->matter->events->where('code', 'REG')
                ->first()
                ?->detail,
            'Pub_Reg_Date' => $this->matter->events->where('code', 'PR')
                ->first()
                ?->event_date->isoFormat('L'),
            'Pub_Reg_Number' => $this->matter->events->where('code', 'PR')
                ->first()
                ?->detail,
            'Allowance_Date' => $this->matter->events->where('code', 'ALL')
                ->first()
                ?->event_date->isoFormat('L'),
            'Expiration_Date' => $this->matter->expire_date,
            'Contact' => $this->matter->contact
                ->first()
                ?->name,
            'Billing_Address' => $this->matter->getBillingAddress(),
            'Client_Ref' => $this->matter->client->actor_ref,
            'Email' => $this->matter->client->email,
            'VAT' => $this->matter->client->VAT_number,
            'Official_Title' => $this->matter->titles->where('type_code', 'TITOF')
                    ->first()
                    ?->value ??
                $this->matter->titles->where('type_code', 'TIT')
                    ->first()
                    ?->value,
            'English_Title' => $this->matter->titles->where('type_code', 'TITEN')
                    ->first()
                    ?->value ??
                $this->matter->titles->where('type_code', 'TITOF')
                    ->first()
                    ?->value,
            'Title' => $this->matter->titles->where('type_code', 'TIT')
                ->first()
                ?->value, // Changer TATA par le code de la classification
            'Trademark' => $this->matter->titles->where('type_code', 'TM')
                ->first()
                ?->value,
            'Classes' => $this->matter->titles->where('type_code', 'TMCL')
                ->map(fn($class) => $class->value)
                ->implode('.'),
            'Inventors' => $this->matter->inventors
                ->map(fn($inventor) => $inventor->first_name ? ($inventor->name . ' ' . $inventor->first_name) : $inventor->name)
                ->implode(' - '),
            'Inventor_Addresses' => $this->matter->inventors
                ->map(function ($inventor) {
                    return collect([
                        $inventor->first_name ? ($inventor->name . ' ' . $inventor->first_name) : $inventor->name,
                        $inventor->actor->address,
                        $inventor->actor->country,
                        $inventor->actor->nationality
                    ])->filter()->implode("\n");
                })->implode("\n\n"),
            'Owner' => $this->matter->getOwnerName(),
            'Responsible' => $this->matter->responsibleActor
                ?->name,
            'Writer' => $this->matter->writer()
                ?->name,
        ])->merge([
            ...$this->getTaskRules(),
            ...$this->getActorsFields(),
        ]);

        dd($selects);

        $complex = ['Priority', 'Client_Address', 'Billing_Address', 'Inventor_Addresses', 'Owner', 'Agent'];
        return [
            'simple' => $selects->except($complex)->toArray(),
            'complex' => $selects->only($complex)
        ];
    }

    private function getTaskRules(): \Illuminate\Support\Collection
    {
        return $this->matter->tasks->whereNotNull('rule_used')->mapWithKeys(function ($task) {
            $name = $task->rule?->detail ? ($task->rule->taskInfo->name . ' ' . Str::of($task->rule->detail)->replaceMatches('/[^\w\s]/', '')) : $task->rule->taskInfo->name;
            $name = Str::replace(' ', '_', Str::title($name));

            if (!$name) {
                return [];
            }

            return [$name . '_Due_Date' => $task->due_date->isoFormat('L')];
        })->filter();
    }

    private function getAgentFields(): \Illuminate\Support\Collection
    {
        $agent = $this->matter->agent();

        if (!$agent) {
            return collect();
        }

        return collect([
            'Agent' => $agent->name ?
                collect([
                    $agent->name,
                    $agent->address,
                    $agent->country
                ])->filter()->implode("\n") : "",
            'Agent_Ref' => $agent->actor_ref,
        ]);
    }

    private function getActorDetails(mixed $actor, string $prefix): \Illuminate\Support\Collection
    {
        if (!$actor) {
            return collect();
        }

        return collect([
            "{$prefix}" => $actor->name,
            "{$prefix}_Ref" => $actor->pivot ? $actor->pivot->actor_ref : $actor->actor_ref,
            "{$prefix}_Address" => $actor->address,
            "{$prefix}_Country" => $actor->country,
            "{$prefix}_Registration_No" => $actor->registration_no,
            "{$prefix}_VAT_No" => $actor->VAT_number,
        ]);
    }

    private function getPrimaryAgentFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->agent(), 'Primary_Agent');
    }

    private function getSecondaryAgentFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->secondaryAgent(), 'Secondary_agent');
    }

    private function getAnnuityAgentFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->annuityAgent(), 'Annuity_Agent');
    }

    private function getClientFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->clientFromLnk(), 'Client');
    }

    private function getPayorFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->payor(), 'Payor');
    }

    private function getActorsFields(): \Illuminate\Support\Collection
    {
        return collect([
            ...$this->getAgentFields(),
            ...$this->getPrimaryAgentFields(),
            ...$this->getSecondaryAgentFields(),
            ...$this->getAnnuityAgentFields(),
            ...$this->getClientFields(),
            ...$this->getPayorFields(),
        ]);
    }

    private function setComplexValues(TemplateProcessor $template, $complexData)
    {
        foreach ($complexData as $key => $item) {
            $item = str_replace("\n", '${nl}', $item);
            $template->setValue($key, $item);
        }
    }
}