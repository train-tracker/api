<?php

  use SlimFacades\Facade;

  $app = new \Slim\Slim(require __DIR__.'/configuration.php');

  Facade::setFacadeApplication($app);
  Facade::registerAliases();

  Facade::registerAliases(array('DB'=>'\Facades\DBFacade'));

  $app->view(new \JsonApiView());
  $app->add(new \JsonApiMiddleware());


  //$app->add(new \Slim\Middleware\SessionCookie(array('domain' => 'dockerhost')));

  function authenticate(\Slim\Route $route) {
    $app = \Slim\Slim::getInstance();
    if (!isset($_SESSION['id'])) {
      $app->render(403,array(
        'msg' => 'Not Logged In',
        'data' => $_SESSION
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
