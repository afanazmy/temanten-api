<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use App\Imports\InvitationImport;

class ImportInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'file' => ['required'],
            'type' => ['nullable', function ($attribute, $value, $fail) {
                $available = [InvitationImport::ADD, InvitationImport::REPLACE];
                if (!in_array($value, $available)) $fail('Invalid import type');
            }]
        ];
    }
}
