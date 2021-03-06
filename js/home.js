let userInfo;
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

// When the addCommentBtn is clicked, performs an AJAX POST request to save the comment in the DB.
// If the request is successful, the comment is added at the end of the commentArea.
$('#addCommentBtn').on('click', function(event) {
  retrieveSession(
    // Callback function executed if the session was successfully retrieved.
    function() {
      let commentText = $($postBox).val();
      let currentDate = getFormattedDate(new Date());

      let dataToSend = {
        'action': 'COMMENT',
        'commentDate': currentDate,
        'content': commentText,
        'username': userInfo.username,
      }

      $.ajax({
        url: './assets/applicationLayer.php',
        type: 'POST',
        data: dataToSend,
        ContentType: 'application/json',
        dataType: 'json',
        success: function(data) {
          let comment = {
            content: commentText,
            username: userInfo.username,
            date: currentDate,
            completeName: userInfo.firstName + ' ' + userInfo.lastName,
            profilePicture: userInfo.profilePicture
          };

          prependComment(comment);
          $($postBox).val('');
          $($postBox).css('height', '26px');
          $(this).prop('disabled', true);
          $(this).addClass('hidden');
        },
        error: function(err) {
          alert(err.responseText);
        }
      });
    }
  );
});

// Executes an AJAX GET request to the comment service. Retrieves comments posted by the current
// user or any of the people he follows and adds them to the page. This function must be called
// after the session variables are retrieved from the server.
function getComments() {
  $('#commentArea').html('');
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'GET',
    data: {
      'action': 'COMMENTS',
      'username': userInfo.username
    },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      for (let index in data) {
        appendComment(data[index]);
      }
    },
    error: function(err) {
      console.log(err);
    }
  });
}

