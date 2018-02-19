<?php

namespace App\Models;

class User extends Model
{
    protected $fields = [
        '_id',
        'email',
        'username',
        'password',
        'tokens',
    ];
}
