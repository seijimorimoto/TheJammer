<?php
  header('Content-type: application/json');

  session_start();

  $method = $_SERVER['REQUEST_METHOD'];
  
  if ($method == 'GET') {
    if (isset($_SESSION['firstName']) &&
        isset($_SESSION['lastName']) &&
        isset($_SESSION['username'])) {
      $response = array('firstName' => $_SESSION['firstName'],
                        'lastName' => $_SESSION['lastName'],
                        'username' => $_SESSION['username']);
      echo json_encode($response);
    } else {
      session_destroy();
      header('HTTP/1.1 406 Session not set yet');
      die('Your session has expired.');
    }
  }

  else if ($method == 'DELETE') {
    unset($_SESSION['firstName']);
    unset($_SESSION['lastName']);
    unset($_SESSION['username']);
    session_destroy();
    $response = array('response' => 'Session successfully terminated.');
    echo json_encode($response);
  }
?>