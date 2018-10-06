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
    $username = $_GET['username'];
    $sql = "SELECT
              username, CONCAT(firstName, ' ', lastName) AS completeName, email, gender, country,
              profilePicture
            FROM Users
            WHERE username = '$username'";
    
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
      $response = array('profile' => array());
      while ($row = $result->fetch_assoc()) {
        $response['profile'][] = array(
          'username' => $row['username'],
          'completeName' => $row['completeName'],
          'email' => $row['email'],
          'gender' => $row['gender'],
          'country' => $row['country'],
          'profilePicture' => $row['profilePicture']
        );
      }
      echo json_encode($response);
    } else {
      header('HTTP/1.1 406 User not found');
      die('The username does not match any registered user');
    }
  }

  $connection->close();
?>