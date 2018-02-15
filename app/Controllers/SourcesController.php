<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;

use App\Response;
use App\Databases\MongoClient;

use MongoDB\BSON\ObjectId;

class SourcesController
{
    /**
    * Return a list of all sources.
    *
    * @param Symfony\Component\HttpFoundation\Request $request
    */
    public function index(Request $request)
    {
        $client = new MongoClient('dockable', 'sources');

        $result = $client->find();

        if ($result->err) {
            Response::send($result->err, 500);
        }

        Response::send($result->documents);
    }

    /**
    * Send a single source in the response, based on a route parameter.
    *
    * @param Symfony\Component\HttpFoundation\Request $request
    */
    public function read(Request $request)
    {
        $id = new ObjectId($request->params['source_id']);

        $client = new MongoClient('dockable', 'sources');

        $result = $client->find(['_id' => $id]);

        if ($result->err) {
            Response::send($result->err, 500);
        }

        if (count($result->documents) < 1) {
            Response::send('source not found', 404);
        }

        Response::send($result->documents[0]);
    }

    /**
    * Create a new source from POST data.
    *
    * @param Symfony\Component\HttpFoundation\Request $request
    */
    public function create(Request $request)
    {
        $document = $request->request->all();

        $client = new MongoClient('dockable', 'sources');

        $result = $client->create($document);

        if ($result->err) {
            Response::send($result->err, 500);
        }

        Response::send($result->documents);
    }

    /**
    * Update sources from PUT data.
    *
    * @var Symfony\Component\HttpFoundation\Request $request
    */
    public function update(Request $request)
    {
        $id = new ObjectId($request->params['source_id']);
        $document = $request->request->all();

        $client = new MongoClient('dockable', 'sources');

        $result = $client->update(['_id' => $id], $document);

        if ($result->err) {
            Response::send($result->err, 500);
        }

        Response::send($document);
    }

    /**
    * Delete sources from route parameters.
    *
    * @var Symfony\Component\HttpFoundation\Request $request
    */
    public function delete(Request $request)
    {
        $id = new ObjectId($request->params['source_id']);

        $client = new MongoClient('dockable', 'sources');

        $result = $client->delete(['_id' => $id]);

        if ($result->err) {
            Response::send($result->err, 500);
        }

        Response::send('deleted "' . $request->params['source_id'] . '"');
    }
}
