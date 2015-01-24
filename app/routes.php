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
      'msg' => 'Logged In',
      'data' => $_SESSION
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
App::get('/session', 'authenticate', function() use ($app) {
  $userInfo = $_SESSION;
  $app->render(200,array(
    'msg' => 'Authenticated',
    'data' => $userInfo,
    'error' => false
  ));
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

App::options('/(:name+)', function($name) use ($app) {


  $response = $app->response();
  $response->header('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
  $response->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
  $response->header('Access-Control-Allow-Credentials', 'true');
  $response->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE, PATCH');

  $app->render(200,array(
    'msg' => "Options return"
  ));

});

App::get('/modules', 'authenticate', function() use ($app) {
  $reply = DB::table('module')->run()->toNative();
  $app->render(200,array(
    'msg' => "All Modules",
    'data' => $reply
  ));

});

App::post('/modules', 'authenticate', function() use ($app) {
  $vars = json_decode($app->request->getBody());
  $result = DB::table('module')->insert($vars)->run()->toNative();
  $vars->id = $result['generated_keys'][0];
  $app->render(200,array(
    'msg' => "Module Added",
    'data' => $result
  ));
});

App::put('/modules/:moduleID', 'authenticate', function($moduleID) use ($app) {
  $vars = json_decode($app->request->getBody());
  $filter = array('id' => $moduleID);
  $result = DB::table('module')->filter($filter)->update($vars)->run()->toNative();
  $app->render(200,array(
    'msg' => "Module Updated",
    'data' => $result
  ));
});

App::delete('/modules/:moduleID', 'authenticate', function($moduleID) use ($app) {
  $filter = array('id' => $moduleID);
  $result = DB::table('module')->filter($filter)->delete()->run()->toNative();
  $app->render(200,array(
    'msg' => "Module Deleted",
    'data' => $result
  ));
});

App::get('/modules/:moduleID/questions', 'authenticate', function($moduleID) use ($app) {
  $filter = array('moduleID' => $moduleID);
  $reply = DB::table('moduleQuestion')->filter($filter)->run()->toNative();
  $app->render(200,array(
    'msg' => "All Modules",
    'data' => $reply
  ));

});

App::post('/modules/:moduleID/questions', 'authenticate', function($moduleID) use ($app) {
  $vars = json_decode($app->request->getBody());
  $filter = array('moduleID' => $moduleID);
  $result = DB::table('moduleQuestion')->insert($vars)->run()->toNative();
  $vars->id = $result['generated_keys'][0];
  $app->render(200,array(
    'msg' => "Module Added",
    'data' => $result
  ));
});

App::put('/modules/:moduleID/questions/:questionID', 'authenticate', function($moduleID, $questionID) use ($app) {
  $vars = json_decode($app->request->getBody());
  $filter = array('id' => $questionID);
  $result = DB::table('moduleQuestion')->filter($filter)->update($vars)->run()->toNative();
  $app->render(200,array(
    'msg' => "Module Added",
    'data' => $result
  ));
});

App::delete('/modules/:moduleID/questions/:questionID', 'authenticate', function($moduleID, $questionID) use ($app) {
  $filter = array('id' => $questionID);
  $result = DB::table('moduleQuestion')->filter($filter)->delete()->run()->toNative();
  $app->render(200,array(
    'msg' => "Module Deleted",
    'data' => $result
  ));
});


App::get('/modules/:moduleID/questions/:questionsID/answers', 'authenticate', function($moduleID, $questionID) use ($app) {
  $filter = array('moduleID' => $moduleID);
  $reply = DB::table('moduleQuestionAnswer')->run()->toNative();
  $app->render(200,array(
    'msg' => "All Modules",
    'data' => $reply
  ));

});

App::post('/modules/:moduleID/questions/:questionsID/answers', 'authenticate', function($moduleID, $questionID) use ($app) {
  $vars = json_decode($app->request->getBody());
  $vars->moduleID = $moduleID;
  $result = DB::table('moduleQuestionAnswer')->insert($vars)->run()->toNative();
  $vars->id = $result['generated_keys'][0];
  $app->render(200,array(
    'msg' => "Module Added",
    'data' => $result
  ));
});

App::put('/modules/:moduleID/questions/:questionID/answers/:answerID', 'authenticate', function($moduleID, $questionID, $answerID) use ($app) {
  $vars = json_decode($app->request->getBody());
  $filter = array('id' => $answerID);
  $result = DB::table('moduleQuestionAnswer')->filter($filter)->update($vars)->run()->toNative();
  $app->render(200,array(
    'msg' => "Module Added",
    'data' => $result
  ));
});

App::delete('/modules/:moduleID/questions/:questionID/answers/:answerID', 'authenticate', function($moduleID, $questionID, $answerID) use ($app) {
  $vars = json_decode($app->request->getBody());
  $filter = array('id' => $answerID);
  $result = DB::table('moduleQuestionAnswer')->filter($filter)->delete()->run()->toNative();
  $app->render(200,array(
    'msg' => "Module Deleted",
    'data' => $result
  ));
});

