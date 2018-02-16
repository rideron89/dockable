<?php

namespace App\Databases;

class Result
{
    /**
    * Holds the error message if one was found. Null, otherwise.
    *
    * @var string
    */
    public $err;

    /**
    * Holds all the results from the database operation.
    *
    * @var array
    */
    public $data;

    public function __construct($results = [], $error = false)
    {
        $this->data = $results;

        $this->err = $error;
    }
}
