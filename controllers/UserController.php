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

    function wins($params) {
      Controller::respond(['consecutive_wins' => $this->user->consecutive_wins()]);
    }

    function teams($params) {
      $this->user->teams();
    }

    function list() {
      echo User::all()->map(function($user) { return ['id' => $user->id, 'username' => $user->username, 'points' => $user->points, 'bounty' => $user->bounty()]; } )->json();
    }


  }

?>
