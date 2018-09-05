$("#loginBtn").on("click", function(event) {
  validateLogin();
});

$("#registerBtn").on("click", function(event) {
  event.preventDefault();

  validateRegistration();
});

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

  if ($username.val() === "lab3" && $password.val() === "lab3") {
    window.location.href = "./home.html";
  } else if (!missingCredentials) {
    $usernameError.text("Incorrect credentials");
    $usernameError.removeClass("hidden");
  }
}

function validateRegistration() {
  validateRegistrationTextInputs();
  if (validateRegistrationTextInputs() & validateRegistrationRadioButtons() &
    validateRegistrationDropDownMenu()) {
    window.location.href = "./home.html";
  }
}

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