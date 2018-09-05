let loginBtn = document.getElementById("loginBtn");
let registrationBtn = document.getElementById("registerBtn");

loginBtn.addEventListener("click", function(event) {
  validateLogin();
});

registrationBtn.addEventListener("click", function(event) {
  event.preventDefault();

  validateRegistration();
})

function validateLogin() {
  let username = document.getElementById("loginUsername");
  let usernameError = document.getElementById("loginUsernameError");
  let password = document.getElementById("loginPassword");
  let passwordError = document.getElementById("loginPasswordError");
  let missingCredentials = false;
  
  if (username.value === "") {
    usernameError.textContent = "Please enter your First Name";
    usernameError.style.display = "block";
    missingCredentials = true;
  } else {
    usernameError.style.display = "none";
  }

  if (password.value === "") {
    passwordError.style.display = "block";
    missingCredentials = true;
  } else {
    passwordError.style.display = "none";
  }

  if (username.value === "lab3" && password.value === "lab3") {
    window.location.href = "./home.html";
  } else if (!missingCredentials) {
    usernameError.textContent = "Incorrect credentials";
    usernameError.style.display = "block";
  }
}

function validateRegistration() {
  if (validateRegistrationTextInputs() & validateRegistrationRadioButtons() &
    validateRegistrationDropDownMenu()) {
    window.location.href = "./home.html";
  }
}

function validateRegistrationTextInputs() {
  let firstName = document.getElementById("registrationFName");
  let firstNameError = document.getElementById("registrationFNameError");
  let lastName = document.getElementById("registrationLName");
  let lastNameError = document.getElementById("registrationLNameError");
  let username = document.getElementById("registrationUsername");
  let usernameError = document.getElementById("registrationUsernameError");
  let email = document.getElementById("registrationEmail");
  let emailError = document.getElementById("registrationEmailError");
  let password = document.getElementById("registrationPass");
  let passwordError = document.getElementById("registrationPassError");
  let passwordConf = document.getElementById("registrationPassConf");
  let passwordConfError = document.getElementById("registrationPassConfError");
  let isValid = true;

  if (firstName.value === "") {
    firstNameError.style.display = "block";
    isValid = false;
  } else {
    firstNameError.style.display = "none";
  }

  if (lastName.value === "") {
    lastNameError.style.display = "block";
    isValid = false;
  } else {
    lastNameError.style.display = "none";
  }

  if (username.value === "") {
    usernameError.style.display = "block";
    isValid = false;
  } else {
    usernameError.style.display = "none";
  }

  if (email.value === "") {
    emailError.style.display = "block";
    isValid = false;
  } else {
    emailError.style.display = "none";
  }

  if (password.value === "") {
    passwordError.style.display = "block";
    isValid = false;
  } else {
    passwordError.style.display = "none";
  }

  if (passwordConf.value === "") {
    passwordConfError.textContent = "Please confirm your Password";
    passwordConfError.style.display = "block";
    isValid = false;
  } else if (passwordConf.value != password.value) {
    passwordConfError.textContent = "Password confirmation should match password";
    passwordConfError.style.display = "block";
    isValid = false;
  } else {
    passwordConfError.style.display = "none";
  }

  return isValid;
}

function validateRegistrationRadioButtons() {
  let genderRadios = document.getElementsByName("gender");
  let genderError = document.getElementById("registrationGenderError");
  let selectedFlag = false;

  for (let i = 0; i < genderRadios.length && !selectedFlag; i++) {
    if (genderRadios[i].checked)
      selectedFlag = true;
  }

  if (!selectedFlag) {
    genderError.style.display = "block";
    return false;
  }

  genderError.style.display = "none";
  return true;
}

function validateRegistrationDropDownMenu() {
  let country = document.getElementById("registrationCountry");
  let countryError = document.getElementById("registrationCountryError");
  let selectedOption = country.options[country.selectedIndex].value;
  
  if (selectedOption === "default") {
    countryError.style.display = "block";
    return false;
  }
  
  countryError.style.display = "none";
  return true;
}