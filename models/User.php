<?php
  /**
   * User - Description 
   * extended description
   **/
  class User extends Model {

    //database table for this model
    static $table = "user";

    function __construct($data = []) {
      $options = ['id', 'created_at', 'modified_at', 'username', 'points'];
      foreach($options as $key) {
        $data[$key] = isset($data[$key]) ? $data[$key] : null;
      }

      $this->id = $data['id'];
      $this->created_at = $data['created_at'];
      $this->modified_at = $data['modified_at'];
      $this->username = $data['username'];
      $this->points = $data['points'];

      if ($this->points == "") {
        $this->points = 1200;
      }

      $this->validate_presence_of(['username']);
      return $this;
    }

    function history() {
      $history = SinglesHistory::where(['winner' => $this->id, 'loser' => $this->id], "OR");
      return $history === null ? [] : $history;
    }

    function wins() {
      $wins = SinglesHistory::where(['winner' => $this->id]);
      return $wins == null ? [] : $wins->to_array();
    }

    function losses() {
      $losses = SinglesHistory::where(['loser' => $this->id]);
      return $losses == null ? [] : $losses->to_array();
    }

    function teams() {
      return Team::where(['captain' => $this->id, 'mate' => $this->id], "OR");
    }

    function expected(User $opponent) {
      return 1/(1 + pow(10, ($opponent->points - $this->points)/400));
    }

    function adjustMMR(User $opponent, bool $win) {
      $adjustment = $win ? 1 : 0;
      $diff = $this->points - $opponent->points;
      $adjusted = $this->points + MMR::$K * ($adjustment - $this->expected($opponent));
      return round($adjusted);
    }

    function consecutive_wins() {
      $games = SinglesHistory::where(['winner' => $this->id, 'loser' => $this->id], "OR");
      if ($games === null) {
        return 0;
      }
      $consecutive = 0;
      $stop = false;
      $games->reverse()->each(function($game) use (&$consecutive, &$stop){
        if ($game->winner != $this->id || !$stop) {
          $stop = true;
          return;
        } else {
          $consecutive++;
        }
      });
      return $consecutive;
    }

  }

?>