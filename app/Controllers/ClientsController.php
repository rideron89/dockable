<?php

namespace App\Controllers;

use MongoDB\BSON\ObjectId;
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
        $db = new MongoClient('dockable', 'clients');
        $id = $request->request->get('id');

        $allowedFields = ['name'];
        $params = [];

        foreach ($allowedFields as $field)
        {
            $value = $request->request->get($field);

            if ($value)
            {
                $params[$field] = $value;
            }
        }

        $result = $db->update(['_id' => new ObjectId($id)], $params);

        if ($result->err)
        {
            return new Response($result->err, 500);
        }

        return new Response(json_encode($result), 200);
    }

    /**
    * Reset the client by creating a new secret it for it. This will
    * invalidate the old one immediately.
    *
    * @param Symfony\Component\HttpFoundation\Request
    *
    * @return Symfony\Component\HttpFoundation\Response
    */
    public function reset(Request $request)
    {
        $db = new MongoClient('dockable', 'clients');
        $id = $request->request->get('id');

        $secret = bin2hex(openssl_random_pseudo_bytes(32));

        $result = $db->update(['_id' => new ObjectId($id)], ['secret' => $secret]);

        if ($result->err)
        {
            return new Response($result->err, 500);
        }

        return new Response(json_encode($result->data));
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

        $id = $request->request->get('id');

        $result = $db->delete(['_id' => new ObjectId($id)]);

        if ($result->err)
        {
            return new Response($result->err, 500);
        }

        return new Response(json_encode($id));

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
