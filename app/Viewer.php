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
    public static function renderHtml($filename)
    {
        $fullPath = __DIR__ . '/../views/' . $filename;
        $html = file_get_contents($fullPath);

        return $html;
    }

    public static function renderTwig($filepath, $data = [])
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../views');
        $twig = new \Twig_Environment($loader);

        return $twig->render($filepath, $data);
    }
}
