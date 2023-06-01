<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $keyType = 'string';

    public function wish()
    {
        return $this->hasOne(Wish::class);
    }

    public function phoneNumbers()
    {
        return $this->hasMany(InvitationPhoneNumber::class);
    }

    public function guestBook()
    {
        return $this->hasOne(GuestBook::class);
    }
}
