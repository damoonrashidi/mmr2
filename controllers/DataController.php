<?php

  class DataController extends Controller{

    function __construct(){
      $this->layout = "";
    }

    function __before($params, $action) {
      header("Access-Control-Allow-Origin: *");
    }

    function bar() {
      $ret = [];
      for ($i = 0; $i < 200; $i++) {
        $ret[] = mt_rand() % 500;
      }
      Controller::respond($ret);
    }

    function line() {
      Controller::respond([1,5,1,3,4,9,9,3,1,2,4]);
    }

    function area() {

    }


  }

?>
