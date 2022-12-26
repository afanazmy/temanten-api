<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateInvitationRequest extends FormRequest
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
        $id = $this->route('id') ?? null;

        return [
            'recipient_name' => ['required', function ($attribute, $value, $fail) use ($id) {
                $find = DB::table('invitations')->where('id', $id)->first();
                $exist = DB::table('invitations')->where('recipient_name', $value)->first();

                if (!$find) $fail('Invitation not found');
                else if ($exist && $exist->id !== $id) $fail('Recipient already exist');
            }],
        ];
    }
}
