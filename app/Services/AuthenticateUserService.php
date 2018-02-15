<?php

namespace App\Services;

use App\Databases\MongoClient;

class AuthenticateUserService
{
    /**
    * Try to authenticate the user by querying the database and checking the
    * username/password combo. If successful, returns the User's ID.
    *
    * @param string $username
    * @param string $password
    *
    * @return array
    */
    public static function authenticate($username, $password)
    {
        if (!$username || !$password) {
            return [];
        }

        $client = new MongoClient('dockable', 'users');
        $results = $client->find(['username' => $username]);

        if ((password_verify($password, $results->data[0]['password']) === false) &&
            ($password === $results->data[0]['password']) === false) {
            return [];
        }

        return [
            'id'              => $results->data[0]['_id']->__toString(),
            'username'        => $results->data[0]['username'],
        ];
    }
}
