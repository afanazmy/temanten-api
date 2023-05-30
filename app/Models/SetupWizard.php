<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupWizard extends Model
{
    /** Setup Wizard Type */
    const T_SUPERADMIN = 'superadmin';
    const T_APP = 'app';

    /** Setup Wizard Status */
    const S_DONE = 'done';
    const S_NOTYET = 'not-yet';
    const S_SKIPPED = 'skipped';

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'step', 'name', 'type', 'status'
    ];
}
