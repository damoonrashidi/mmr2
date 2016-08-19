<?php
  
  $router = new Router();

  //API
  $router->post('user/register', 'user#register');
  $router->get('user/history/:username', 'user#history');
  $router->get('user/teams', 'user#teams');
  $router->get('user/wins/:username', 'user#wins');
  $router->get('user/profile/:username', "user#profile");

  $router->get('singles/history', 'singles#history');
  $router->post('singles/register', 'singles#register');

  $router->get('user/simulate/:p1/:p2', 'user#simulate');

  $router->run();
?>