<?php

use Bootstrap\Provider;

/**
 * Require the autoloader
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Bootstrap the application
 */
require_once __DIR__ . '/../bootstrap/app.php';

/**
 * Run the application
 */
$app = Provider::run();

/**
 * Load the routes
 */
require_once __DIR__ . '/../route/web.php';


