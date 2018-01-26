<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Databases\MongoClient;

/*
Client:
    id: ObjectId
    name: String
    redirect_uri: String
    owner_id: String
    secret: String
*/

/*
User:
    id: ObjectId
    username: String
    password: String (hashed)
*/

class ClientsController
{
    /**
    * Register a new client, giving them a secret and refresh token.
    *
    * @param Symfony\Component\HttpFoundation\Request $request
    *
    * @return Symfony\Component\HttpFoundation\Response
    */
    public function register(Request $request)
    {
        $db = new MongoClient('dockable', 'clients');

        // only extract the params we want
        $params = [
            'name'         => $request->request->get('name'),
            'redirect_uri' => $request->request->get('redirect_uri'),
            'user_id'      => $request->attributes->get('user_id'),
        ];

        // make sure we have received all required fields
        foreach ($params as $key => $value)
        {
            if (!$value)
            {
                return new Response("\"$key\" field is required", 400);
            }
        }

        // check for a client with the same name
        if ($db->exists(['name' => $params['name']]))
        {
            return new Response("client already exists with name \"{$params['name']}\"", 409);
        }

        // create the secret and refresh token
        $params['secret'] = bin2hex(openssl_random_pseudo_bytes(32));

        $result = $db->create($params);

        if ($result->err)
        {
            return new Response($result->err, 500);
        }

        return new Response(json_encode($result->data), 200);
    }

    public function update(Request $request)
    {
        return new Response('update()', 200);
    }

    /**
    * Unregister a client from the server.
    *
    * @param Symfony\Component\HttpFoundation\Request
    *
    * @return Symfony\Component\HttpFoundation\Response
    */
    public function unregister(Request $request)
    {
        $db = new MongoClient('dockable', 'clients');

        $params = [
            'name'   => $request->request->get('name'),
            'secret' => $request->request->get('secret'),
        ];

        // make sure we have received all required fields
        foreach ($params as $key => $value)
        {
            if (!$value)
            {
                return new Response("\"$key\" field is required", 400);
            }
        }

        // try to get the client object
        $result = $db->find(['name' => $params['name']]);

        if (count($result->data) < 1)
        {
            return new Response("client \"{$params['name']}\" not found", 404);
        }

        if ($result->data[0]['secret'] !== $params['secret'])
        {
            return new Response('unauthorized', 401);
        }

        // try to delete the client from the database
        $db->delete(['id' => $result->data[0]['id']]);

        return new Response('unregistered', 200);
    }
}
