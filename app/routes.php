<?php

App::post('/login', function() use($app) {
  $vars = json_decode($app->request->getBody());

  $filter = ['email'=>$vars->email, 'pass'=>password_hash($vars->password, PASSWORD_BCRYPT)];

  $reply = DB::table('user')->filter($filter)->run()->toNative();

  App::render(200,array(
    'msg' => 'Logged In',
    'data' => $vars
  ));
});

App::get('/data', function() use ($app) {
  $data = DB::table('data')->run()->toNative();

  $app->render(HTTPError::get('OK'),array(
    'msg' => 'Some Data!',
    'data' => $data,
    'error' => false
  ));
});

$app->options('/(:name+)', function($name) use ($app) {
  $response = $app->response();
  $response->header('Access-Control-Allow-Origin', '*');
  $response->header('Access-Control-Allow-Headers', 'Content-Type');
  $response->header('Access-Control-Allow-Credentials', 'true');
  $response->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE, PATCH');

  $app->render(200,array(
    'msg' => "Options return"
  ));

});


