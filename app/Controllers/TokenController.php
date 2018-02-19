<?php

namespace App\Controllers;

use App\Databases\MongoClient;
use App\Services\CookieManagerService;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenController
{
    public function delete(Request $request)
    {
        $auth = CookieManagerService::get('auth');
        $auth = base64_decode(trim($auth));
        $auth = json_decode($auth, true);

        $token_id = $request->params['token_id'];

        if (!$token_id) {
            return new Response('Bad Request', Response::HTTP_BAD_REQUEST);
        }

        $client = new MongoClient('dockable', 'auth_tokens');
        $token = $client->findOne(['_id' => new ObjectId($token_id)])->data;

        // delete the token
        $client->delete(['_id' => new ObjectId($token_id)]);

        // delete the token from the user's document
        $client = new MongoClient('dockable', 'users');

        $user = $client->findOne(['_id' => $token['user']->_id])->data;

        $new_tokens = array_filter($user['tokens'], function ($user_token) use ($token) {
            return $user_token != $token['token'];
        });

        if ($user_tokens == []) {
            $client->updateImproved(['_id' => $user['_id']], ['$unset' => ['tokens' => '']]);
        } else {
            $client->update(['_id' => $user['_id']], ['tokens' => $user_tokens]);
        }

        if ($client->err) {
            return new Response($client->err, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // remove the cookie if the token it refers to was removed
        if ($auth['id'] === $token_id) {
            CookieManagerService::remove('auth');
        }

        return new Response('Deleted', Response::HTTP_OK);
    }
}
