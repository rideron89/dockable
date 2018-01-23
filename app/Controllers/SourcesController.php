<?php

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Databases\MongoClient;

use MongoDB\BSON\ObjectId;

class SourcesController
{
    /**
    * Return a list of all sources.
    *
    * @param App\Request $request
    */
    public function index(Request $request)
    {
        $client = new MongoClient('dockable', 'sources');

        $sources = $client->find()->all();

        Response::send($sources);
    }

    /**
    * Send a single source in the response, based on a route parameter.
    *
    * @param App\Request $request
    */
    public function single(Request $request)
    {
        $id = new ObjectId($request->params['source_id']);

        $client = new MongoClient('dockable', 'sources');

        $document = $client->find(['_id' => $id])->all();

        Response::send($document);
    }

    /**
    * Create a new source from POST data.
    *
    * @param App\Request $request
    */
    public function create(Request $request)
    {
        $client = new MongoClient('dockable', 'sources');

        $client->create($request->post);

        Response::send($request->post);
    }

    /**
    * Update sources from PUT data.
    *
    * @var App\Request $request
    */
    public function update(Request $request)
    {
        $document = $request->post;
        $id = new ObjectId($request->params['source_id']);

        $client = new MongoClient('dockable', 'sources');

        $client->update(['_id' => $id], $document);

        Response::send($document);
    }

    /**
    * Delete sources from route parameters.
    *
    * @var App\Request $request
    */
    public function delete(Request $request)
    {
        $id = new ObjectId($request->params['source_id']);

        $client = new MongoClient('dockable', 'sources');

        $client->delete(['_id' => $id]);

        Response::send('deleted "' . $request->params['source_id'] . '"');
    }
}
