<?php
/**
 * Singleshistory - Description
 * extended description
 **/
class SinglesHistory extends Model {

  //database table for this model
  static $table = "singleshistory";
  static $has_one = [
    'winner' => 'User',
    'loser' => 'User',
  ];
  static $validate = [
    'delta_winner' => 'Validate::positive',
    'delta_loser' => 'Validate::negative',
  ];

  public $id = null;
  public $created_at = null;
  public $modified_at = null;
  public $winner = null;
  public $loser = null;
  public $delta_winner = null;
  public $delta_loser = null;

  public function __construct($data = []) {
    foreach ($data as $key => $val) {
      $this->$key = $val;
    }

    return $this;
  }

}
