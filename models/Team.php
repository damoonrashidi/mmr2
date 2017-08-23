<?php
/**
 * Team - Description
 * extended description
 **/

class Team extends Model {

  //database table for this model
  static $table = "team";
  static $has_one = [
    'captain' => 'User',
    'mate' => 'User',
  ];
  static $validate = [
    'points' => 'Validate::positive',
  ];

  public $id = null;
  public $created_at = null;
  public $modified_at = null;
  public $name = null;
  public $points = null;
  public $captain = null;
  public $mate = null;

  public function __construct($data = []) {
    foreach ($data as $k => $v) {
      $this->$k = $v;
    }

    return $this;
  }

}
