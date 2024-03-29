<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationPhoneNumber extends Model
{
    protected $keyType = 'string';

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }
}
