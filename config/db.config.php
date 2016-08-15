<?php
  
  $conf = Spyc::YAMLLoad(".empathy.yaml");
  $env = "development";
  
  $GLOBALS["MYSQL"] = mysqli_connect(
    $conf['db'][$env]["host"],
    $conf['db'][$env]["username"],
    $conf['db'][$env]["password"],
    $conf['db'][$env]["database"]
  ) or die ("Error establishing a connection to the database");

?>