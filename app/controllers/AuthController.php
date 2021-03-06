<?php namespace Controllers;

use App, Request, DB, Exception;

class AuthController extends BaseController {

  public function register() {
    // Get the user
    $user = json_decode(Request::getBody(), true);
    // Hash the password
    $password = sha1($user['password']);

    if(!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
      $code = 422;
      $response = ['msg' => 'Email must be a valid email address'];
    // Make sure the passwords match
    } else if($user['password'] != $user['passwordConfirm']) {

      // If not, return a validation error
      $code = 422;
      $response = ['msg' => 'Passwords must match'];
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
          $user['id'] = DB::table('user')->insert($user)->run()->toNative();
          $code = 200;
          unset($user['password']);
          $response = ['msg' => 'User created', 'data' => $user];
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
  }
}