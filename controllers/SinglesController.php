<?php

  class SinglesController extends Controller{

    function __construct(){
      $this->layout = "";
    }

    function __before($params, $action) {
      header("Content-Type: application/json");
    }

    function register($params) {
      $winner = User::where(['username' => $params->winner]);
      $loser = User::where(['username' => $params->loser]);

      if ($winner === null || $loser === null) {
        Controller::respond(['success' => false, 'status_code' => 412, 'message' => 'One (or both) of the users does not exist']);
        return;
      }

      $winner = $winner->first();
      $loser = $loser->first();
      
      $winner_mmr = $winner->adjustMMR($loser, true);
      $loser_mmr = $loser->adjustMMR($winner, false);
      
      $winner->points = $winner_mmr;
      $loser->points = $loser_mmr;
      
      $winner->save();
      $loser->save();

      $s = new SinglesHistory([
        'winner' => $winner->id,
        'loser' => $loser->id
      ]);
      $s->save();
    }

    function history() {
      echo SinglesHistory::all()->reverse()->json();
    }

    function simulate($params) {
      $p1 = User::where(['username' => $params->p1]);
      $p2 = User::where(['username' => $params->p2]);
      if ($p1 == null || $p2 == null) {
        Controller::respond(['success' => false, 'status_code' => 412, 'message' => 'Could not found one of the users']);
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


  }

?>
