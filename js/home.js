let $postBox = $('#postBox');
let completeName = '';
let username = '';

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
  prependComment(completeName, username, commentText);
  $($postBox).val('');
  $($postBox).css('height', '26px');
  $(this).prop('disabled', true);
  $(this).addClass('hidden');
});

// AJAX GET request executed when the page is loaded. Retrieves comments stored in an xml and adds
// it to the page.
$.ajax({
  url: './assets/comments.xml',
  type: 'GET',
  dataType: 'xml',
  success: function(data) {
    $(data).find('contact').each(function() {
      let $name = $(this).find('name');
      let commentText = $(this).find('text').text();
      appendComment($($name).text(), $($name).attr('username'), commentText);
    })
  },
  error: function(err) {
    console.log(err);
  }
});

// Adds a comment at the end of the river of comments in the home page.
function appendComment(completeName, username, commentText) {
  let newHtml = createCommentAsHtml(completeName, username, commentText);
  $('#commentArea').append(newHtml);
}

// Adds a comment to the beginning of the river of comments in the home page.
function prependComment(completeName, username, commentText) {
  let newHtml = createCommentAsHtml(completeName, username, commentText);
  $('#commentArea').prepend(newHtml);
}

// Creates HTML content to hold a comment and to display it appropriately on the home page.
function createCommentAsHtml(completeName, username, commentText) {
  return `<div class="commentBox twoColumnGrid">
            <img class="smallUserImage" src="images/default_user_image.png" alt="User Image">
            <div class="comment">
              <div class="commentHeader">
                <span class="completeNameComment">${completeName}</span>
                <span class="usernameComment">@${username}</span>
              </div>
              <div class="commentBody">
                ${commentText}
              </div>
            </div>
          </div>`;
}

// AJAX GET request that retrieves the user profile data stored in a JSON file. This request is
// executed when the home page is loaded to prevent firing an AJAX request each time the profile
// tab/icon is clicked (however, the profile section itself is displayed only when the profile tab
// is clicked).
$.ajax({
  url: './assets/profileData.json',
  type: 'GET',
  dataType: 'json',
  success: function(data) {
    username = data.username;
    completeName = data.name.firstName + " " + data.name.lastName;
    $('#username').text('@' + username);
    $('#completeName').text(completeName);
    $('#email').text(data.email);
    if (data.gender === 'M') {
      $('#gender').text('Male');
    } else {
      $('#gender').text('Female');
    }
    $('#country').text(data.country);
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