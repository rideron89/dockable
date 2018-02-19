<?php

namespace App\Databases;

use App\Response;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager as MongoDriver;
use MongoDB\Driver\Query;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Exception\Exception as MongoException;
use MongoDB\Driver\Exception\BulkWriteException;

class MongoClient extends Client
{
    /**
    * Formatted connection string with the database and collection name.
    *
    * @var string
    */
    private $collection;

    private $driver;

    public function __construct($collection)
    {
        parent::__construct();

        $database = [
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'name' => getenv('DB_NAME'),
        ];

        $this->collection = "$database[name].$collection";

        $this->driver = new MongoDriver("mongodb://$database[host]:$database[port]");
    }

    /**
    * Find all rows for a given filter configuration.
    *
    * @param array $filter
    * @param array $options
    *
    * @return App\Databases\Result
    */
    public function find($model, $filter = [], $options = [])
    {
        $query      = new Query($filter, $options);
        $cursor     = $this->driver->executeQuery($this->collection, $query);
        $documents  = [];

        foreach ($cursor as $doc) {
            $documents[] = new $model($doc);
        }

        return $documents;
    }

    /**
    * Find a single row for a given filter configuration.
    *
    * @param array $filter
    * @param array $options
    *
    * @return App\Databases\Result
    */
    public function findOne($model, $filter = [], $options = [])
    {
        $query      = new Query($filter, $options);
        $cursor     = $this->driver->executeQuery($this->collection, $query);
        $documents  = [];

        return $cursor->toArray()[0];
    }

    /**
    * Create a document from POST data.
    *
    * @param array $document
    *
    * @return App\Databases\DatabaseResult
    */
    public function create($document)
    {
        $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

        $bulk = new BulkWrite();
        $newId = $bulk->insert($document);

        $this->driver->executeBulkWrite($this->collection, $bulk, $writeConcern);

        $newDocument = $document;
        $newDocument['_id'] = $newId;

        return $newDocument;
    }

    /**
    * Update documents based on a filter.
    *
    * @var array $filter
    * @var array $document
    *
    * @return App\Databases\Result
    */
    public function update($filter, $document)
    {
        $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

        $bulk = new BulkWrite();
        $bulk->update($filter, ['$set' => $document]);

        $this->driver->executeBulkWrite($this->collection, $bulk, $writeConcern);

        return $document;
    }

    /**
    * Delete documents based on a filter.
    *
    * @var array $filter
    *
    * @return App\Databases\DatabaseResult
    */
    public function delete($filter)
    {
        $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

        $bulk = new BulkWrite();
        $bulk->delete($filter);

        $this->driver->executeBulkWrite($this->collection, $bulk, $writeConcern);

        return $filter;
    }
}
