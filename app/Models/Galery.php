<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galery extends Model
{
    protected $keyType = 'string';

    const COVER = 'cover';
    const GALERY = 'galery';
}
