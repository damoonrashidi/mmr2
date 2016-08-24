<?php
  /**
   * Singleshistory - Description 
   * extended description
   **/
  class SinglesHistory extends Model {

    //database table for this model
    static $table = "singleshistory";

    function __construct($data = []) {
      $options = ['id', 'created_at', 'modified_at', 'winner', 'loser', 'delta_winner', 'delta_loser'];
      foreach($options as $key) {
        $data[$key] = isset($data[$key]) ? $data[$key] : "";
      }

      $this->id = $data['id'];
      $this->created_at = $data['created_at'];
      $this->modified_at = $data['modified_at'];
      $this->winner = $data['winner'];
      $this->loser = $data['loser'];
      $this->delta_winner = $data['delta_winner'];
      $this->delta_loser = $data['delta_loser'];

      
      return $this;
    }

  }

?>