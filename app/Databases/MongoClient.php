<?php

namespace App\Databases;

use App\Response;

use App\Databases\Result as DatabaseResult;

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
    private function _connect()
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
    * @return App\Databases\Result
    */
    public function find($filter = [], $options = [])
    {
        $documents = [];

        try
        {
            $connection = $this->_connect();

            $query = new Query($filter, $options);
            $cursor = $connection->executeQuery($this->collectionString, $query);

            foreach ($cursor as $doc)
            {
                array_push($documents, (array)$doc);
            }

            return new DatabaseResult($documents);
        }
        catch (MongoException $e)
        {
            return new DatabaseResult(null, $e->getMessage());
        }
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
        try
        {
            $connection = $this->_connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $newId = $bulk->insert($document);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);

            return new DatabaseResult(['id' => $newId]);
        }
        catch (MongoException $e)
        {
            return new DatabaseResult(null, $e->getMessage());
        }
        catch (BulkWriteException $e)
        {
            return new DatabaseResult(null, $e->getMessage());
        }
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
        try
        {
            $connection = $this->_connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $bulk->update($filter, ['$set' => $document]);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);

            return new DatabaseResult($document);
        }
        catch (MongoException $e)
        {
            return new DatabaseResult(null, $e->getMessage());
        }
        catch (BulkWriteException $e)
        {
            return new DatabaseResult(null, $e->getMessage());
        }
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
        try
        {
            $connection = $this->_connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $bulk->delete($filter);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);

            return new DatabaseResult($filter);
        }
        catch (MongoException $e)
        {
            return new DatabaseResult(null, $e->getMessage());
        }
        catch (BulkWriteException $e)
        {
            return new DatabaseResult(null, $e->getMessage());
        }
    }
}
