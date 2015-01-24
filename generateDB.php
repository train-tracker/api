<?php

ini_set('display_errors', 1);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

$config = require __DIR__.'/app/configuration.php';

$conn = r\connect($config['RETHINK_HOST']);
$conn->useDb($config['RETHINK_DATABASE']);

switch($argv[1]){
  case ('db'):
    try{
      r\dbCreate("traintracker")->run($conn);
    }catch(Exception $e){}
    break;
  case ('users'):
    // Create a test table

    try{
      r\db("traintracker")->tableDrop("user")->run($conn);
    }catch(Exception $e){}

    r\db("traintracker")->tableCreate("user")->run($conn);

    $spass = password_hash('1234', PASSWORD_BCRYPT);

    $user1 = array('uname' => "testuser", "password" => $spass, "email"=> "test@user.com");
    $user2 = array('uname' => "testuser2", "password" => $spass, "email"=> "test@user2.com");

    r\db("traintracker")->table('user')->insert($user1)->run($conn);
    r\db("traintracker")->table('user')->insert($user2)->run($conn);

    break;
}