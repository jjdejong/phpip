<?php

namespace App\Services;

use App\Models\Matter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentMergeService
{
    public function merge(Matter $matter, $filePath)
    {
        $data = $this->collectData($matter);
        $template = new TemplateProcessor($filePath);
        Settings::setOutputEscapingEnabled(true);
        $template->setValues($data['simple']);
        $this->setComplexValues($template, $data['complex']);
        Settings::setOutputEscapingEnabled(false);
        $template->setValue('nl', '<w:br/>');
        return $template;
    }

    private function collectData(Matter $matter)
    {
        $selects = collect([
            'id' => $matter->id,
            'File_Ref' => $matter->uid,
            'Alt_Ref' => $matter->alt_ref,
            'Country' => $matter->country,
            'File_Category' => $matter->category_code,
            'Filing_Date' => $matter->events->where('code', 'FIL')
                ->first()
                ?->event_date->isoFormat('L'),
            'Filing_Number' => $matter->events->where('code', 'FIL')
                ->first()
                ?->detail,
            'Pub_Date' => $matter->events->where('code', 'PUB')
                ->first()
                ?->event_date->isoFormat('L'),
            'Pub_Number' => $matter->events->where('code', 'PUB')
                ->first()
                ?->detail,
            'Priority' => $matter->prioritiesFromView
                ->map(fn($priority) => $priority->country . $priority->detail . ' - ' . $priority->event_date->isoFormat('L'))->implode("\n"),
            'Grant_Date' => $matter->events->where('code', 'GRT')
                ->first()
                ?->event_date->isoFormat('L'),
            'Grant_Number' => $matter->events->where('code', 'GRT')
                ->first()
                ?->detail,
            'Registration_Date' => $matter->events->where('code', 'REG')
                ->first()
                ?->event_date->isoFormat('L'),
            'Registration_Number' => $matter->events->where('code', 'REG')
                ->first()
                ?->detail,
            'Pub_Reg_Date' => $matter->events->where('code', 'PR')
                ->first()
                ?->event_date->isoFormat('L'),
            'Pub_Reg_Number' => $matter->events->where('code', 'PR')
                ->first()
                ?->detail,
            'Allowance_Date' => $matter->events->where('code', 'ALL')
                ->first()
                ?->event_date->isoFormat('L'),
            'Expiration_Date' => $matter->expire_date,
            'Client' => $matter->client->name,
            'Client_Address' => $matter->client->actor->address ?? $matter->sharedClient->actor->address,
            'Client_Country' => $matter->client->actor->country ?? $matter->sharedClient->actor->country,
            'Contact' => $matter->contact
                ->first()
                ?->name,
            'Billing_Address' => $matter->getBillingAddress(),
            'Client_Ref' => $matter->client->actor_ref,
            'Email' => $matter->client->email,
            'VAT' => $matter->client->VAT_number,
            'Official_Title' => $matter->titles->where('type_code', 'TITOF')
                    ->first()
                    ?->value ??
                $matter->titles->where('type_code', 'TIT')
                    ->first()
                    ?->value,
            'English_Title' => $matter->titles->where('type_code', 'TITEN')
                    ->first()
                    ?->value ??
                $matter->titles->where('type_code', 'TITOF')
                    ->first()
                    ?->value,
            'Title' => $matter->titles->where('type_code', 'TIT')
                ->first()
                ?->value, // Changer TATA par le code de la classification
            'Trademark' => $matter->titles->where('type_code', 'TM')
                ->first()
                ?->value,
            'Classes' => $matter->titles->where('type_code', 'TMCL')
                ->map(fn($class) => $class->value)
                ->implode('.'),
            'Inventors' => $matter->inventors
                ->map(fn($inventor) => $inventor->first_name ? ($inventor->name . ' ' . $inventor->first_name) : $inventor->name)
                ->implode(' - '),
            'Inventor_Addresses' => $matter->inventors
                ->map(function ($inventor) {
                    return collect([
                        $inventor->first_name ? ($inventor->name . ' ' . $inventor->first_name) : $inventor->name,
                        $inventor->actor->address,
                        $inventor->actor->country,
                        $inventor->actor->nationality
                    ])->filter()->implode("\n");
                })->implode("\n\n"),
            'Owner' => $matter->getOwnerName(),
            'Agent' => $matter->agents->first()->name ?
                collect([
                    $matter->agents->first()->name,
                    $matter->agents->first()->address,
                    $matter->agents->first()->country
                ])->filter()->implode("\n") : "",
            'Agent_Ref' => $matter->agents
                ->first()
                ?->actor_ref,
            'Responsible' => $matter->responsibles
                ->first()
                ->name,
            'Writer' => $matter->writers
                ->first()
                ?->name,
            'Annuity_Agent' => $matter->annuityAgents
                ->first()
                ?->name,
        ])->merge($this->getTaskRules($matter));

        $complex = ['Priority', 'Client_Address', 'Billing_Address', 'Inventor_Addresses', 'Owner', 'Agent'];
        return [
            'simple' => $selects->except($complex)->toArray(),
            'complex' => $selects->only($complex)
        ];
    }

    private function getTaskRules(Matter $matter): \Illuminate\Support\Collection
    {
        return $matter->tasks->whereNotNull('rule_used')->mapWithKeys(function ($task) {
            $name = $task->rule?->detail ? ($task->rule->taskInfo->name . ' ' . Str::of($task->rule->detail)->replaceMatches('/[^\w\s]/', '')) : $task->rule->taskInfo->name;
            $name = Str::replace(' ', '_', Str::title($name));

            if(!$name) {
                return [];
            }

            return [$name . '_Due_Date' => $task->due_date->isoFormat('L')];
        })->filter();
    }

    private function setComplexValues(TemplateProcessor $template, $complexData)
    {
        foreach ($complexData as $key => $item) {
            $item = str_replace("\n", '${nl}', $item);
            $template->setValue($key, $item);
        }
    }
}