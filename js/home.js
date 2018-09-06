$('#postBox')
  .css('height', this.scrollHeight)
  .on('input', function(event) {
    $(this).css('height', 'auto');
    $(this).css('height', $(this).prop('scrollHeight'));
    if ($(this).val() === "") {
      $('#addCommentBtn').prop('disabled', true);
    } else {
      $('#addCommentBtn').prop('disabled', false);
    }
  })
  .on('focus', function(event) {
    $(this).css('height', 'auto');
    $(this).css('height', $(this).prop('scrollHeight'));
    $('#addCommentBtn').removeClass('hidden');
  })
  .on('blur', function(event) {
    if ($(this).val() === "") {
      $('#addCommentBtn').addClass('hidden');
      $(this).css('height', '26px');
    }
  });

$('#addCommentBtn').on('click', function(event) {
  let commentText = $('#postBox').val();
  let newHtml = `<div class="commentBox twoColumnGrid">
                  <img class="userImage" src="images/default_user_image.png" alt="User Image">
                  <div class="comment">${commentText}</div>
                 </div>`;
  $('#commentArea').append(newHtml);
});