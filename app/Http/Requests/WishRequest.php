<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Support\Facades\DB;

class WishRequest extends FormRequest
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
            'invitation_id' => ['required', function ($attribute, $value, $fail) {
                $exist = DB::table('invitations')->where('id', $value)->first();
                if (!$exist) $fail('Invitation not found');
            }]
        ];
    }
}
