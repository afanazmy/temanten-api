<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use App\Models\Galery;

class StoreGaleryRequest extends FormRequest
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
            'galeries' => ['required', function ($attribute, $value, $fail) {
                if (count($value) < 1) $fail('Galeries required');
            }],
            'galeries.*.type' => ['required', function ($attribute, $value, $fail) {
                $available = [Galery::COVER, Galery::GALERY];
                if (!in_array($value, $available)) $fail('Type not found');
            }],
            'galeries.*.file' => ['required'],
            'galeries.*.extension' => ['required'],
        ];
    }
}
