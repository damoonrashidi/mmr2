<?php
  class Resource {

    private $bucket;

    function __construct($bucket){
      $this->bucket = $bucket;
    }

    /**
     * last int -> Resource
     * @param  integer $n - amount of items to limit bucket to (default 1)
     * @return Resource - the last $n items in this bucket]
     */
    function last($n = 1) {
      if($n == 1)
        return $this->bucket[count($this->bucket)-1];
      if($n >= count($this->bucket))
        return new Resource($this->bucket);
      else
        return new Resource(array_slice($this->bucket), -$n, count($this->bucket)-1);
    }

    /**
     * first int -> Resour
     * @param  integer $n amount of items to fetch (default 1)
     * @return Resource - The first $n items in this bucket as a Resource
     */
    function first($n = 1){
      if($n == 1)
        return $this->bucket[0];
      if($n >= count($this->bucket))
        return new Resource($this->bucket);
      else
        return new Resource(array_slice($this->bucket), 0, $n);
    }

    /**
    * [count description]
    * @return [type] [description]
    */
    function count() {
      return count($this->bucket);
    }
    
    /**
     * map fn -> Resource
     * @param  function $fn function to apply to items
     * @return Resource - Resource with all items in this bucket but with $fn applied to them
     */
    function map($fn) {
      $len = count($this->bucket);
      for($i = 0; $i < $len; $i++) {
        $this->bucket[$i] = $fn($this->bucket[$i]);
      }
      return $this;
    }

    /**
     * reduce fn -> Resource
     * @param  function $fn - function to apply on this bucket to reduce it to a single value
     * @return [type]     [description]
     */
    function reduce($fn) {
      return call_user_func($fn,$this->bucket);
    }

    /**
     * each fn -> ()
     * @param  Function $fn function to run for each item in this bucket
     * @return ()
     */
    function each($fn) {
      for ($i=0; $i < count($this->bucket); $i++) { 
        $fn($this->bucket[$i]);
      }
    }

    /**
     * json () -> String
     * @return String the items in this bucket as json
     */
    function json() {
      return json_encode($this->bucket);
    }

    /**
     * order Class->$property -> Resource
     * @param  Class->$property $predicate property to sort this bucket by
     * @return Resource - this bucket but with the items ordered by $predicate
     */
    function order($predicate) {
      $res = [];
      for($i = 0, $count = count($this->bucket); $i < $count; $i++) {
        $min = $i;
        for($j = 0; $j < $count; $j++) {
          if($this->bucket[$j]->$predicate < $this->bucket[$min]->$predicate) {
            $min = $j;
          }
        }
        $res[] = $this->bucket[$min];
      }
      return new Resource($res);
    }

    /**
     * reverse () -> Resource
     * @return Resource this bucket reversed
     */
    function reverse() {
      return new Resource(array_reverse($this->bucket));
    }

    /**
     * to_array () -> Array
     * @return array - returns this bucket as array
     */
    function to_array() {
      return $this->bucket;
    }

    function delete() {
      for($i = 0; $i < count($this->bucket); $i++) {
        $this->bucket[$i]->delete();
      }
    }


    /**
     * filter: fn -> Resource
     * all items in bucket where $fn($item) == true
     * @param  lambda $fn [description]
     * @return Resource     [description]
     */
    function filter($fn) {
      $matched = [];
      for($i = 0, $count = count($this->bucket); $i < $count; $i++) {
        if ($fn($this->bucket[$i])) {
          $matched[] = $this->bucket[$i];
        }
      }
      return new Resource($matched);
    }

  }

?>