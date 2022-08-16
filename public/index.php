<?php

use Bootstrap\Provider;

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../bootstrap/app.php';

$app = Provider::run();

include '../route/web.php';


