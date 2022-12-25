<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateUserRequest extends FormRequest
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
        $username = $this->username ?? null;
        $find = DB::table('users')->where('id', $id)->first();
        $exist = DB::table('users')->where('username', $username)->first();

        return [
            'username' => ['required', function ($attribute, $value, $fail) use ($id, $find, $exist) {
                if (!$find) $fail('User not found');
                else if ($exist && $exist->id !== $id) $fail('Username already use.');
            }],
            'password' => ['nullable', 'confirmed'],
        ];
    }
}
