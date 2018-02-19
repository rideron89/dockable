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

        // get all tokens
        $client = new MongoClient('auth_tokens');
        $tokens = $client->find(Token::class);

        // get all users
        $client = new MongoClient('users');
        $users = $client->find(User::class, [], ['projection' => ['password' => 0]]);

        // get the authorized user if there is one
        $user = (!$auth_token->user) ? [] : $client->findOne(User::class, ['_id' => $auth_token->user->_id]);

        $data = ['user' => $user, 'tokens' => $tokens, 'users' => $users];
        $html = Viewer::renderTwig('index.twig', $data);

        return new Response($html);
    }

    public function register(Request $request)
    {
        return new Response(Viewer::renderTwig('register.twig'));
    }
}
