<?php

namespace App\Models;

use App\Models\Model;

class Token extends Model
{
    protected $fields = [
        '_id',
        'user',
        'token',
        'expires_date',
    ];
}
