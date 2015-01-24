<?php

App::post('/login', function() use($app) {
  $vars = json_decode($app->request->getBody());

  $filter = ['email'=>$vars->email, 'password'=>sha1($vars->password)];
  $reply = DB::table('user')->filter($filter)->run()->toNative();

  if (!empty($reply)){
    App::render(200,array(
      'msg' => 'Logged In'
    ));
  }else{
    App::render(403,array(
      'msg' => 'Not Logged In'
    ));
  }

});

App::post('/register', function() {

  // Get the user
  $user = json_decode(Request::getBody(), true);

  // Hash the password
  $password = sha1($user['password']);

  // Make sure the passwords match
  if($user['password'] != $user['passwordConfirm']) {

    // If not, return a validation error
    $code = 422;
    $response = ['msg' => 'Passwords must match', 'error' => true];
  } else {

    // Set the password to the hash
    $user['password'] = $password;

    // Unset the confirm
    unset($user['passwordConfirm']);

    // Make sure the email is unique
    $emailExists = DB::table('user')->filter(['email' => $user['email']])->run()->toNative();

    // If it isn't, return a 403
    if(empty($emailExists)) {

      // Otherwise insert it and return if possible
      try {
        $newEmail = DB::table('user')->insert($user);
        $code = 200;
        $response = ['msg' => 'User created', 'data' => $newEmail];
      } catch(Exception $e){
        $code = 500;
        $response = ['msg' => 'There was an error registering your user. Please try again'];
      }

    } else {
      $code = 403;
      $response = ['msg' => 'There was a problem with your email or password. Please check your email/password and try again.'];
    }
  }

  App::render($code, $response);
});

App::post('/register', function() {

  // Get the user
  $user = json_decode(Request::getBody(), true);

  // Hash the password
  $password = sha1($user['password']);

  // Make sure the passwords match
  if($user['password'] != $user['passwordConfirm']) {

    // If not, return a validation error
    $code = 422;
    $response = ['msg' => 'Passwords must match', 'error' => true];
  } else {

    // Set the password to the hash
    $user['password'] = $password;

    // Unset the confirm
    unset($user['passwordConfirm']);

    // Make sure the email is unique
    $emailExists = DB::table('user')->filter(['email' => $user['email']])->run()->toNative();

    // If it isn't, return a 403
    if(empty($emailExists)) {

      // Otherwise insert it and return if possible
      try {
        $newEmail = DB::table('user')->insert($user);
        $code = 200;
        $response = ['msg' => 'User created', 'data' => $newEmail];
      } catch(Exception $e){
        $code = 500;
        $response = ['msg' => 'There was an error registering your user. Please try again'];
      }

    } else {
      $code = 403;
      $response = ['msg' => 'There was a problem with your email or password. Please check your email/password and try again.'];
    }
  }

  App::render($code, $response);
});

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


