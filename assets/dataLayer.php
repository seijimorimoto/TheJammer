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