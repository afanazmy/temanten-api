<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $keyType = 'string';

    public function wishes()
    {
        return $this->hasOne(Wish::class);
    }
}
