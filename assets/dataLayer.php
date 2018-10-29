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
              (SELECT DISTINCT(username2) FROM Friends WHERE username1 = ? AND requestAccepted = 1
               UNION
               SELECT DISTINCT(username1) FROM Friends WHERE username2 = ? AND requestAccepted = 1)
              OR C.username = ?
              ORDER BY commentDate DESC";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('sss', $username, $username, $username);
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

  # Retrieves the username, completeName and profilePicture of all users that are not friends of the
  # current user (and that doesn't have already a friend request from him) and whose username,
  # firstName or lastName match a given pattern.
  # Parameters:
  # - $username: String representing the username of the current user.
  # - $pattern: String representing the search pattern.
  # Return: Array with a status of the result of the operation and a response with the information
  # of the users (in case it was successful) or an error code.
  function findNewFriends($username, $pattern) {
    $conn = connect();

    if ($conn != null) {
      $sql = "SELECT username, CONCAT(firstName, ' ', lastName) AS completeName, profilePicture
              FROM Users
              WHERE username NOT IN
              (SELECT username2 FROM Friends WHERE username1 = ?
               UNION
               SELECT username1 FROM Friends WHERE username2 = ?)
              AND (username LIKE ? OR firstName LIKE ? OR lastName LIKE ?)
              AND username != ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('ssssss', $username, $username, $pattern, $pattern, $pattern, $username);
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

  # Adds a friendship relationship between two users, but this friendship is only truly established
  # until the $username2 accepts the friend request.
  # Parameters:
  # - $username1: String representing the username of the user who sent the friend request.
  # - $username2: String representing the username of the user who will receive the friend request.
  # Return: Array with a status of the result of the operation and a response (in case it was
  # successful) or an error code.
  function addFriend($username1, $username2) {
    $conn = connect();

    if ($conn != null) {
      $sql = "INSERT INTO Friends (username1, username2, requestAccepted) VALUES (?, ?, 0)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('ss', $username1, $username2);
      
      if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return array('status' => 'SUCCESS', 'response' => 'Successfully sent friend request');
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

  # Retrieves the username, completeName and profilePicture of all users that sent a friend request
  # to the current user.
  # Parameters:
  # - $username: String representing the username of the current user.
  # Return: Array with a status of the result of the operation and a response with the information
  # of the users (in case it was successful) or an error code.
  function retrieveIncomingFriendRequests($username) {
    $conn = connect();

    if ($conn != null) {
      $sql = "SELECT username, CONCAT(firstName, ' ', lastName) AS completeName, profilePicture
              FROM Users U JOIN Friends F ON U.username = F.username1
              WHERE F.username2 = ? AND requestAccepted = 0";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('s', $username);
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

  # Retrieves the username, completeName and profilePicture of all users that received a friend
  # request from the current user.
  # Parameters:
  # - $username: String representing the username of the current user.
  # Return: Array with a status of the result of the operation and a response with the information
  # of the users (in case it was successful) or an error code.
  function retrieveOutgoingFriendRequests($username) {
    $conn = connect();

    if ($conn != null) {
      $sql = "SELECT username, CONCAT(firstName, ' ', lastName) AS completeName, profilePicture
              FROM Users U JOIN Friends F ON U.username = F.username2
              WHERE F.username1 = ? AND requestAccepted = 0";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('s', $username);
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

  # Accepts a friend request sent by another user.
  # Parameters:
  # - $username1: String representing the username who sent the friend request.
  # - $username2: String representing the username who is accepting the friend request.
  # Return: Array with a status of the result of the operation and a response (in case it was
  # successful) or an error code.
  function acceptFriendRequest($username1, $username2) {
    $conn = connect();

    if ($conn != null) {
      $sql = "UPDATE Friends SET requestAccepted = 1 WHERE username1 = ? AND username2 = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('ss', $username1, $username2);

      if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return array('status' => 'SUCCESS', 'response' => 'Successfully accepted friend request');
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

  # Rejects a friend request sent by another user.
  # Parameters:
  # - $username1: String representing the username who sent the friend request.
  # - $username2: String representing the username who is rejecting the friend request.
  # Return: Array with a status of the result of the operation and a response (in case it was
  # successful) or an error code.
  function rejectFriendRequest($username1, $username2) {
    $conn = connect();

    if ($conn != null) {
      $sql = "DELETE FROM Friends WHERE username1 = ? AND username2 = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param('ss', $username1, $username2);

      if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return array('status' => 'SUCCESS', 'response' => 'Successfully rejected friend request');
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