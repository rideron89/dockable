<?php

namespace App\Controllers;

use App\Databases\MongoClient;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function update(Request $request)
    {
        $user_id = new ObjectId($request->request->get('id'));

        // filter out parameters for protection's sake
        $params = $request->request->all();
        $params = array_filter($params, function ($value, $key) {
            return (
                ($key !== 'id') && ($key !== 'password')
            );
        }, ARRAY_FILTER_USE_BOTH);

        // try to load the user so we can determine if it exists or not
        $client = new MongoClient('users');
        $user = $client->find(['_id' => $user_id]);

        if ($user->err || !$user->data[0]) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        $client->update(['_id' => $user_id], $params);

        return new Response('Ok', Response::HTTP_OK);
    }
}
