<?php

namespace App\Models;

use App\Models\Model;
use App\Models\User;

class Token extends Model
{
    protected $fields = [
        '_id'          => 'string',
        'user'         => User::class,
        'token'        => 'string',
        'expires_date' => 'int',
    ];
}
