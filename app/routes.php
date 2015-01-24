<?php

App::post('/login', function() use($app) {
  $vars = json_decode($app->request->getBody());

  $filter = ['email'=>$vars->email, 'password'=>sha1($vars->password)];
  $reply = DB::table('user')->filter($filter)->run()->toNative();

  if (!empty($reply)){
    $_SESSION['id'] = $reply[0]['id'];
    $_SESSION['first_name'] = $reply[0]['first_name'];
    $_SESSION['last_name'] = $reply[0]['last_name'];
    $_SESSION['username'] = $reply[0]['username'];
    $_SESSION['email'] = $reply[0]['email'];
    if (isset($reply[0]['isAdmin'])) $_SESSION['isAdmin'] = $reply[0]['isAdmin'];

    App::render(200,array(
      'msg' => 'Logged In'
    ));
  } else {
    App::render(403,array(
      'msg' => 'Not Logged In'
    ));
  }
});

App::get('/logout', function() use($app) {
  unset($_SESSION['id']);
  App::render(200,array(
    'msg' => 'Logged Out'
  ));
});

// route middleware for simple API authentication
App::get('/session', function() use ($app) {
  if (isset($_SESSION['id'])){
    $userInfo = $_SESSION;
    $app->render(200,array(
      'msg' => 'Authenticated',
      'data' => $userInfo,
      'error' => false
    ));
  }else{
    $app->render(403,array(
      'msg' => 'Not Authenticated',
      'error' => true
    ));
  }
});


App::get('/test', 'authenticate', function() use($app) {
  App::render(200,array(
    'msg' => 'Logged In'
  ));
});

App::post('/register', '\Controllers\AuthController::register');

App::get('/data', function() use ($app) {
  $data = DB::table('data')->run()->toNative();

  $app->render(200,array(
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


