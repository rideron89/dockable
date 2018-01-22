<?php

namespace App;

class Request
{
    public $method = 'GET';
    
    /**
    * An array of all request parameters.
    *
    * @var array
    */
    private $params = [];

    public $path = '/';

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = explode('?', $_SERVER['REQUEST_URI'])[0];

        $this->prepareParams();
    }

    /**
    * Extract the request parameters and parse them into the $params property.
    */
    private function prepareParams()
    {
        if (isset($_GET))
        {
            foreach ($_GET as $key => $value)
            {
                $this->params[$key] = $value;
            }
        }
        else if (isset($_POST))
        {
            Response::send('not implemented yet', 501);
        }
    }

    /**
    * Retrieve either all params or a specific param by key.
    *
    * @param string $key [default: null]
    *
    * @return array/string
    */
    public function params($key = null)
    {
        if ($key === null)
        {
            return $this->params;
        }

        return $this->params[$key];
    }
}
