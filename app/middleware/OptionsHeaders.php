<?php namespace Middleware;

use \Response, \Config;

/**
 * Options request headers middleware
 */

class OptionsHeaders extends \Slim\Middleware {

  public function call() {
    Response::header('Access-Control-Allow-Origin', Config::get('ALLOWED_ORIGIN'));
    Response::header('Access-Control-Allow-Headers', 'Content-Type');
    Response::header('Access-Control-Allow-Credentials', 'true');
    $this->next->call();
  }

}
