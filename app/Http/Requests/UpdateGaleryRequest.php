<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use App\Models\Galery;

class UpdateGaleryRequest extends FormRequest
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
            'type' => ['required', function ($attribute, $value, $fail) {
                $available = [Galery::COVER, Galery::GALERY];
                if (!in_array($value, $available)) $fail('Type not found');
            }],
            'extension' => ['required_with:file'],
        ];
    }
}
