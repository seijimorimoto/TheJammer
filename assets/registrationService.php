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
    parse_str(file_get_contents('php://input'), $params);
    $userName = $params['username'];
    $sql = "SELECT username
            FROM Users
            WHERE username='$userName'";
    $selectResult = $connection->query($sql);

    if ($selectResult->num_rows > 0) {
      header("HTTP/1.1 409 Conflict: Username already in use. Please select another one");
      die("User already exists");
    } else {
      $password = $params['password'];
      $firstName = $params['firstName'];
      $lastName = $params['lastName'];
      $email = $params['email'];
      $gender = $params['gender'];
      $country = $params['country'];
      $sql = "INSERT INTO Users (username, passwd, firstName, lastName, email, gender, country)
              VALUES ('$userName', '$password', '$firstName', '$lastName', '$email', '$gender', '$country')";
      
      if(mysqli_query($connection, $sql)) {
        session_start();
        
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['username'] = $username;

        $response = array('status' => 'success');
        echo json_encode($response);
      } else {
        header('HTTP/1.1 500 Bad connection, something went wrong while saving your data,
                please try again later');
        die('There was a bad connection');
      }
    }
  }

  $connection->close();
?>