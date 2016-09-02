<?php

  class AppController extends Controller{

    function __construct(){
      $this->layout = "app";
    }

    function index() {
      Controller::render_layout("app#index");
    }


  }

?>
