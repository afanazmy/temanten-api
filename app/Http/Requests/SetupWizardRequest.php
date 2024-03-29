<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use App\Models\SetupWizard;
use Illuminate\Support\Facades\DB;

class SetupWizardRequest extends FormRequest
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
            'superadmin' => ['required', function ($attribute, $value, $fail) {
                $status = DB::table('setup_wizards')->where('type', SetupWizard::T_SUPERADMIN)->first()->status ?? SetupWizard::S_NOTYET;
                if ($status === SetupWizard::S_DONE) $fail('Superadmin has been created.');
            }],
            'superadmin.username' => ['required', function ($attribute, $value, $fail) {
                $exist = DB::table('users')->where('username', $value)->first();
                if ($exist) $fail('Username has been used');
            }],
            'superadmin.password' => ['required', 'confirmed'],
            'superadmin.language' => ['nullable', function ($attribute, $value, $fail) {
                $availableLanguages = ['en-US', 'id-ID'];
                if (!in_array($value, $availableLanguages)) $fail('Language not found');
            }],
            'app.bride' => ['required'],
            'app.groom' => ['required'],
            'app.bride_nickname' => ['required'],
            'app.groom_nickname' => ['required'],
            'app.bride_father' => ['required'],
            'app.bride_mother' => ['required'],
            'app.groom_father' => ['required'],
            'app.groom_mother' => ['required'],
            'app.akad_datetime' => ['required'],
            'app.akad_place' => ['required'],
            'app.akad_map' => ['required'],
            'app.reception_datetime' => ['required'],
            'app.reception_place' => ['required'],
            'app.reception_map' => ['required'],
            'app.dresscode' => ['nullable'],
            'app.invitation_wording' => ['nullable'],
        ];
    }
}
