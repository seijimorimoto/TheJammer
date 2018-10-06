let $postBox = $('#postBox');

$postBox.css('height', this.scrollHeight);

// Any time there is a change in input in the postBox, it is resized (if needed) and the
// addCommentBtn is enabled/disabled based on whether there is text on the postBox. 
$postBox.on('input', function(event) {
  autoresize(this);
  if ($(this).val().trim() === "") {
    $('#addCommentBtn').prop('disabled', true);
  } else {
    $('#addCommentBtn').prop('disabled', false);
  }
});

// When the postBox is focused, it is resized (if needed) and the addCommentBtn is shown.
$postBox.on('focus', function(event) {
  autoresize(this);
  $('#addCommentBtn').removeClass('hidden');
});

// When the postBox loses focus, if there isn't text in it, it is resized to its original height
// and the addCommentBtn is hidden.
$postBox.on('blur', function(event) {
  if ($(this).val() === "") {
    $('#addCommentBtn').addClass('hidden');
    $(this).css('height', '26px');
  }
});

// Autoresizes a textarea so as to prevent the scrollbar from showing.
function autoresize(textarea) {
  $(textarea).css('height', 'auto');
  $(textarea).css('height', $(textarea).prop('scrollHeight'));
}

// When the addCommentBtn is clicked, appends a comment to the end of the commentArea.
$('#addCommentBtn').on('click', function(event) {
  let commentText = $($postBox).val();
  let currentDate = getFormattedDate(new Date());
  let comment = {
    content: $($postBox).val(),
    username: 'Deku',
    date: currentDate,
    completeName: 'Izuku Midoriya',
    profilePicture: 'images/Deku_profile_pic.jpg'
  }
  prependComment(comment);
  $($postBox).val('');
  $($postBox).css('height', '26px');
  $(this).prop('disabled', true);
  $(this).addClass('hidden');
});

// Temporal user for testing purposes. To be removed when sessions are implemented.
let jsonUser = {
  'username': 'Deku'
}

// AJAX GET request to the comment service executed when the page is loaded. Retrieves comments
// posted by the current user or any of the people he follows and adds them to the page.
$.ajax({
  url: './assets/commentService.php',
  type: 'GET',
  data: jsonUser,
  ContentType: 'application/json',
  dataType: 'json',
  success: function(data) {
    for (let index in data.comments) {
      appendComment(data.comments[index]);
    }
  },
  error: function(err) {
    console.log(err);
  }
});

// Transforms a Date object into a string with the following format: "DD/MM/YYYY hh:mm:ss".
function getFormattedDate(date) {
  let newDate = date.getFullYear().toString();
  newDate += '-';
  newDate +=
    (date.getMonth() + 1 < 10) ?
    '0' + (date.getMonth() + 1).toString() :
    (date.getMonth() + 1).toString();
  newDate += '-';
  newDate += (date.getDate() < 10) ? '0' + date.getDate().toString() : date.getDate().toString();
  newDate += ' ';
  newDate += (date.getHours() < 10) ? '0' + date.getHours().toString() : date.getHours().toString();
  newDate += ':';
  newDate += (date.getMinutes() < 10) ? '0' + date.getMinutes().toString() : date.getMinutes().toString();
  newDate += ':';
  newDate += (date.getSeconds() < 10) ? '0' + date.getSeconds().toString() : date.getSeconds().toString();
  return newDate;
}

// Adds a comment at the end of the river of comments in the home page.
function appendComment(comment) {
  let newHtml = createCommentAsHtml(comment);
  $('#commentArea').append(newHtml);
}

// Adds a comment to the beginning of the river of comments in the home page.
function prependComment(comment) {
  let newHtml = createCommentAsHtml(comment);
  $('#commentArea').prepend(newHtml);
}

// Creates HTML content to hold a comment and to display it appropriately on the home page.
function createCommentAsHtml(comment) {
  if (comment.profilePicture == '') {
    comment.profilePicture = 'images/default_user_image.png';
  }

  return `<div class="commentBox twoColumnGrid">
            <img class="smallUserImage" src="${comment.profilePicture}" alt="User Image">
            <div class="comment">
              <div class="commentHeader">
                <span class="headerComment1">${comment.completeName}</span>
                <span class="headerComment2">@${comment.username}</span>
                <span class="headerComment2">${comment.date}</span>
              </div>
              <div class="commentBody">
                ${comment.content}
              </div>
            </div>
          </div>`;
}

// AJAX GET request to the profileService that retrieves the user profile data stored in the DB.
// This request is executed when the home page is loaded to prevent firing an AJAX request each time
// the profile tab/icon is clicked (however, the profile section itself is displayed only when the
// profile tab is clicked).
$.ajax({
  url: './assets/profileService.php',
  type: 'GET',
  data: jsonUser,
  ContentType: 'application/json',
  dataType: 'json',
  success: function(data) {
    completeName = data.profile[0].completeName;
    $('#username').text('@' + data.profile[0].username);
    $('#completeName').text(completeName);
    $('#email').text(data.profile[0].email);
    if (data.profile[0].gender === 'M') {
      $('#gender').text('Male');
    } else {
      $('#gender').text('Female');
    }
    $('#country').text(data.profile[0].country);
  },
  error: function(err) {
    console.log(err);
  }
});

// When the #profileTab is clicked, display the profile section and hide the home section.
$('#profileTab').on('click', function(event) {
  $('#homeSection').addClass('hidden');
  $('#homeTab').removeClass('selected');
  $('#profileSection').removeClass('hidden');
});

// When the #homeTab is clicked, display the home section and hide the profile section.
$('#homeTab').on('click', function(event) {
  $('#profileSection').addClass('hidden');
  $('#homeSection').removeClass('hidden');
  $('#homeTab').addClass('selected');
})