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
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method == 'GET') {
      $userName = $_GET['username'];
      $sql = "SELECT
                C.content, C.username, C.commentDate,
                CONCAT(U.firstName, ' ', U.lastName) AS completeName, U.profilePicture 
              FROM Comments C JOIN Users U ON C.username = U.username
              WHERE C.username IN
                (SELECT DISTINCT(username2)
                FROM Followers
                WHERE username1 = '$userName')
              OR C.username = '$userName'
              ORDER BY commentDate DESC";
      
      $result = $connection->query($sql);

      $response = array('comments' => array());
      while ($row = $result->fetch_assoc()) {
        $response['comments'][] = array(
          'content' => $row['content'],
          'username' => $row['username'],
          'date' => $row['commentDate'],
          'completeName' => $row['completeName'],
          'profilePicture' => $row['profilePicture']
        );
      }
      echo json_encode($response);
    }

    else if ($method == 'POST') {
      $userName = $_POST['username'];
      $content = $_POST['content'];
      $commentDate = $_POST['commentDate'];
      if (!array_key_exists('repliedCommentId', $_POST) || $_POST['repliedCommentId'] == null) {
        $repliedCommentId = 'NULL';
      } else {
        $repliedCommentId = $_POST['repliedCommentId'];
      }

      $sql = "INSERT INTO Comments (content, username, repliedCommentId, commentDate) VALUES (";
      $sql = $sql . $content . ', ';
      $sql = $sql . "'$userName', $repliedCommentId, STR_TO_DATE('$commentDate', '%Y-%m-%d %H:%i:%S'))";
      
      if (mysqli_query($connection, $sql)) {
        $response = array('status' => 'success');
        echo json_encode($response);
      } else {
        header('HTTP/1.1 500 Bad connection, something went wrong while saving your data, please try again later');
        die('There was a bad connection');
      }
    }
  }

  $connection->close();
?>