<?php

ini_set('display_errors', 1);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

$config = require __DIR__.'/app/configuration.php';

$conn = r\connect($config['RETHINK_HOST']);
$conn->useDb($config['RETHINK_DATABASE']);

if(!isset($argv[1])){
    doDB($conn);
    doUsers($conn);
    doModules($conn);
    die();
}

switch($argv[1]){
  case ('db'):
    doDB($conn);
  case ('users'):
    doUsers($conn);
    break;
  case ('modules'):
    doModules($conn);
    break;
  case ('all'):
    doDB($conn);
    doUsers($conn);
    doModules($conn);
    break;

}

function doDB($conn){
  try{
    r\dbCreate("traintracker")->run($conn);
  }catch(Exception $e){}
}

function doUsers($conn){
  // Create a test table

  try{
    r\db("traintracker")->tableDrop("user")->run($conn);
  }catch(Exception $e){}

  r\db("traintracker")->tableCreate("user")->run($conn);

  $spass = sha1('1234');

  $user1 = array('username' => "testuser", "first_name"=>"test", "last_name"=>"user", "password" => $spass, "email"=> "test@user.com", "admin" => 1);
  $user2 = array('username' => "testuser2", "first_name"=>"test", "last_name"=>"user2", "password" => $spass, "email"=> "test@user2.com");

  r\db("traintracker")->table('user')->insert($user1)->run($conn)->toNative();
  r\db("traintracker")->table('user')->insert($user2)->run($conn)->toNative();

}

function doModules($conn){
  try{
    r\db("traintracker")->tableDrop("module")->run($conn);
  }catch(Exception $e){}
  try{
    r\db("traintracker")->tableDrop("moduleQuiz")->run($conn);
  }catch(Exception $e){}
  try{
    r\db("traintracker")->tableDrop("moduleQuestion")->run($conn);
  }catch(Exception $e){}
  try{
    r\db("traintracker")->tableDrop("moduleQuestionAnswer")->run($conn);
  }catch(Exception $e){}
  try{
    r\db("traintracker")->tableDrop("moduleUser")->run($conn);
  }catch(Exception $e){}

  r\db("traintracker")->tableCreate("module")->run($conn);
  r\db("traintracker")->tableCreate("moduleQuestion")->run($conn);
  r\db("traintracker")->tableCreate("moduleQuestionAnswer")->run($conn);
  r\db("traintracker")->tableCreate("moduleUser")->run($conn);

  $module1 = array('name' => "Test Module 1", "description"=>"This is a short description of a module.", "text"=>"This is longer description<br>with html in it.", "video"=>"cat.mov");
  $reply = r\db("traintracker")->table('module')->insert($module1)->run($conn)->toNative();
  $id = $reply['generated_keys'][0];
  $modq1 = array('moduleID'=>$id, 'text'=>'Is The Cat Cute?');
  $reply = r\db("traintracker")->table('moduleQuestion')->insert($modq1)->run($conn)->toNative();
  $qid = $reply['generated_keys'][0];
  $modq1a1 = array('moduleQuestionID'=>$qid, 'text'=>'Yes it is', 'correct'=>1);
  r\db("traintracker")->table('moduleQuestionAnswer')->insert($modq1a1)->run($conn)->toNative();
  $modq1a2 = array('moduleQuestionID'=>$qid, 'text'=>'No it is not');
  r\db("traintracker")->table('moduleQuestionAnswer')->insert($modq1a2)->run($conn)->toNative();
  $modq1a3 = array('moduleQuestionID'=>$qid, 'text'=>'Cats are never cute on internet videos');
  r\db("traintracker")->table('moduleQuestionAnswer')->insert($modq1a3)->run($conn)->toNative();

}