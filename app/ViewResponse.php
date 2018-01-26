<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;

class ViewResponse extends Response
{

    /**
    * Load a view and send it back to the client.
    *
    * @param string $filePath
    */
    public function send($filePath)
    {
        $html = Viewer::loadHtml($filePath);

        __parent::send($html);
    }
}
