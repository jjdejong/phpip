<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MergeFileRequest extends FormRequest
{
    /**
     * The upload is merged against the route's matter, so the caller must be
     * allowed to see it. Checked here rather than only in the controller so it
     * runs before validation - an unauthorized caller gets 403, not a hint
     * about which file types are accepted.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('view', $this->route('matter')) ?? false;
    }

    public function rules()
    {
        return [
            'file' => 'required|file|mimes:docx,dotx',
        ];
    }
}