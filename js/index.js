// AJAX GET request to the sessionService executed when the page loads. Determines whether a session
// exists. If it does, then it redirects to the home page of the logged in user.
$.ajax({
  url: './assets/sessionService.php',
  type: 'GET',
  dataType: 'json',
  success: function(data) {
    $(location).attr('href', './home.html');
  },
  error: function(err) {
    // No need to log the error, since this just means that no user is logged in.
  }
});

// Getting the array of cookies.
let cookies = document.cookie.split('; ');

let usernameCookie = getCookie('username');
if (usernameCookie != null) {
  $('#loginUsername').val(usernameCookie);
}

// When the loginBtn is clicked, validates the login fields.
$("#loginBtn").on("click", function(event) {
  validateLogin();
});

// When the registerBtn is clicked, validates the registration fields.
$("#registerBtn").on("click", function(event) {
  event.preventDefault();
  validateRegistration();
});

// Retrieves the value of the cookie with name cookieName from the global array of cookies.
// Returns null if the cookie does not exist.
function getCookie(cookieName) {
  for (let i in cookies) {
    let splitPosition = cookies[i].indexOf('=');
    let cookieKey = cookies[i].substring(0, splitPosition);
    let cookieValue = cookies[i].substring(splitPosition + 1);
    if (cookieKey == cookieName) {
      return cookieValue;
    }
  }
  return null;
}

// Validates the login fields and credentials.
function validateLogin() {
  let $username = $("#loginUsername");
  let $usernameError = $("#loginUsernameError");
  let $password = $("#loginPassword");
  let $passwordError = $("#loginPasswordError");
  let missingCredentials = false;
  
  if ($username.val() === "") {
    $usernameError.text("Please enter your First Name");
    $usernameError.removeClass("hidden");
    missingCredentials = true;
  } else {
    $usernameError.addClass("hidden");
  }

  if ($password.val() === "") {
    $passwordError.removeClass("hidden");
    missingCredentials = true;
  } else {
    $passwordError.addClass("hidden");
  }

  if (!missingCredentials) {
    checkLoginCredentials($username, $password, $usernameError);
  }
}

// Validates the registration fields.
function validateRegistration() {
  validateRegistrationTextInputs();
  if (validateRegistrationTextInputs() & validateRegistrationRadioButtons() &
    validateRegistrationDropDownMenu()) {
    registerUser();
  }
}

// Validates the registration text inputs.
function validateRegistrationTextInputs() {
  let $firstName = $("#registrationFName");
  let $firstNameError = $("#registrationFNameError");
  let $lastName = $("#registrationLName");
  let $lastNameError = $("#registrationLNameError");
  let $username = $("#registrationUsername");
  let $usernameError = $("#registrationUsernameError");
  let $email = $("#registrationEmail");
  let $emailError = $("#registrationEmailError");
  let $password = $("#registrationPass");
  let $passwordError = $("#registrationPassError");
  let $passwordConf = $("#registrationPassConf");
  let $passwordConfError = $("#registrationPassConfError");
  let isValid = true;

  if ($firstName.val() === "") {
    $firstNameError.text('Please enter your First Name');
    $firstNameError.removeClass("hidden");
    isValid = false;
  } else {
    $firstNameError.addClass("hidden");
  }

  if ($lastName.val() === "") {
    $lastNameError.removeClass("hidden");
    isValid = false;
  } else {
    $lastNameError.addClass("hidden");
  }

  if ($username.val() === "") {
    $usernameError.text("Please enter your Username");
    $usernameError.removeClass("hidden");
    isValid = false;
  } else {
    $usernameError.addClass("hidden");
  }

  if ($email.val() === "") {
    $emailError.removeClass("hidden");
    isValid = false;
  } else {
    $emailError.addClass("hidden");
  }

  if ($password.val() === "") {
    $passwordError.removeClass("hidden");
    isValid = false;
  } else {
    $passwordError.addClass("hidden");
  }

  if ($passwordConf.val() === "") {
    $passwordConfError.text("Please confirm your Password");
    $passwordConfError.removeClass("hidden");
    isValid = false;
  } else if ($passwordConf.val() != $password.val()) {
    $passwordConfError.text("Password confirmation should match password");
    $passwordConfError.removeClass("hidden");
    isValid = false;
  } else {
    $passwordConfError.addClass("hidden");
  }

  return isValid;
}

// Validates the registration radio buttons.
function validateRegistrationRadioButtons() {
  let $genderCheckedRadios = $("input[type=radio][name=gender]:checked");
  let $genderError = $("#registrationGenderError");

  if ($genderCheckedRadios.length === 0) {
    $genderError.removeClass("hidden");
    return false;
  }

  $genderError.addClass("hidden");
  return true;
}

// Validates the registration dropdown menu (the "select country" menu).
function validateRegistrationDropDownMenu() {
  let $selectedOption = $("#registrationCountry > option:checked");
  let $countryError = $("#registrationCountryError");
  
  if ($selectedOption.val() === "default") {
    $countryError.removeClass("hidden");
    return false;
  }
  
  $countryError.addClass("hidden");
  return true;
}

// Performs an AJAX GET request to the login service to check whether the user credentials are valid
// and allow/deny him to login.
function checkLoginCredentials($username, $password, $error) {
  let rememberMe = 'false';
  if ($("#rememberMe").prop("checked")) {
    rememberMe = 'true';
  }

  let jsonToSend = {
    'username': $username.val(),
    'password': $password.val(),
    'rememberMe': rememberMe
  };

  $.ajax({
    url: './assets/loginService.php',
    type: 'GET',
    data: jsonToSend,
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      window.location.href = './home.html';
    },
    error: function(error) {
      console.log(error);
      $error.text("Incorrect credentials");
      $error.removeClass("hidden");
    }
  });
}

// Performs an AJAX POST request to the registration service in order to register the information of
// a new user.
function registerUser() {
  let jsonToSend = {
    'username': $('#registrationUsername').val(),
    'password': $('#registrationPass').val(),
    'firstName': $('#registrationFName').val(),
    'lastName': $('#registrationLName').val(),
    'email': $('#registrationEmail').val(),
    'gender': $("input[type=radio][name=gender]:checked").val(),
    'country': $("#registrationCountry").val()
  };

  $.ajax({
    url: './assets/registrationService.php',
    type: 'POST',
    data: jsonToSend,
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      window.location.href = './home.html';
    },
    error: function(error) {
      console.log(error);
      if (error.status === 409) {
        let $usernameError = $("#registrationUsernameError");
        $usernameError.text('Username already exists. Please select another one');
        $usernameError.removeClass('hidden');
      } else if (error.status === 500) {
        let $error = $("#registrationFNameError");
        $error.text('Something went wrong when saving your data. Try again later');
        $error.removeClass('hidden');
      } else {
        let $error = $('#registrationFNameError');
        $error.text('There was an unexpected error.');
        $error.removeClass('hidden');
      }
    }
  });
}