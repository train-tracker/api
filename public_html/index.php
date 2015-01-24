<?php
session_cache_limiter(false);
session_start();

ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../app/start.php';

$app->run();
