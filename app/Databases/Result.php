<?php

namespace App\Databases;

class Result
{
    /**
    * Holds the error message if one was found. Null, otherwise.
    *
    * @var string
    */
    public $error;

    /**
    * Holds all the results from the database operation.
    *
    * @var array
    */
    public $results;

    /**
    * The number of results returned by the database operation.
    *
    * @var int
    */
    public $count;

    public function __construct($results, $error = false)
    {
        $this->results = $results;
        $this->count = count($results);

        $this->error = $error;
    }
}
