<?php

namespace App\Services;

use App\Models\Matter;
use App\Models\MatterActors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * Service for merging matter data into Word document templates.
 *
 * This service handles the collection and merging of intellectual property matter data
 * (patents, trademarks, etc.) into Microsoft Word document templates using PHPWord.
 */
class DocumentMergeService
{
    private Matter $matter;

    /**
     * Set the matter to be used for document merge operations.
     *
     * @param Matter $matter The matter model instance to merge.
     * @return $this Returns itself for method chaining.
     */
    public function setMatter(Matter $matter)
    {
        $this->matter = $matter;

        return $this;
    }

    /**
     * Merge matter data into a Word document template.
     *
     * Processes a Word document template by replacing placeholders with matter data.
     * Handles both simple string replacements and complex multi-line values.
     *
     * @param string $filePath The path to the Word document template file.
     * @return TemplateProcessor The processed template ready for saving or download.
     */
    public function merge($filePath)
    {
        $data = $this->collectData();
        $template = new TemplateProcessor($filePath);
        Settings::setOutputEscapingEnabled(true);
        $template->setValues($data['simple']);
        $this->setComplexValues($template, $data['complex']);
        Settings::setOutputEscapingEnabled(false);
        $template->setValue('nl', "<w:br/>");
        return $template;
    }

    /**
     * Collect all matter data for document merge.
     *
     * Gathers comprehensive matter information including dates, numbers, titles,
     * actors (clients, agents, inventors), and task rules. Data is organized into
     * simple (single-line) and complex (multi-line) values for template processing.
     *
     * @return array An associative array with 'simple' and 'complex' keys containing merge data.
     */
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

        // dd($selects);

        $complex = [
            'Priority',
            'Primary_Agent_Address',
            'Secondary_Agent_Address',
            'Annuity_Agent_Address',
            'Client_Address',
            'Payor_Address',
            'Billing_Address',
            'Inventor_Addresses',
            'Owner',
            'Agent'
        ];
        return [
            'simple' => $selects->except($complex)->toArray(),
            'complex' => $selects->only($complex)
        ];
    }

    /**
     * Extract task due dates from matter rules.
     *
     * Retrieves due dates for tasks that have associated rules, formatting them
     * as merge fields with rule names and details.
     *
     * @return \Illuminate\Support\Collection Collection of task rule due dates keyed by rule name.
     */
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

    /**
     * Retrieve agent fields for the matter.
     *
     * Collects the primary agent's name, reference, address, and country.
     * This is a legacy method maintained for backward compatibility.
     *
     * @return \Illuminate\Support\Collection Collection of agent fields.
     */
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
                    $agent->actor?->address,
                    $agent->actor?->country
                ])->filter()->implode("\n") : "",
            'Agent_Ref' => $agent->actor_ref,
        ]);
    }

    /**
     * Extract detailed information for a matter actor.
     *
     * Collects name, reference, address, country, registration number, and VAT number
     * for any matter actor, prefixing field names with the provided prefix.
     *
     * @param MatterActors|null $matterActor The matter actor to extract details from.
     * @param string $prefix The prefix to use for field names (e.g., 'Client', 'Agent').
     * @return \Illuminate\Support\Collection Collection of actor details with prefixed field names.
     */
    private function getActorDetails(?MatterActors $matterActor, string $prefix): \Illuminate\Support\Collection
    {
        if(!$matterActor) {
            return collect();
        }

        return collect([
            "{$prefix}" => $matterActor->name,
            "{$prefix}_Ref" => $matterActor->actor_ref,
            "{$prefix}_Address" => $matterActor->actor?->address,
            "{$prefix}_Country" => $matterActor->actor?->country,
            "{$prefix}_Registration_No" => $matterActor->actor?->registration_no,
            "{$prefix}_VAT_No" => $matterActor->actor?->VAT_number,
        ]);
    }

    /**
     * Retrieve primary agent fields for the matter.
     *
     * @return \Illuminate\Support\Collection Collection of primary agent details.
     */
    private function getPrimaryAgentFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->agent(), 'Primary_Agent');
    }

    /**
     * Retrieve secondary agent fields for the matter.
     *
     * @return \Illuminate\Support\Collection Collection of secondary agent details.
     */
    private function getSecondaryAgentFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->secondaryAgent(), 'Secondary_Agent');
    }

    /**
     * Retrieve annuity agent fields for the matter.
     *
     * @return \Illuminate\Support\Collection Collection of annuity agent details.
     */
    private function getAnnuityAgentFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->annuityAgent(), 'Annuity_Agent');
    }

    /**
     * Retrieve client fields for the matter.
     *
     * @return \Illuminate\Support\Collection Collection of client details.
     */
    private function getClientFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->clientFromLnk(), 'Client');
    }

    /**
     * Retrieve payor fields for the matter.
     *
     * @return \Illuminate\Support\Collection Collection of payor details.
     */
    private function getPayorFields(): \Illuminate\Support\Collection
    {
        return $this->getActorDetails($this->matter->payor(), 'Payor');
    }

    /**
     * Retrieve all actor fields for the matter.
     *
     * Aggregates fields from all actor types including agents, client, and payor.
     *
     * @return \Illuminate\Support\Collection Combined collection of all actor fields.
     */
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

    /**
     * Set complex multi-line values in the document template.
     *
     * Processes multi-line values by converting newlines to Word XML line breaks,
     * allowing proper formatting of addresses and other multi-line fields.
     *
     * @param TemplateProcessor $template The template processor instance.
     * @param array $complexData Array of complex field values containing newlines.
     * @return void
     */
    private function setComplexValues(TemplateProcessor $template, $complexData)
    {
        foreach ($complexData as $key => $item) {
            $item = str_replace("\n", '${nl}', $item);
            $template->setValue($key, $item);
        }
    }
}