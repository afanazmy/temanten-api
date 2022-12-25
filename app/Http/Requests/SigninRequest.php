<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SigninRequest extends FormRequest
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
        $user = DB::table('users')->where('username', $this->username)->first();

        return [
            'username' => ['required', function ($attribute, $value, $fail) use ($user) {
                $isValid = Hash::check($this->password, $user->password ?? null);
                if (!$user || !$isValid) $fail('Username or password invalid');
            }],
            'password' => ['required', function ($attribute, $value, $fail) use ($user) {
                $isValid = Hash::check($value, $user->password ?? null);
                if (!$user || !$isValid) $fail('Username or password invalid');
            }]
        ];
    }
}
