<?php

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/app/Application.php';

require __DIR__ . '/routes.php';

/*------------------------------------------------------------*
 |
 |    Load the application and start it up!
 |
 *------------------------------------------------------------*/
App\Application::start();
