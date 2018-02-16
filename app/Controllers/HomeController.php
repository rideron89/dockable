<?php

namespace App\Controllers;

use App\Viewer;
use App\Databases\MongoClient;
use App\Services\AuthenticateUserService;
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

        if (!$auth) {
            $user = [];
        } else {
            $client = new MongoClient('dockable', 'users');

            $user = $client->find([
                '_id' => new ObjectId($auth['user_id']['$oid'])
            ], [
                'projection' => ['password' => 0]
            ]);
        }

        $html = Viewer::renderTwig('index.twig', ['user' => $user]);

        return new Response($html);
    }

    public function login(Request $request)
    {
        return new Response(Viewer::renderTwig('login.twig'));
    }

    public function register(Request $request)
    {
        return new Response(Viewer::renderTwig('register.twig'));
    }
}
