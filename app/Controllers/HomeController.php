<?php

namespace App\Controllers;

use App\Viewer;
use App\Databases\MongoClient;
use App\Models\Token;
use App\Models\User;
use App\Services\CookieManagerService;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index(Request $request)
    {
        $auth = CookieManagerService::get('auth');
        $auth = base64_decode(trim($auth));
        $auth = json_decode($auth, true);
        $auth = new Token($auth);

        // TODO: need to find a safeguard for unauthorized cookies

        if ($auth->user) {
            $user = MongoClient::findOneAs('users', User::class, ['_id' => new ObjectId($auth->user['_id']['$oid'])]);
        } else {
            $user = new User();
        }

        $tokens = MongoClient::findAs('auth_tokens', Token::class);
        $users  = MongoClient::findAs('users', User::class, [], ['projection' => ['password' => 0]]);

        $html = Viewer::renderTwig('index.twig', ['user' => $user, 'tokens' => $tokens, 'users' => $users]);

        return new Response($html);
    }

    public function register(Request $request)
    {
        return new Response(Viewer::renderTwig('register.twig'));
    }
}
