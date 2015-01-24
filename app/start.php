<?php

  use SlimFacades\Facade;

  $app = new \Slim\Slim(require __DIR__.'/configuration.php');

  Facade::setFacadeApplication($app);
  Facade::registerAliases();

  Facade::registerAliases(array('DB'=>'\Facades\DBFacade'));

  $app->view(new \JsonApiView());
  $app->add(new \JsonApiMiddleware());

  function authenticate(\Slim\Route $route) {
    $app = \Slim\Slim::getInstance();
    if (empty($_SESSION)) {
      $app->render(401,array(
        'msg' => 'Not Logged In'
      ));
    }
    return true;
  }

  $app->add(new \Middleware\OptionsHeaders());

  $app->container->singleton('Database', function() use ($app){
    $database = r\connect($app->config('RETHINK_HOST'));
    $database->useDb($app->config('RETHINK_DATABASE'));
    return new Database($database);
  });

  require __DIR__.'/routes.php';

  return $app;
