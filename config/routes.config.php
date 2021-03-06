<?php
  
  $router = new Router();

  //API
  /**
   * POST user/register
   * params username=$username
   * PRE-CONDITION: $username 
   * RESULT: 200 if user is created without errors
   *         412 if user already exists
   *         500 if something goes wrong
   */
  $router->post('user/register', 'user#register');
  $router->get('user/history/:username', 'user#history');
  $router->get('user/teams', 'user#teams');
  $router->get('user/list', 'user#list');
  $router->get('user/wins/:username', 'user#wins');
  $router->get('user/profile/:username', "user#profile");

  /**
   * POST user/register
   * params winner=$winner (username of the winning player) loser=$winner (username of the losing player)
   * PRE-CONDITION: $winner and $loser are existing users
   * RESULT: 200 if the result is reported without errors
   *         412 if either $winner or $loser does not exists
   *         500 if something goes wrong
   */
  $router->post('singles/register', 'singles#register');
  $router->get('singles/history', 'singles#history');
  $router->get('singles/simulate/:p1/:p2', 'singles#simulate');

  $router->get('help', function() {
    header("Content-Type: application/json");
    echo json_encode([
      'GET' => [
        'user/history/:username' => 'list of games for :username',
        'user/teams/:username' => 'list of teams :username belongs to',
        'user/list' => 'list of all users and their points',
        'user/wins/:username' => 'number of consecutive wins for :username',
        'user/profile/:username' => 'information about :username'
      ],
      'POST' => [
        'user/register' => ['username' => 'string'],
        'singles/register' => ['winner' => 'string', 'loser' => 'string']
      ]
    ]);
  });

  $router->get('data/bar', 'data#bar');


  /**
   * The web interface part
   */
  $router->index("app#index");

  $router->run();
?>