<?php

  class UserController extends Controller{

    function __construct(){
      $this->layout = "";
    }

    function __before($params, $action) {
      header("Content-Type: application/json");
      if ($action == 'register') {
        return;
      }
      $found_user = true;
      if(isset($params->username)) {
        $found_user = User::where(['username' => $params->username]);
        if ($found_user !== null) {
          $found_user = $found_user->first();
        }
      } else if (isset($params->id)) {
        $found_user = User::find($params->id);
      }
      if ($found_user === null) {
        Controller::respond(['success' => false, 'status_code' => 412, 'message' => 'Could not find user']);
        exit;
      } else {
        $this->user = $found_user;
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
      Controller::respond([
        'username' => $this->user->username,
        'points' => $this->user->points,
        'consecutive wins' => $this->user->consecutive_wins(),
        'games' => $this->user->history()->to_array(),
        'losses' => $this->user->losses(),
        'wins' => $this->user->wins()
      ]);
    }

    function history($params) {
      echo $this->user->history()->json();
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
      Controller::respond(['consecutive_wins' => $this->user->consecutive_wins()]);
    }

    function teams($params) {
      $this->user->teams();
    }


  }

?>
