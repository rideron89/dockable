<?php

namespace App\Databases;

use Symfony\Component\Dotenv\Dotenv;

class Client
{
    /**
    * Hostname of the database.
    *
    * @var string
    */
    protected $dbHost;

    /**
    * Port number of the database.
    *
    * @var int
    */
    protected $dbPort;

    public function __construct()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');

        $this->dbHost = getenv('DB_HOST');
        $this->dbPort = getenv('DB_PORT');
    }
}