// Transforms a Date object into a string with the following format: "YYYY-MM-DD hh:mm:ss".
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

  return `<div class="whiteBox twoColumnGrid">
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

// Sets the appropriate HTML img elements to display the profile picture of the user. This function
// must be called after the session variables are retrieved from the server.
function setUserImages() {
  $('#profileTab').attr('src', userInfo.profilePicture);
  $('#profilePicture').attr('src', userInfo.profilePicture);
  $('#postUserImage').attr('src', userInfo.profilePicture);
}

// Executes an AJAX GET request to the profileService that retrieves the user profile data stored in
// the DB. This function must be called after the session variables are retrieved from the server.
function getUserProfile() {
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'GET',
    data: {
      'action': 'PROFILE',
      'username': userInfo.username
    },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      completeName = data[0].completeName;
      $('#username').text('@' + data[0].username);
      $('#completeName').text(completeName);
      $('#email').text(data[0].email);
      if (data[0].gender === 'M') {
        $('#gender').text('Male');
      } else {
        $('#gender').text('Female');
      }
      $('#country').text(data[0].country);
    },
    error: function(err) {
      console.log(err);
    }
  });
}

// When the ENTER key is pressed while the searchBox is focused, performs an AJAX GET request to
// search for all the users that match the pattern in the searchBox and that aren't already friends
// and that do not have already a friend request from the current user.
$('#searchBox').keypress(function(event) {
  let keyPressed = event.which;
  if (keyPressed == 13) { // The key code of ENTER.
    let pattern = $(this).val().trim();
    if (pattern != '') {
      $.ajax({
        url: './assets/applicationLayer.php',
        type: 'GET',
        data: {
          'action': 'SEARCH',
          'pattern': pattern,
          'username': userInfo.username
        },
        ContentType: 'application/json',
        dataType: 'json',
        success: function(data) {
          $('#searchResultsBody').html('');
          if (data.length > 0) {
            $('#searchResultsHeaderText').text(`Search results for '${pattern}'`);
            for (let index in data) {
              appendSearchResultToSearchList(data[index]);
            }
          } else {
            $('#searchResultsHeaderText').text(`No results found for '${pattern}'`);
          }
          activateSection('searchSection');
        },
        error: function(err) {
          console.log(err);
        }
      });
    }
  }
});

// Adds a user's information to the river of search results.
function appendSearchResultToSearchList(user) {
  let newHtml = createSearchResultAsHtml(user);
  $('#searchResultsBody').append(newHtml);
}

// Creates HTML content to hold a search result and to display it appropriately on the home page. 
function createSearchResultAsHtml(user) {
  return `<div class="whiteBox gridContainer result">
            <img class="resultImage" src="${user.profilePicture}">
            <div class="resultData">
              <span class="resultCompleteName">${user.completeName}</span>
              <span class="resultUsername">@${user.username}</span>
            </div>
            <i class="material-icons md-36 md-dark blueHover addIcon">person_add</i>
          </div>`;
}

// When any .addIcon is clicked, performs an AJAX POST request to send a friend request to the user
// corresponding to the .addIcon clicked. In this event listener, #searchResultsBody is the targeted
// element and .addIcon is passed as a selector since dynamically generated content (the .addIcon
// elements in this case) cannot be directly bounded to event listeners in JQuery.
$('#searchResultsBody').on('click', '.addIcon', function(event) {
  let $iconClicked = this;
  let $username2 = $($iconClicked).parent().find('.resultUsername')[0];
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'POST',
    data: {
      'action': 'FRIEND_REQUEST',
      'username1': userInfo.username,
      'username2': $($username2).text().slice(1)
    },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      $($iconClicked).addClass('md-inactive');
      $($iconClicked).removeClass('blueHover');
      $($iconClicked).removeClass('addIcon');
    },
    error: function(err) {
      console.log(err);
    }
  });
});

// Retrieves incoming and outgoing friend requests.
function getFriendRequests() {
  getIncomingFriendRequests();
  getOutgoingFriendRequests();
}

// Executes an AJAX GET request to the in_friend_request service. Retrieves friend requests received
// by the current user. This function must be called after the session variables are retrieved from
// the server.
function getIncomingFriendRequests() {
  $('#inFriendRequestsBody').html('');
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'GET',
    data: {
      'action': 'IN_FRIEND_REQUEST',
      'username': userInfo.username
    },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      for (let index in data) {
        appendIncomingFriendRequestToList(data[index]);
      }
    },
    error: function(err) {
      console.log(err);
    }
  });
}

// Executes an AJAX GET request to the out_friend_request service. Retrieves friend requests sent
// by the current user. This function must be called after the session variables are retrieved from
// the server.
function getOutgoingFriendRequests() {
  $('#outFriendRequestsBody').html('');
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'GET',
    data: {
      'action': 'OUT_FRIEND_REQUEST',
      'username': userInfo.username
    },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      for (let index in data) {
        appendOutgoingFriendRequestToList(data[index]);
      }
    },
    error: function(err) {
      console.log(err);
    }
  });
}

// Adds the information of a friend request to the river of incoming friend requests.
function appendIncomingFriendRequestToList(friendRequest) {
  let newHtml = createIncomingFriendRequestAsHtml(friendRequest);
  $('#inFriendRequestsBody').append(newHtml);
}

// Creates HTML content to hold an incoming friend request and to display it appropriately on the
// section for incoming friend requests.
function createIncomingFriendRequestAsHtml(friendRequest) {
  return `<div class="whiteBox gridContainer result">
            <img class="resultImage" src="${friendRequest.profilePicture}">
            <div class="resultData">
              <span class="resultCompleteName">${friendRequest.completeName}</span>
              <span class="resultUsername">@${friendRequest.username}</span>
            </div>
            <div>
              <i class="material-icons md-36 md-dark greenHover acceptIcon">check_circle</i>
              <i class="material-icons md-36 md-dark redHover cancelIcon">cancel</i>
            </div>
          </div>`;
}

// Adds the information of a friend request to the river of outgoing friend requests.
function appendOutgoingFriendRequestToList(friendRequest) {
  let newHtml = createOutgoingFriendRequestAsHtml(friendRequest);
  $('#outFriendRequestsBody').append(newHtml);
}

// Creates HTML content to hold an outgoing friend request and to display it appropriately on the
// section for outgoing friend requests.
function createOutgoingFriendRequestAsHtml(friendRequest) {
  return `<div class="whiteBox gridContainer2 result">
            <img class="resultImage" src="${friendRequest.profilePicture}">
            <div class="resultData">
              <span class="resultCompleteName">${friendRequest.completeName}</span>
              <span class="resultUsername">@${friendRequest.username}</span>
            </div>
          </div>`;
}

// When any .acceptIcon is clicked, performs an AJAX PUT request to accept the friend request from a
// user corresponding to the .acceptIcon clicked. In this event listener, #inFriendRequestsBody is
// the targeted element and .acceptIcon is passed as a selector since dynamically generated content
// (the .acceptIcon elements in this case) cannot be directly bounded to event listeners in JQuery.
$('#inFriendRequestsBody').on('click', '.acceptIcon', function(event) {
  let $friendRequest = $(this).parent().parent();
  let $username1 = $($friendRequest).find('.resultUsername')[0];
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'PUT',
    data: {
      'action': 'FRIEND_REQUEST',
      'username1': $($username1).text().slice(1),
      'username2': userInfo.username
    },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      $($friendRequest).remove();
    },
    error: function(err) {
      console.log(err);
    }
  });
});

// When any .cancelIcon is clicked, performs an AJAX DELETE request to cancel the friend request
// from a user corresponding to the .cancelIcon clicked. In this event listener,
// #inFriendRequestsBody is the targeted element and .cancelIcon is passed as a selector since
// dynamically generated content (the .cancelIcon elements in this case) cannot be directly bounded
// to event listeners in JQuery.
$('#inFriendRequestsBody').on('click', '.cancelIcon', function(event) {
  let $friendRequest = $(this).parent().parent();
  let $username1 = $($friendRequest).find('.resultUsername')[0];
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'DELETE',
    data: {
      'action': 'FRIEND_REQUEST',
      'username1': $($username1).text().slice(1),
      'username2': userInfo.username
    },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      $($friendRequest).remove();
    },
    error: function(err) {
      console.log(err);
    }
  });
});

// Executes an AJAX GET request to the friends service. Retrieves the information of all the friends
// of the current user. This function must be called after the session variables are retrieved from
// the server.
function getFriends() {
  $('#friendListBody').html('');
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'GET',
    data: {
      'action': 'FRIENDS',
      'username': userInfo.username
    },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      for (let index in data) {
        appendFriendToList(data[index]);
      }
    },
    error: function(err) {
      console.log(err);
    }
  });
}

// Adds the information of a friend to the river/list of friends displayed in the Friend section.
function appendFriendToList(friend) {
  let newHtml = createFriendInfoAsHtml(friend);
  $('#friendListBody').append(newHtml);
}

// Creates HTML content to hold the information of a friend and to display it appropriately on the
// Friend section.
function createFriendInfoAsHtml(friend) {
  return `<div class="whiteBox gridContainer2 result">
            <img class="resultImage" src="${friend.profilePicture}">
            <div class="resultData">
              <span class="resultCompleteName">${friend.completeName}</span>
              <span class="resultUsername">@${friend.username}</span>
            </div>
          </div>`;
}

// When the #profileTab is clicked, display the profile section and hide the others.
$('#profileTab').on('click', function(event) {
  activateSection('profileSection');
});

// When the #homeTab is clicked, display the home section and hide the others. Call as well the
// service to get all the comments posted by the current user and his friends.
$('#homeTab').on('click', function(event) {
  activateSection('homeSection', 'homeTab');
  retrieveSession(getComments());
});

// When the #friendRequestsTab is clicked, display the friend requests tab and hide the others.
// Call as well the service to get all the friend requests.
$('#friendRequestsTab').on('click', function(event) {
  activateSection('friendRequestSection', 'friendRequestsTab');
  retrieveSession(getFriendRequests());
});

// When the #friendsTab is clicked, display the friends tab and hide the others. Call as well the
// service to get all the friends of the current user.
$('#friendsTab').on('click', function(event) {
  activateSection('friendSection', 'friendsTab');
  retrieveSession(getFriends());
});

// When the #logout is clicked, call the sessionService to terminate the session and redirect to
// the login/registration page.
$('#logout').on('click', function(event) {
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'DELETE',
    data: { 'action': 'SESSION' },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      $(location).attr('href', './index.html');
    },
    error: function(err) {
      console.log(err);
    }
  });
});

// Activates/displays a specific HTML section and hides the currently displayed section.
function activateSection(sectionId, tabId = null) {
  let $currentSection = $('section').not('.hidden')[0];
  $($currentSection).addClass('hidden');
  $(`#${sectionId}`).removeClass('hidden');

  let $currentTab = $('.menuItem.selected')[0];
  $($currentTab).removeClass('selected');
  if (tabId != null) {
    let $tabToSelect = $(`#${tabId}`); 
    if ($tabToSelect.length > 0) {
      $($tabToSelect).addClass('selected');
    }
  }
}

// Retrieves the session variables and calls the callback function (default, an empty function) on
// success. If the session does not exists, then it redirects to the login/registration page.
function retrieveSession(callback = function() {}) {
  $.ajax({
    url: './assets/applicationLayer.php',
    type: 'GET',
    data: { 'action': 'SESSION' },
    ContentType: 'application/json',
    dataType: 'json',
    success: function(data) {
      userInfo = data;
      callback();
    },
    error: function(err) {
      alert(err.responseText);
      $(location).attr('href', './index.html');
    }
  });
}

retrieveSession(function() {
  setUserImages();
  getComments();
  getUserProfile();
});