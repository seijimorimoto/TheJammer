<?php
  header('Content-type: application/json');
  header('Accept: application/json');

  require_once __DIR__ . '/dataLayer.php';
  
  $request_method = $_SERVER['REQUEST_METHOD'];

  switch ($request_method) {
    case 'GET':
      $action = $_GET['action'];
      getRequests($action);
      break;
    case 'POST':
      $action = $_POST['action'];
      postRequests($action);
      break;
  }

  # Handles GET requests.
  # Parameters:
  # - $action: String representing an action requested by the front-end.
  function getRequests($action) {
    switch ($action) {
      case 'LOGIN':
        requestLogin();
        break;
      case 'PROFILE':
        requestProfile();
        break;
      case 'COMMENTS':
        requestComments();
        break;
    }
  }

  # Handles POST requests.
  # Parameters:
  # - $action: String representing an action requested by the front-end.
  function postRequests($action) {
    switch ($action) {
      case 'REGISTER':
        registerUser();
        break;
      case 'COMMENT':
        postComment();
        break;
    }
  }

  # Handles the login of the application.
  function requestLogin() {
    $username = $_GET['username'];
    $password = $_GET['password'];

    $response = attemptLogin($username, $password);
    
    if ($response['status'] == 'SUCCESS') {
      if ($_GET['rememberMe'] == 'true') {
        setcookie('username', $username, time() + 3600 * 24 * 30, '/', '', 0);
      }
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the registration of a user to the application.
  function registerUser() {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $country = $_POST['country'];
    $profilePicture = 'images/default_user_image.png';

    $response = attemptRegistration($username, $password, $firstName, $lastName, $email, $gender,
                                    $country, $profilePicture);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code'], 'The username provided already exists');
    }
  }

  # Handles the request for the profile of a given user.
  function requestProfile() {
    $username = $_GET['username'];

    $response = retrieveProfile($username);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the request for comments of a given user and his friends.
  function requestComments() {
    $username = $_GET['username'];
    
    $response = retrieveComments($username);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the request for posting/adding a comment.
  function postComment() {
    $username = $_POST['username'];
    $content = $_POST['content'];
    $commentDate = $_POST['commentDate'];
    $repliedCommentId = null;
    
    if (array_key_exists('repliedCommentId', $_POST)) {
      $repliedCommentId = $_POST['repliedCommentId'];
    }

    $response = insertComment($username, $content, $commentDate, $repliedCommentId);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles errors that occured in the data layer and returns an appropriate message to front-end.
  # Parameters:
  # - $status: Integer representing the status/reason of the error.
  # - $code: Integer representing an HTTP error code.
  # - $message: String representing an specific message to be sent to the front-end. If null, a
  #   default message for each error code will be used.
  function errorHandler($status, $code, $message = null) {
    switch ($code) {
      case 406:
        header("HTTP/1.1 $code User $status");
        if ($message == null)
          $message = 'Wrong credentials provided'; 
        break;
      case 409:
        header("HTTP/1.1 $code $status");
        if ($message == null)
          $message = 'There was a conflict with the data being sent'; 
        break;
      case 500:
        header("HTTP/1.1 $code $status. Bad connection, portal is down");
        if ($message == null)
          $message = "The server is down, we couldn't retrieve data from the database";
        break;
    }
    die($message);
  }
?>