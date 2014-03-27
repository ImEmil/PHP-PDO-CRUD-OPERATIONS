<?php
require_once("path/class.pdo_crud.php");
$db = new PDO_CRUD("config.ini.php");


/*
* #SELECT QUERY
*/
  $select = $db->query("SELECT * FROM users");

  foreach($select as $user_info)
  {
    echo $user_info['username'];
  }
