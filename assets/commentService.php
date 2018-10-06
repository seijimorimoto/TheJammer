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

  $connection->close();
?>