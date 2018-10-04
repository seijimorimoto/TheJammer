<?php
  header('Content-type: application/json');
  header('Accept: application/json');

  $serverName = 'localhost';
  $serverUserName = 'root';
  $serverPassword = '';
  $databaseName = 'TheJammerA01281380';

  $connection = new mysqli($serverName, $serverUserName, $serverPassword, $databaseName);

  if ($connection->connect_error) {
    header('HTTP/1.1 500 Bad connection, portal is down');
    die("The server is down, we couldn't retrieve data from the database");
  } else {
    $userName = $_GET['username'];
    $password = $_GET['password'];
    $sql = "SELECT firstName, lastName
            FROM Users
            WHERE username='$userName' AND passwd='$password'";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $response = array("firstName" => $row["firstName"], "lastName" => $row["lastName"]);
      }
      echo json_encode($response);
    } else {
      header('HTTP/1.1 406 User not found');
      die('Wrong credentials provided');
    }
  }
?>