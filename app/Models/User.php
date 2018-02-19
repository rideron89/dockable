<?php

namespace App\Models;

class User extends Model
{
    protected $fields = [
        '_id'      => 'string',
        'email'    => 'string',
        'username' => 'string',
        'password' => 'string',
        'tokens'   => 'string',
    ];
}
