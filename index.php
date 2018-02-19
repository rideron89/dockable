<?php

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/app/AppKernel.php';

require __DIR__ . '/routes.php';

/*------------------------------------------------------------*
 |
 |    Load the application and start it up!
 |
 *------------------------------------------------------------*/
App\AppKernel::start();
