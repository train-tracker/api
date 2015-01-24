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
      'msg' => 'Not Logged In',
      'data' => $_SERVER
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
    'data2' => $_SERVER,
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
    'msg' => "Options return",
    'data' => $_SERVER
  ));

});

App::get('/modules', 'authenticate', function() use ($app) {
  $reply = DB::table('module')->run()->toNative();
  $app->render(200,array(
    'msg' => "All Modules",
    'data' => $reply
  ));

});

App::get('/modules/:moduleID', 'authenticate', function($moduleID) use ($app) {
  $filter = array('id' => $moduleID);
  $reply = DB::table('module')->filter($filter)->run()->toNative();
  $filter = array('moduleID' => $moduleID);
  $reply['questions'] = DB::table('moduleQuestion')->filter($filter)->run()->toNative();

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
    'data' => $vars
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
  $result = getModule($app, $moduleID);
  $app->render(200,array(
    'msg' => "Module Deleted",
    'data' => $result
  ));
});

function getModule($app, $moduleID){
  $filter = array('id' => $moduleID);
  return DB::table('module')->filter($filter)->run()->toNative();

}


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
  $vars->moduleID = $moduleID;
  $result = DB::table('moduleQuestion')->insert($vars)->run()->toNative();
  $vars->id = $result['generated_keys'][0];
  $app->render(200,array(
    'msg' => "Module Added",
    'data' => $vars
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


App::get('/user/:userID', 'authenticate', function($userID) use ($app) {
  $user = getUser($app, $userID);
  $app->render(200,array(
    'msg' => "One User",
    'data' => $user
  ));
});

function getUser($app, $userID){
  $filter = array('id' => $userID);
  $user = DB::table('user')->filter($filter)->run()->toNative();
  return $user;
}

App::get('/class', 'authenticate', function() use ($app) {
  $reply = DB::table('class')->run()->toNative();
  $app->render(200,array(
    'msg' => "All Modules",
    'data' => $reply
  ));

});

App::get('/class/:classID', 'authenticate', function($classID) use ($app) {
  $filter = array('id' => $classID);
  $reply = DB::table('class')->filter($filter)->run()->toNative();
  $filter = array('classID' => $classID);

  $users = DB::table('classUser')->filter($filter)->run()->toNative();
  foreach($users as $user){
    $userID = $user['userID'];
    $reply['users'][] = getUser($app, $userID);
  }

  $modules = DB::table('classModule')->filter($filter)->run()->toNative();
  foreach($modules as $module){
    $moduleID = $module['moduleID'];
    $reply['modules'][] = getModule($app, $moduleID);
  }

  $app->render(200,array(
    'msg' => "One Class",
    'data' => $reply
  ));

});

App::post('/classes', 'authenticate', function() use ($app) {
  $vars = json_decode($app->request->getBody());
  $result = DB::table('class')->insert($vars)->run()->toNative();
  $vars->id = $result['generated_keys'][0];
  $app->render(200,array(
    'msg' => "Class Added",
    'data' => $vars
  ));
});

App::put('/classes/:classID', 'authenticate', function($classID) use ($app) {
  $vars = json_decode($app->request->getBody());
  $filter = array('id' => $classID);
  $result = DB::table('class')->filter($filter)->update($vars)->run()->toNative();
  $app->render(200,array(
    'msg' => "Class Updated",
    'data' => $result
  ));
});

App::delete('/classes/:classID', 'authenticate', function($classID) use ($app) {
  $filter = array('id' => $classID);
  $result = DB::table('class')->filter($filter)->delete()->run()->toNative();
  $app->render(200,array(
    'msg' => "Class Deleted",
    'data' => $result
  ));
});

App::get('/class/:classID/user/:userID', 'authenticate', function($classID, $userID) use ($app) {
  $userClass = array('classID'=>$classID, 'userID'=>$userID);
  $result = DB::table('classUser')->insert($userClass)->run()->toNative();
  $app->render(200,array(
    'msg' => "User Added to Class",
    'data' => $result
  ));
});

App::delete('/class/:classID/user/:userID', 'authenticate', function($classID, $userID) use ($app) {
  $filter = array('classID'=>$classID, 'userID'=>$userID);
  $result = DB::table('classUser')->filter($filter)->delete()->run()->toNative();
  $app->render(200,array(
    'msg' => "User Removed from Class",
    'data' => $result
  ));
});

App::get('/class/:classID/module/:moduleID', 'authenticate', function($classID, $moduleID) use ($app) {
  $moduleClass = array('classID'=>$classID, 'moduleID'=>$moduleID);
  $result = DB::table('classModule')->insert($moduleClass)->run()->toNative();
  $app->render(200,array(
    'msg' => "Module Added to Class",
    'data' => $result
  ));
});

App::delete('/class/:classID/module/:moduleID', 'authenticate', function($classID, $moduleID) use ($app) {
  $filter = array('classID'=>$classID, 'moduleID'=>$moduleID);
  $result = DB::table('classModule')->filter($filter)->delete()->run()->toNative();
  $app->render(200,array(
    'msg' => "Module Removed from Class",
    'data' => $result
  ));
});



