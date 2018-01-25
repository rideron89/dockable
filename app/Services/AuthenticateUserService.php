<?php

namespace App\Services;

use App\Databases\MongoClient;

class AuthenticateUserService
{
    /**
    * Try to authenticate the user by querying the database and checking the
    * username/password combo.
    *
    * @param string $username
    * @param string $password
    *
    * @return bool
    */
    public static function authenticate($username, $password)
    {
        if (!$username || !$password)
        {
            return false;
        }

        $client = new MongoClient('dockable', 'users');
        $results = $client->find(['username' => $username]);

        if (password_verify($password, $results->documents[0]['password']) === false)
        {
            return false;
        }

        return true;
    }
}
