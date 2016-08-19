<?php

  class SinglesController extends Controller{

    function __construct(){
      $this->layout = "";
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


  }

?>
