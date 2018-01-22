<?php

namespace App\Databases;

use App\Response;

use MongoDB\Driver\Command;
use MongoDB\Driver\Manager as MongoDriver;
use MongoDB\Driver\Query;
use MongoDB\Driver\Exception\Exception as MongoException;

class MongoCursor
{
    /**
    * List of all rows after performing any limit or filter methods.
    *
    * @var array
    */
    private $rows;

    public function __construct($documents)
    {
        $this->rows = [];

        foreach ($documents as $doc)
        {
            $this->rows[] = $doc;
        }
    }

    /**
    * Return all the rows referenced by the cursor.
    *
    * @return array
    */
    public function all()
    {
        return $this->rows;
    }

    /**
    * Reduce the number of rows to a specific amount. This method returns the
    * rows directly.
    *
    * @param int $count [default: 10]
    *
    * @return array
    */
    public function limit($count = 10)
    {
        $newRows = [];
        $index = 0;

        foreach ($this->rows as $row)
        {
            if ($index < $count)
            {
                $newRows[] = $row;
                $index++;
            }
        }

        $this->rows = $newRows;

        return $this->rows;
    }
}

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
}
