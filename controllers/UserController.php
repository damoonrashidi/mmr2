<?php

  class UserController extends Controller{

    function __construct(){
      $this->layout = "";
    }

    function __before($params, $action) {
      $found_user = true;
      if(isset($params->username)) {
        $found_user = User::where(['username' => $params->username]) !== null;
      } else if (isset($params->id)) {
        $found_user = User::find($params->id) !== null;
      }

      if (!$found_user) {
        Controller::respond(['success' => false, 'status_code' => 412, 'message' => 'Could not find user']);
        exit;
      }
    }

    function register($params) {
      if (User::where(['username' => $params->username]) === null) {
        $user = new User(['username' => $params->username]);
        $save = $user->save();
        if ($save) {
          Controller::respond(['message' => 'User created successfully']);
        }
      }
      else {
        Controller::respond(['success' => false, 'message' => 'User already exists', 'status_code' => 412]);
      }
    }

    function profile($params) {
      $user = User::where(['username' => $params->username])->first();
      Controller::respond([
        'username' => $user->username,
        'points' => $user->points,
        'consecutive wins' => $user->consecutive_wins(),
        'games' => $user->history()->to_array(),
        'losses' => $user->losses(),
        'wins' => $user->wins()
      ]);
    }

    function history($params) {
      $user = User::where(['username' => $params->username])->first();
      echo $user->history()->json();
    }

    function simulate($params) {
      $p1 = User::where(['username' => $params->p1]);
      $p2 = User::where(['username' => $params->p2]);
      if ($p1 == null || $p2 == null) {
        Controller::respond(['success' => false, status_code => 412, 'message' => 'Could not found one of the users']);
        return;
      }
      $p1 = $p1->first();
      $p2 = $p2->first();
      Controller::respond([
        $p1->username." wins" => [
          $p1->username => $p1->adjustMMR($p2, true),
          $p2->username => $p2->adjustMMR($p1, false),
        ],
        $p2->username." wins" => [
          $p1->username => $p1->adjustMMR($p2, false),
          $p2->username => $p2->adjustMMR($p1, true),
        ]
      ]);
    }

    function wins($params) {
      $wins = User::where(['username' => $params->username])->first()->wins();
      Controller::respond(['wins' => $wins]);
    }


  }

?>
