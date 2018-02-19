<?php

namespace App\Middleware;

use App\Databases\MongoClient;
use App\Models\Token;
use App\Models\User;
use App\Services\CookieManagerService;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpFoundation\Request;

/**
* Validate the active auth cookie, if there is one. If the cookie is invalid
* or it has expired, remove it from the database, and delete the cookie.
*/
class ValidateCookieToken implements Middleware
{
    public static function run(Request $request)
    {
        // read in and parse the auth cookie
        $cookie_token = CookieManagerService::get('auth');
        $cookie_token = base64_decode(trim($cookie_token));
        $cookie_token = json_decode($cookie_token, true);
        $cookie_token = new Token($cookie_token);

        $cookie_token->_id = new ObjectId($cookie_token->_id['$oid']);

        $client = new MongoClient('auth_tokens');
        $database_token = $client->findOne(Token::class, ['_id' => $cookie_token->_id]);

        // check if token does not exist or is expired
        if (!$database_token) {
            $request->auth_token = null;

            CookieManagerService::remove('auth');
        } else if ($database_token->expires_date <= time()) {
            $request->auth_token = null;

            CookieManagerService::remove('auth');

            $client = new MongoClient('auth_tokens');
            $client->remove(['_id' => $database_token->_id]);

            // TODO: need to also delete it from the user's token list
        } else {
            $request->auth_token = $database_token;
        }

        return $request;
    }
}
