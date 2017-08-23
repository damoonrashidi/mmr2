<?php
/**
 * Model - Persitant Object Storage and Manipulation
 */
abstract class Model {

  public function __construct() {
    return $this;
  }

  public function save() {

    if (isset(static::$validate)) {
      $this->validate();
    }

    if (isset(static::$has_one)) {
      $this->checkRelations();
    }

    $ar = new ArrayManip();
    $this->created_at = date("Y-m-d H:i:s");
    $this->modified_at = date("Y-m-d H:i:s");
    if ($this->id !== null && $this->id !== "") {
      return $this->update();
    }
    $rows = $ar->listify(array_keys($ar->without($this, ['id'])));
    $values = $ar->listify(array_values($ar->without($this, ['id'])), "'");
    $q = "INSERT INTO " . static::$table . " ($rows) VALUES($values)";
    $tstart = microtime(true);
    $create = $GLOBALS['MYSQL']->query($q);
    $time = round(microtime(true) - $tstart, 5) . "s";
    fwrite(STDOUT, "QUERY: $q ($time)");
    if ($create !== false) {
      $this->id = $GLOBALS['MYSQL']->insert_id;
      return $GLOBALS['MYSQL']->insert_id;
    } else {
      return false;
    }

  }
  /*
   * Function: where($data = array, $inclusive = bool) -> array
   * PRE: every key in $data must match a column in the table for this model
   * POST: every object that matches $key = $value. if it's only one object (for instance when runnning where id = 1)
   *       it returns that object as an array, otherwise it returns an array of arrays
   * SIDE-EFFECTS: queries the database
   * EXAMPLE: $UserObject->where(array('email' => 'joe@example.net')) #=> array('id' => 1, 'email' => 'joe@example.net', 'firstname' => 'Joe', 'lastname' => 'Poe', 'age' => 20)
   * Example 2: $UserObject->where(array('age' => '20')); #=> array(array(user1, user2,...))
   */
  public static function where($data, $inclusion = "AND") {
    $set = [];
    $data = ArrayManip::chain($data, $inclusion);
    $q = "SELECT * FROM " . static::$table . " WHERE " . $data;
    $tstart = microtime(true);
    $result = $GLOBALS['MYSQL']->query($q);
    $time = round(microtime(true) - $tstart, 5) . "s";
    fwrite(STDOUT, "QUERY: $q ($time)\n");
    fwrite(STDOUT, "RESULT: " . $result->num_rows . " rows\n");
    while ($row = $result->fetch_assoc()) {
      $obj = static::createRelationalObject($row);
      array_push($set, $obj);
    }
    if (count($set) == 0) {
      return null;
    }
    return new Resource($set);
  }

  /**
   * [find description]
   * @param  int    $id [id of the record to find]
   * @return Object corresponding object in the table for this model with the id = $id or null
   */
  public static function find(int $id) {
    if (!is_numeric($id)) {
      return null;
    }

    $set = [];
    $q = "SELECT * FROM " . static::$table . " WHERE id = $id";
    $tstart = microtime(true);
    $results = $GLOBALS['MYSQL']->query($q);
    $time = round(microtime(true) - $tstart, 5) . "s";
    fwrite(STDOUT, "QUERY: $q ($time)\n");
    if ($results->num_rows == 0) {
      return null;
    }
    return static::createRelationalObject($results->fetch_assoc());
  }

  /*
   * Function: wildcard($data = array) -> array
   * PRE: every key in $data must match a column in the table for this model
   * POST: every object that matches $key LIKE %$value%., An array of objects matching
   *       the query
   * SIDE-EFFECTS: queries the database
   * EXAMPLE: $find = Article::wildcard(['title' => 'Welc']) #=> ArticleObject->['title' => 'welcome to my blog', 'body' => 'stuff here..',...]
   */
  public static function wildcard(array $data) {
    $set = [];
    $data = ArrayManip::wildcard($data, '%', '%', 'OR');
    $q = "SELECT * FROM " . static::$table . " WHERE " . $data;
    $tstart = microtime(true);
    $result = $GLOBALS['MYSQL']->query($q);
    $time = round(microtime(true) - $tstart, 5) . "s";
    fwrite(STDOUT, "QUERY: $q ($time)\n");
    fwrite(STDOUT, "RESULT: " . $result->num_rows . " rows\n");
    while ($row = $result->fetch_assoc()) {
      array_push($set, new static($row));
    }
    if (count($set) == 0) {
      return null;
    }
    return new Resource($set);

  }

