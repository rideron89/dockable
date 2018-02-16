<?php

namespace App\Controllers;

use App\Viewer;
use App\Databases\MongoClient;
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

        // TODO: need to find a safeguard for unauthorized cookies

        $user = ($auth) ? $auth['user'] : [];

        // get all tokens
        $client = new MongoClient('dockable', 'auth_tokens');
        $tokens = $client->find()->data;

        // get all users
        $client = new MongoClient('dockable', 'users');
        $users  = $client->find([], ['projection' => ['password' => 0]])->data;

        $html = Viewer::renderTwig('index.twig', ['user' => $user, 'tokens' => $tokens, 'users' => $users]);

        return new Response($html);
    }

    public function register(Request $request)
    {
        return new Response(Viewer::renderTwig('register.twig'));
    }
}
