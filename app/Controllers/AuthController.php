<?php

namespace App\Controllers;


use App\Databases\MongoClient;
use App\Services\CookieManagerService;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AuthController
{
    public function login(Request $request)
    {
        // retrieve and sanitize credentials from the request
        $username = trim($request->request->get('username'));
        $password = trim($request->request->get('password'));

        // get the user
        $client = new MongoClient('users');
        $user = $client->find(['username' => $username]);

        // test the user credentials
        if (!password_verify($password, $user->data[0]['password'])) {
            return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        // set up the token object
        $document = [
            'user'    => [
                '_id'      => $user->data[0]['_id'],
                'username' => $user->data[0]['username'],
            ],
            'token'   => bin2hex(random_bytes(16)),
            'expires_date' => time() + 2419200 // 4 weeks
        ];

        // add a new entry to the token database
        $client = new MongoClient('auth_tokens');
        $result = $client->create($document);

        // add the token id to the user's document
        $client = new MongoClient('users');
        $client->updateImproved([
            '_id' => $user->data[0]['_id']
        ], [
            '$push' => ['tokens' => $document['token']]
        ]);

        // save the token data in a cookie for future requests
        $auth = base64_encode(json_encode($result->data));
        CookieManagerService::add('auth', $auth);

        return new Response($auth);
    }

    public function logout(Request $request)
    {
        $auth = CookieManagerService::get('auth');
        $auth = base64_decode(trim($auth));
        $auth = json_decode($auth, true);

        if ($auth) {
            // remove the token
            $client = new MongoClient('auth_tokens');
            $client->delete(['_id' => new ObjectId($auth['id'])]);
        }

        CookieManagerService::remove('auth');

        return new Response('Ok');
    }

    public function register(Request $request)
    {
        $username = trim($request->request->get('username'));
        $password = trim($request->request->get('password'));

        if (!$username || !$password) {
            return new Response('invalid user data', 400);
        }

        $db = new MongoClient('users');

        if ($db->exists(['username' => $username])) {
            return new Response('account already exists', 409);
        }

        $result = $db->create(['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)]);

        if ($result->err) {
            return new Response($result->err, 500);
        }

        return new Response('Ok');
    }
}
