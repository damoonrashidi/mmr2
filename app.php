<?php

  $res = substr($_SERVER['REQUEST_URI'],1);

  if(file_exists($res)){
    $mimes = [
      'css' => 'text/css',
      'js'  => 'application/javascript',
      'png' => 'image/png',
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'gif' => 'image/gif',
      'bmp' => 'image/bmp',
      'webp' => 'image/webp',
      'webm' => 'image/webm',
      'pdf' => 'application/pdf',
      'html' => 'text/html',
      'svg' => 'image/svg+xml'
    ];
    $ext = explode(".", $res)[count(explode(".", $res))-1];
    header("Content-Type: ".$mimes[$ext]);
    echo file_get_contents($res);
    exit;
  }

  ob_start();
  ob_clean();
  
  require_once "lib/loader.php";
  require_once "config/routes.config.php";
