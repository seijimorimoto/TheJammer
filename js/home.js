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