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
  # - $username: String representing the username to be validated.
  # - $password: String representing the password of the username to be validated.
  # Return: Array with a status of the result of the validation and a response (in case it was
  # successful) or an error code.
  function attemptLogin($username, $password) {
    $conn = connect();

    if ($conn != null) {
      $sql = "SELECT firstName, lastName, profilePicture
              FROM Users
              WHERE username = ? AND passwd = ?";
      $stmt = $conn->prepare($sql);

      $stmt->bind_param('ss', $username, $password);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $firstName = $row['firstName'];
          $lastName = $row['lastName'];
          $profilePicture = $row['profilePicture'];
        }

        $response = array('firstName' => $firstName, 'lastName' => $lastName,
                             'profilePicture' => $profilePicture, 'message' => 'Successful login');

        $stmt->close();
        $conn->close();
        return array('status' => 'SUCCESS', 'response' => $response);
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

  # Attempts the registration of a user.
  # Parameters:
  # - $username: String representing the username of the user to be registered.
  # - $password: String representing the password of the user to be registered.
  # - $firstName: String representing the first name of the user to be registered.
  # - $lastName: String representing the last name of the user to be registered.
  # - $email: String representing the email of the user to be registered.
  # - $gender: Single character string representing the gender of the user to be registered (M/F).
  # - $country: String representing the country code of the user to be registered.
  # - $profilePicture: String representing the path to the profile pic of the user to be registered.
  # Return: Array with a status of the result of the operation and a response (in case it was
  # successful) or an error code.
  function attemptRegistration($username, $password, $firstName, $lastName, $email, $gender,
    $country, $profilePicture) {
    $conn = connect();

    if ($conn != null) {
      $sql = "SELECT username FROM Users WHERE username = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows == 0) {
        $stmt->close();
        $sql = "INSERT INTO Users
                  (username, passwd, firstName, lastName, email, gender, profilePicture, country)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssss', $username, $password, $firstName, $lastName, $email, $gender,
                          $profilePicture, $country);
        
        if ($stmt->execute()) {
          $stmt->close();
          $conn->close();
          return array('status' => 'SUCCESS', 'response' => 'Successful user registration');
        } else {
          $stmt->close();
          $conn->close();
          return array('status' => 'INTERNAL_SERVER_ERROR', 'code' => 500);
        }
      }

      else {
        $stmt->close();
        $conn->close();
        return array('status' => 'CONFLICT', 'code' => 409);
      }
    }

    else {
      return array('status' => 'INTERNAL_SERVER_ERROR', 'code' => 500);
    }
  }

  # Retrieves the profile information of a given user.
  # Parameters:
  # - $username: String representing the username whose profile is to be retrieved.
  # Return: Array with a status of the result of the operation and a response with the profile
  # information (in case it was successful) or an error code.
  function retrieveProfile($username) {
    $conn = connect();

    if ($conn != null) {
      $sql = "SELECT
                username, CONCAT(firstName, ' ', lastName) AS completeName, email, gender, country,
                profilePicture
              FROM Users
              WHERE username = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        $response = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return array('status' => 'SUCCESS', 'response' => $response);
      } else {
        $stmt->close();
        $conn->close();
        return array('status' => 'NOT_FOUND', 'code' => 406);
      }
    }

    else {
      return array('status' => 'INTERNAL_SERVER_ERROR', 'code' => 500);
    }
  }

  # Retrieves the comments of a given user and all of its friends.
  # Parameters:
  # - $username: String representing the username whose comments (along with those of his friends)
  #   are to be retrieved.
  # Return: Array with a status of the result of the operation and a response with the comments (in
  # case it was successful) or an error code.
  function retrieveComments($username) {
    $conn = connect();

    if ($conn != null) {
      $sql = "SELECT
                C.content, C.username, C.commentDate AS date,
                CONCAT(U.firstName, ' ', U.lastName) AS completeName, U.profilePicture 
              FROM Comments C JOIN Users U ON C.username = U.username
              WHERE C.username IN
              (SELECT DISTINCT(username2)
               FROM Followers
               WHERE username1 = ?)
              OR C.username = ?
              ORDER BY commentDate DESC";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('ss', $username, $username);
      $stmt->execute();
      $result = $stmt->get_result();

      $response = $result->fetch_all(MYSQLI_ASSOC);
      $stmt->close();
      $conn->close();
      return array('status' => 'SUCCESS', 'response' => $response);
    }

    else {
      return array('status' => 'INTERNAL_SERVER_ERROR', 'code' => 500);
    }
  }

  # Inserts a comment in the Database.
  # Parameters:
  # - $username: String representing the username who is posting the comment.
  # - $content: String representing the content of the comment to be inserted.
  # - $commentDate: String representing the date (in 'YYYY-MM-DD HH:mm:SS' format) when the comment
  #   was posted.
  # - $repliedCommentId: Integer representing the id of the comment who is receiving an answer with
  #   the comment to be inserted. If this last one is a standalone comment, then repliedCommentId
  #   should be null.
  # Return: Array with a status of the result of the operation and a response (in case it was
  # successful) or an error code.
  function insertComment($username, $content, $commentDate, $repliedCommentId) {
    $conn = connect();

    if ($conn != null) {
      $stmt;
      if ($repliedCommentId != null) {
        $sql = "INSERT INTO Comments (content, username, repliedCommentId, commentDate)
                VALUES (?, ?, ?, STR_TO_DATE(?, '%Y-%m-%d %H:%i:%S'))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssis', $content, $username, $repliedCommentId, $commentDate);
      } else {
        $sql = "INSERT INTO Comments (content, username, commentDate)
                VALUES (?, ?, STR_TO_DATE(?, '%Y-%m-%d %H:%i:%S'))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $content, $username, $commentDate);
      }

      if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return array('status' => 'SUCCESS', 'response' => 'Successful comment insertion');
      } else {
        $stmt->close();
        $conn->close();
        return array('status' => 'INTERNAL_SERVER_ERROR', 'code' => 500);
      }
    }

    else {
      return array('status' => 'INTERNAL_SERVER_ERROR', 'code' => 500);
    }
  }

?>