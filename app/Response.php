<?php

namespace App;

class Response
{
    /**
    * Encode a message and send it back to the client, with a status code. This
    * method ends the current script.
    *
    * @param string $message [default: '']
    * @param int $statusCode [default: 200]
    */
    public static function send($message = '', $statusCode = 200)
    {
        http_response_code($statusCode);

        if (gettype($message) === 'array')
        {
            header('Content-Type: application/json');
            $message = json_encode($message);
        }

        // set additional headers

        // send response
        echo $message;

        // end the script
        die();
    }

    /**
    * Load a view and send it back to the client.
    *
    * @param string $filePath
    */
    public static function view($filePath)
    {
        $html = Viewer::loadHtml($filePath);

        self::send($html, 200);
    }
}
