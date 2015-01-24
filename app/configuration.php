<?php

$defaultConfig = array(
  'templates.path' => __DIR__.'/templates',
  'debug' => false
);

return array_merge($defaultConfig, require __DIR__.'/../.env.php');