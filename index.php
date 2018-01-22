<?php

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/app/Application.php';

/*------------------------------------------------------------*
 |
 |    Load the application and start it up!
 |
 *------------------------------------------------------------*/
$app = new App\Application();
$app->start();
