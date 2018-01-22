<?php

namespace App;

class Viewer
{
    /**
    * Load an HTML file based on filename.
    *
    * @param string $filename
    *
    * @return string
    */
    public static function loadHtml($filename)
    {
        $fullPath = __DIR__ . '/../views/' . $filename;
        $html = file_get_contents($fullPath);

        return $html;
    }
}
