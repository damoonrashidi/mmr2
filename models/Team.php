<?php
  /**
   * Team - Description 
   * extended description
   **/
  class Team extends Model {

    //database table for this model
    static $table = "team";

    function __construct($data = []) {
      $options = ['id', 'created_at', 'modified_at', 'name', 'points', 'captain', 'mate'];
      foreach($options as $key) {
        $data[$key] = isset($data[$key]) ? $data[$key] : null;
      }

      $this->id = $data['id'];
      $this->created_at = $data['created_at'];
      $this->modified_at = $data['modified_at'];
      $this->name = $data['name'];
      $this->points = $data['points'];
      $this->captain = $data['captain'];
      $this->mate = $data['mate'];

      
      return $this;
    }

  }

?>