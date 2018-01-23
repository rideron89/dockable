<?php

namespace App\Databases;

use App\Response;

use MongoDB\Driver\Command;
use MongoDB\Driver\Manager as MongoDriver;
use MongoDB\Driver\BulkWrite;
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
    private $collectionString;

    public function __construct($databaseName, $collectionName)
    {
        parent::__construct();

        $this->collectionString = "{$databaseName}.{$collectionName}";
    }

    /**
    * Try to connect to the Mongo database, and return the connection
    * instance.
    *
    * @return MongoDB\Driver\Manager
    */
    private function connect()
    {
        $connection = new MongoDriver('mongodb://' . $this->dbHost . ':' . $this->dbPort);

        return $connection;
    }

    /**
    * Find all rows for a given filter configuration.
    *
    * @param array $filter
    * @param array $options
    *
    * @return MongoCursor
    */
    public function find($filter = [], $options = [])
    {
        $rows = [];

        try
        {
            $connection = $this->connect();

            $query = new Query($filter, $options);
            $cursor = $connection->executeQuery($this->collectionString, $query);

            return new MongoCursor($cursor);
        }
        catch (MongoException $e)
        {
            Response::send($e->getMessage(), 500);
        }
    }

    /**
    * Create a document from POST data.
    *
    * @param array $document
    */
    public function create($document)
    {
        try
        {
            $connection = $this->connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $bulk->insert($document);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);
        }
        catch (MongoException $e)
        {
            Response::send($e->getMessage(), 500);
        }
        catch (BulkWriteException $e)
        {
            Response::send($e->getMessage(), 500);
        }
    }

    /**
    * Update documents based on a filter.
    *
    * @var array $filter
    * @var array $document
    */
    public function update($filter, $document)
    {
        try
        {
            $connection = $this->connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $bulk->update($filter, ['$set' => $document]);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);
        }
        catch (MongoException $e)
        {
            Response::send($e->getMessage(), 500);
        }
        catch (BulkWriteException $e)
        {
            Response::send($e->getMessage(), 500);
        }
    }

    /**
    * Delete documents based on a filter.
    *
    * @var array $filter
    */
    public function delete($filter)
    {
        try
        {
            $connection = $this->connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $bulk->delete($filter);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);
        }
        catch (MongoException $e)
        {
            Response::send($e->getMessage(), 500);
        }
        catch (BulkWriteException $e)
        {
            Response::send($e->getMessage(), 500);
        }
    }
}
