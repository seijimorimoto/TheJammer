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
    case 'DELETE':
      parse_str(file_get_contents('php://input'), $deleteParams);
      $action = $deleteParams['action'];
      deleteRequests($action, $deleteParams);
      break;
    case 'PUT':
      parse_str(file_get_contents('php://input'), $putParams);
      $action = $putParams['action'];
      putRequests($action, $putParams);
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
      case 'SEARCH':
        searchNewFriends();
        break;
      case 'IN_FRIEND_REQUEST':
        requestIncomingFriendRequests();
        break;
      case 'OUT_FRIEND_REQUEST':
        requestOutgoingFriendRequests();
        break;
      case 'FRIENDS':
        requestFriends();
        break;
      case 'SESSION':
        retrieveSession();
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
      case 'FRIEND_REQUEST':
        sendFriendRequest();
        break;
    }
  }

  # Handles DELETE requests.
  # Parameters:
  # - $action: String representing an action requested by the front-end.
  # - $deleteParams: Associative array containing params sent in the DELETE request.
  function deleteRequests($action, $deleteParams) {
    switch ($action) {
      case 'FRIEND_REQUEST':
        cancelFriendRequest($deleteParams);
        break;
      case 'SESSION':
        deleteSession();
        break;
    }
  }

  # Handles PUT requests.
  # Parameters:
  # - $action: String representing an action requested by the front-end.
  # - $putParams: Associative array containing params sent in the PUT request.
  function putRequests($action, $putParams) {
    switch ($action) {
      case 'FRIEND_REQUEST':
        confirmFriendRequest($putParams);
        break;
    }
  }

  # Handles the login of the application.
  function requestLogin() {
    $username = $_GET['username'];
    $password = $_GET['password'];

    $response = attemptLogin($username, $password);
    
    if ($response['status'] == 'SUCCESS') {
      session_start();
      $_SESSION['firstName'] = $response['response']['firstName'];
      $_SESSION['lastName'] = $response['response']['lastName'];
      $_SESSION['username'] = $username;
      $_SESSION['profilePicture'] = $response['response']['profilePicture'];

      if ($_GET['rememberMe'] == 'true') {
        setcookie('username', $username, time() + 3600 * 24 * 30, '/', '', 0);
      }

      echo json_encode($response['response']['message']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the registration of a user to the application.
  function registerUser() {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $country = $_POST['country'];
    $profilePicture = 'images/default_user_image.png';

    $response = attemptRegistration($username, $password, $firstName, $lastName, $email, $gender,
                                    $country, $profilePicture);

    if ($response['status'] == 'SUCCESS') {
      session_start();
      $_SESSION['firstName'] = $firstName;
      $_SESSION['lastName'] = $lastName;
      $_SESSION['username'] = $username;
      $_SESSION['profilePicture'] = $profilePicture;
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

  # Handles the request for searching new friends.
  function searchNewFriends() {
    $username = $_GET['username'];
    $pattern = $_GET['pattern'] . '%';

    $response = findNewFriends($username, $pattern);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  // Handles the request for sending a friend request to another user.
  function sendFriendRequest() {
    $username1 = $_POST['username1'];
    $username2 = $_POST['username2'];

    $response = addFriend($username1, $username2);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the request for retrieving all friend request received by the current user.
  function requestIncomingFriendRequests() {
    $username = $_GET['username'];

    $response = retrieveIncomingFriendRequests($username);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the request for retrieving all friend request sent by the current user.
  function requestOutgoingFriendRequests() {
    $username = $_GET['username'];

    $response = retrieveOutgoingFriendRequests($username);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the request for confirming/accepting a friend request from another user.
  # Parameters:
  # - $params: Associative array containing params sent in the PUT request.
  function confirmFriendRequest($params) {
    $username1 = $params['username1'];
    $username2 = $params['username2'];

    $response = acceptFriendRequest($username1, $username2);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the request for cancelling a friend request from another user.
  # Parameters:
  # - $params: Associative array containing params sent in the DELETE request.
  function cancelFriendRequest($params) {
    $username1 = $params['username1'];
    $username2 = $params['username2'];

    $response = rejectFriendRequest($username1, $username2);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Handles the request for retrieving all the friends of the current user.
  function requestFriends() {
    $username = $_GET['username'];

    $response = retrieveFriends($username);

    if ($response['status'] == 'SUCCESS') {
      echo json_encode($response['response']);
    } else {
      errorHandler($response['status'], $response['code']);
    }
  }

  # Sends the session variables to the front-end, if they exist. Otherwise, sends an error message.
  function retrieveSession() {
    session_start();

    if (isset($_SESSION['firstName']) &&
        isset($_SESSION['lastName']) &&
        isset($_SESSION['username']) &&
        isset($_SESSION['profilePicture'])) {
      $response = array('firstName' => $_SESSION['firstName'],
                        'lastName' => $_SESSION['lastName'],
                        'username' => $_SESSION['username'],
                        'profilePicture' => $_SESSION['profilePicture']);
      echo json_encode($response);
    } else {
      session_destroy();
      errorHandler('NOT_LOGGED_IN', 406, "The session has expired");
    }
  }

  # Deletes a session and all its variables.
  function deleteSession() {
    session_start();
    unset($_SESSION['firstName']);
    unset($_SESSION['lastName']);
    unset($_SESSION['username']);
    unset($_SESSION['profilePicture']);
    session_destroy();
    echo json_encode(array('response' => 'Successful termination of the session'));
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