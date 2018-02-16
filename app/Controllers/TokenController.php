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
        $client->delete(['_id' => new ObjectId($token_id)]);

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
