<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatterExportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'sortkey' => 'caseref',
            'sortdir' => 'asc',
        ]);
    }

    public function rules()
    {
        return [
            'Ref' => 'nullable|string',
            'Cat' => 'nullable|string',
            'country' => 'nullable|string',
            'Status' => 'nullable|string',
            'Status_date' => 'nullable|string',
            'Client' => 'nullable|string',
            'ClRef' => 'nullable|string',
            'Applicant' => 'nullable|string',
            'Agent' => 'nullable|string',
            'AgtRef' => 'nullable|string',
            'Title' => 'nullable|string',
            'Inventor1' => 'nullable|string',
            'Filed' => 'nullable|string',
            'FilNo' => 'nullable|string',
            'Published' => 'nullable|string',
            'PubNo' => 'nullable|string',
            'Granted' => 'nullable|string',
            'GrtNo' => 'nullable|string',
            'responsible' => 'nullable|string',
            'Ctnr' => 'nullable|string',
        ];
    }
}