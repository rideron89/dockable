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
    * Check to see if something exists in the databse. Does not return that
    * document.
    *
    * TODO should probably not just return TRUE on error.
    *
    * @param array $filter
    * @param array $options
    *
    * @return bool
    */
    public function exists($filter = [], $options = [])
    {
        $documents = [];

        try {
            $connection = $this->_connect();

            $query = new Query($filter, $options);
            $cursor = $connection->executeQuery($this->collectionString, $query);

            foreach ($cursor as $doc)
            {
                array_push($documents, (array)$doc);
            }

            return count($documents) > 0;
        } catch (MongoException $e) {
            return true;
        }
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
        $result = new DatabaseResult();

        try {
            $connection = $this->_connect();

            $query = new Query($filter, $options);
            $cursor = $connection->executeQuery($this->collectionString, $query);

            foreach ($cursor as $doc) {
                array_push($documents, (array)$doc);
            }

            $result->data = $documents;
        } catch (MongoException $e) {
            $result->err = $e->getMessage();
        }

        return $result;
    }

    /**
    * Find a single row for a given filter configuration.
    *
    * @param array $filter
    * @param array $options
    *
    * @return App\Databases\Result
    */
    public function findOne($filter = [], $options = [])
    {
        $documents = [];
        $result = new DatabaseResult();

        try {
            $connection = $this->_connect();

            $query = new Query($filter, $options);
            $cursor = $connection->executeQuery($this->collectionString, $query);

            foreach ($cursor as $doc) {
                array_push($documents, (array)$doc);
            }

            $result->data = $documents[0];
        } catch (MongoException $e) {
            $result->err = $e->getMessage();
        }

        return $result;
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
        $result = new DatabaseResult();

        try {
            $connection = $this->_connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $newId = $bulk->insert($document);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);

            $newDocument = $document;
            $newDocument['id'] = $newId->jsonSerialize()['$oid'];

            $result->data = $newDocument;
        } catch (MongoException $e) {
            $result->err = $e->getMessage();
        } catch (BulkWriteException $e) {
            $result->err = $e->getMessage();
        }

        return $result;
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
        $result = new DatabaseResult();

        try {
            $connection = $this->_connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $bulk->update($filter, ['$set' => $document]);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);

            $result->data = $document;
        } catch (MongoException $e) {
            $result->err = $e->getMessage();
        } catch (BulkWriteException $e) {
            $result->err = $e->getMessage();
        }

        return $result;
    }

    public function updateImproved($filter, $rules)
    {
        $result = new DatabaseResult();

        try {
            $connection = $this->_connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $bulk->update($filter, $rules);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);

            $result->data = $document;
        } catch (MongoException $e) {
            $result->err = $e->getMessage();
        } catch (BulkWriteException $e) {
            $result->err = $e->getMessage();
        }

        return $result;
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
        $result = new DatabaseResult();

        try {
            $connection = $this->_connect();
            $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);

            $bulk = new BulkWrite();
            $bulk->delete($filter);

            $connection->executeBulkWrite($this->collectionString, $bulk, $writeConcern);

            $result->data = $filter;
        } catch (MongoException $e) {
            $result->err = $e->getMessage();
        } catch (BulkWriteException $e) {
            $result->err = $e->getMessage();
        }

        return $result;
    }
}
