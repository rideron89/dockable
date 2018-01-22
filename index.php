<?php

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/app/Application.php';

$app = new App\Application();
$app->start();
