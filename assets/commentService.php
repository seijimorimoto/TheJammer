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
    $sql = "SELECT content, username, commentDate
            FROM Comments
            WHERE username IN
              (SELECT DISTINCT(username1)
               FROM Followers
               WHERE username2 = '$userName'
               UNION
               SELECT DISTINCT(username2)
               FROM Followers
               WHERE username1 = '$userName')
            OR username = '$userName'
            ORDER BY commentDate DESC";
    
    $result = $connection->query($sql);

    $response = array('comments' => array());
    while ($row = $result->fetch_assoc()) {
      $response['comments'][] = array(
        'content' => $row['content'],
        'username' => $row['username'],
        'date' => $row['commentDate']
      );
    }
    echo json_encode($response);
  }

  $connection->close();
?>