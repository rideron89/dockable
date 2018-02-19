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
        $auth_token = $request->auth_token;

        if ($auth_token->user) {
            $user = MongoClient::findOneAs('users', User::class, ['_id' => $auth_token->user->_id]);
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
