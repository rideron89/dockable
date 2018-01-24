<?php

namespace App;

class Request
{
    /**
    * Type of HTTP method in the request.
    *
    * @var string [default: 'GET']
    */
    public $method = 'GET';

    /**
    * A list of all route parameters found in the path.
    *
    * @var array
    */
    public $params = [];

    /**
    * A list of all POST parameters in the request.
    *
    * @var array
    */
    public $post = [];

    /**
    * A list of all query parameters found in the query string.
    *
    * @var array
    */
    public $query  = [];

    /**
    * Path of request URI, without the hostname.
    *
    * @var string [default: '/']
    */
    public $path = '/';

    public $originalRequest = [];

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = explode('?', $_SERVER['REQUEST_URI'])[0];

        $this->originalRequest = $_SERVER;

        if ($this->method === 'POST')
        {
            if ($_POST)
            {
                $this->post = $_POST;
            }
        }
        else if ($this->method === 'PUT')
        {
            $putData = file_get_contents('php://input');

            $this->post = json_decode($putData, true);
        }

        $this->parseQueryParams();
    }

    /**
    * Extract the request parameters and parse them into the $params property.
    */
    private function parseQueryParams()
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
}
