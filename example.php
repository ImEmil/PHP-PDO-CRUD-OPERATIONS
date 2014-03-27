<?php
  require_once("path/class.pdo_crud.php");
  $db = new PDO_CRUD("config.ini.php"); // Instance with the config file [Db connections, drivers]


/*
* #Fetch from table
*/
  $select = $db->query("SELECT * FROM users");

  foreach($select as $user_info)
  {
    echo $user_info['username'];
  }
  
/*
* #Fetch from table WHERE
*/
# V1
  $selectW = $db->query("SELECT * FROM users WHERE rank = :rank AND id = :id", ["rank" => "1", "id" => "1"] );

# V2
  $db->bind("rank", "1");
  $db->bind("id", "1");
  $selectW = $db->query("SELECT * FROM users WHERE rank = :rank AND id = :id");
  
  
# V3
  $db->bindAll( ["rank" => "1", "id" => "1"] );
  $selectW = $db->query("SELECT * FROM users WHERE rank = :rank AND id = :id");
  
  
  foreach($selectW as $userW)
  {
    echo $userW['username'];
  }  
  
/*
* #Update table AND delete AND insert AND count
*/

# Insert
$insert   =  $db->query("INSERT INTO users (username,rank) VALUES(:name, :rank)", ["name" => "Emil", "rank" => "1"] );

# Update
$update   =  $db->query("UPDATE users SET rank = :newRank WHERE username = :name", ["newRank" => "2", "name" => "Emil"] );

# Delete
$delete   =  $db->query("DELETE FROM users WHERE username = :name", ["name" => "Emil"] );

/*
* #Rows & columns
*/

# Column
$column = $db->column("SELECT username FROM users");
print_r($column);

# Single
$firstUser = $db->single("SELECT username FROM users WHERE username = :name", ["name" => "Emil"] );

# Count
$db->bind("rank", "1");
$how_many = $db->query("SELECT COUNT(`id`) FROM users WHERE rank = :rank");
echo $how_many;

