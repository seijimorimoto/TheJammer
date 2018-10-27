<?php

  # Connects to the database of the web application.
  # Return: The connection object if the connection was successful. Otherwise, null.
  function connect() {
    $serverName = 'localhost';
    $serverUserName = 'root';
    $serverPassword = '';
    $databaseName = 'TheJammerA01281380';

    $connection = new mysqli($serverName, $serverUserName, $serverPassword, $databaseName);

    if ($connection->connect_error) {
      return null;
    } else {
      return $connection;
    }
  }

  # Attempts a login, validating the credentials received.
  # Parameters:
  # - $username: Username to be validated.
  # - $password: Password of username to be validated.
  # Return: Array with a status of the result of the validation and a response (in case it was
  # successful) or an error code.
  function attemptLogin($username, $password) {
    $conn = connect();

    if ($conn != null) {
      $stmt = $conn->prepare("SELECT firstName, lastName, profilePicture
                              FROM Users
                              WHERE username = ? AND passwd = ?");

      $stmt->bind_param('ss', $username, $password);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        session_start();

        while ($row = $result->fetch_assoc()) {
          $_SESSION['firstName'] = $row['firstName'];
          $_SESSION['lastName'] = $row['lastName'];
          $_SESSION['username'] = $username;
          $_SESSION['profilePicture'] = $row['profilePicture'];
        }

        $stmt->close();
        $conn->close();
        return array('status' => 'SUCCESS', 'response' => 'Successful login');
      }
      
      else {
        $stmt->close();
        $conn->close();
        return array('status' => 'NOT_FOUND', 'code' => 406);
      }

      $stmt->close();
    }

    else {
      return array('status' => 'INTERNAL_SERVER_ERROR', 'code' => 500);
    }
  }

?>