  /*
   * Function: order([predicate, direction limit]) -> array
   * PRE: every key in $parameters must match a column in the table for this model
   * POST: all objects of this model ordered by $fields in $direction (ascending|descending) order
   * SIDE-EFFECTS: queries the database
   * EXAMPLE: User::order(['predicate' => ['age'], 'direction' => 'ASC']) #=> [user1, user2, user3, ... ]
   * @params array $parameters
   */
  public static function order(array $parameters = []) {
    $limit = (isset($parameters['limit'])) ? "LIMIT " . $parameters['limit'] : '';
    $offset = (isset($parameters['offset'])) ? "OFFSET " . $parameters['offset'] : '';
    $parameters['predicate'] = ArrayManip::listify($parameters['predicate']);
    $parameters['direction'] = (isset($parameters['limit'])) ? $parameters['direction'] : 'ASC';
    $q = "SELECT * FROM " . static::$table . " ORDER BY " . $parameters['predicate'] . " " . $parameters['direction'] . " $limit $offset";
    $tstart = microtime(true);
    $result = $GLOBALS['MYSQL']->query($q);
    $time = round(microtime(true) - $tstart, 5) . "s";
    fwrite(STDOUT, "QUERY: $q ($time)\n");
    fwrite(STDOUT, "RESULT: " . $result->num_rows . " rows\n");
    $set = [];
    while ($row = $result->fetch_assoc()) {
      array_push($set, static::createRelationalObject($row));
    }
    return new Resource($set);
  }

  private function update() {
    $this->modified_at = date("Y-m-d H:i:s");
    $data = ArrayManip::chain($this, ", ");
    $q = "UPDATE " . static::$table . " SET " . $data . " WHERE id = " . $this->id;
    $tstart = microtime(true);
    $update = $GLOBALS['MYSQL']->query($q);
    $time = round(microtime(true) - $tstart, 5) . "s";
    fwrite(STDOUT, "QUERY: $q ($time)\n");
    fwrite(STDOUT, $GLOBALS['MYSQL']->info . "\n");
    return $update !== false;
  }

  /**
   * all - all items for this model
   * @return Resource - all items for this model
   */
  public static function all() {
    $set = [];
    $q = "SELECT * FROM " . static::$table;
    $tstart = microtime(true);
    $result = $GLOBALS['MYSQL']->query($q);
    while ($row = $result->fetch_assoc()) {
      array_push($set, static::createRelationalObject($row));
    }
    $time = round(microtime(true) - $tstart, 5) . "s";
    fwrite(STDOUT, "QUERY: $q ($time)\n");
    fwrite(STDOUT, "RESULT: " . $result->num_rows . " rows\n");
    return new Resource($set);
  }

  /**
   * [delete - delete the data source for this object in the database]
   * @return bool - true if the resource was deleted properly, otherwise false
   */
  public function delete() {
    $delete = $GLOBALS['MYSQL']->query("DELETE FROM " . static::$table . " WHERE id = " . $this->id);
    return $delete !== false;
  }

  /**
   * [json - this model as a json object]
   * @return string
   */
  public function json() {
    return json_encode($this);
  }

  private function validate() {
    foreach (static::$validate as $field => $rule) {
      if ((is_callable($rule) && !$rule($this->$field))) {
        throw new Exception("$field does not satisfy $rule");
      }
      if ((!is_callable($rule) && !preg_match($rule, $this->$field))) {
        throw new Exception("$field does not satisfy $rule");
      }
    }
  }

  private static function createRelationalObject($data) {
    $obj = new static($data);
    if (isset(static::$has_one)) {
      foreach (static::$has_one as $k => $v) {
        $obj->$k = $v::find($obj->$k);
      }
    }
    return $obj;
  }

  private function checkRelations() {
    if (isset(static::$has_one)) {
      foreach (static::$has_one as $field => $model) {
        if (gettype($this->$field) !== 'object') {
          throw new Exception("Could not satisfy relation $field <=> $model");
        }
        $object = $model::find($this->$field->id);
        if (get_class($this->$field) !== $model || $object->id === null) {
          throw new Exception("Could not satisfy relation $field <=> $model");
        }
      }
    }

    if (isset(static::$belongs_to)) {
      foreach (static::$belongs_to as $field => $model) {
        $object = new $model;
        if ($this->$field === null || $model->find($this->$field) === null) {
          throw new Exception("Could not find record in $model to which this " . static::$table . " belongs");
        }
      }
    }
  }

}
