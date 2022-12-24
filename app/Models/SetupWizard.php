<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupWizard extends Model
{
    /** Setup Wizard Type */
    const T_SUPERADMIN = 'superadmin';

    /** Setup Wizard Status */
    const S_DONE = 'done';
    const S_NOTYET = 'not-yet';
    const S_SKIPPED = 'skipped';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'step', 'name', 'type', 'status'
    ];
}
