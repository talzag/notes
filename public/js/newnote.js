$('textarea.note-area').focus();

function log(text) {
// console.log(text);
}

// show success
function showSuccess(text) {
  $('#loading-screen').hide();
  log(text);
  log('show success');
  $('a.top-left').removeAttr('href');
  if ($('.status-bar').hasClass('success')) {
    $('.status-bar').fadeOut(200).fadeIn(200).fadeOut(200)
    .fadeIn(200);
  } else {
    $('.status-bar').addClass('success');
    $('.success a.top-left .message').text(text);
  }
}

function hideSuccess(text) {
  $('a.top-left').attr('href', '/');
  $('.success a.top-left .message').text(text);
  $('.status-bar').removeClass('success');
  $('textarea.note-area').focus();
}

function resizeTextArea() {
  $('textarea').height('auto');
  log($('textarea').prop('scrollHeight'));
  $('textarea').height($('textarea').prop('scrollHeight'));
}

function toggleShowOutput() {
  $('body').toggleClass('show-output');
  $('textarea').focus();
  showHTMLOutput();
  resizeTextArea();
}

function createNewUser() {
  // This should be a model where they make an account
  $('.popin').hide();
  $('#login-screen').fadeIn('fast');
  $('.signup-form').find('input[type=email], input[type=password]').val('');
  $('.signup-form').show();
  $('.login-form').hide();
  $('input.user-email').focus();
}

function createTempUser() {
  ga('send', 'event', 'Users', 'New', 'Temporary');
  log('CREATE TEMP USER');
  $.ajax({
    url: 'users/guest',
    type:'POST',
    dataType: 'json',
    data: {
      note_text: $('textarea.note-area').val(),
    },
    success: function (data) {
      log(data);
      if (data.success) {
        $('.popin').hide();
        showSuccess('successfully created temp-user and your first note', 3000);
        $('.view-note').show().attr('href', `?note=${data.insert_id}`);
      }
    },
    error: () => {
      log('error');
    },
  });
}

function createGoogleUser() {

}

function saveNote(callback, params) {
  log('SAVE');
  if ($('textarea.note-area').val().length > 0) {
    $('#loading-screen').show();
    $.ajax({
      url: 'notes/create',
      type: 'POST',
      dataType: 'JSON',
      data: {
        note_text: $('textarea.note-area').val(),
        id: $('body').attr('id'),
      },
      statusCode: {
        200: (data) => {
          log(data.insert_id);
          if (data.insert_id !== null) {
            ga('send', 'event', 'Notes', 'Save', 'New');
            $('body').attr('id', data.insert_id);
            $('.view-note').attr('href', `?note=${data.insert_id}`);
            callback.apply(null, params);
          } else {
            ga('send', 'event', 'Notes', 'Save', 'Old');
            $('.view-note').attr('href', `?note=${$('body').attr('id')}`);
            callback.apply(null, params);
          }
        },
        201: () => {
          // $("#choose-user-type").fadeIn("fast");
          createNewUser();
        },
        500: () => {
          ga('send', 'event', 'Notes', 'Error', 'New');
          alert('Something went wrong saving your note - email tomasienrbc@gmail.com and yell at him about it');
        },
      },
      success: (data) => {
        log(data.status);
      },
      error: () => {
        log('error');
      },
    });
  }
}

function saveGoogleDocData(callback, params) {
  log('save google doc');
  if ($('textarea.note-area').val().length > 0) {
    $.ajax({
      url: 'google/addDoc',
      type: 'POST',
      dataType: 'JSON',
      data: {
        note_text: $('textarea.note-area').val(),
        id: $('body').attr('id'),
      },
      statusCode: {
        200: (data) => {
          ga('send', 'event', 'Extensions', 'Google', 'New');
          $('.view-external-link').addClass('show');
          $('.view-external-link').text('google doc');
          $('.view-external-link').attr('href', data.gdoc_link);
          window.open(data.gdoc_link, '_blank');
          callback.apply(null, params);
          log(data);
        },
        201: (data) => {
          ga('send', 'event', 'Extensions', 'Google', 'Auth');
          log(data);
          window.location = data.auth_url;
        },
        500: () => {
          ga('send', 'event', 'Notes', 'Error', 'Google');
          alert('Something went wrong saving your note - email tomasienrbc@gmail.com and yell at him about it');
        },
      },
      success: (data) => {
        log(data);
      },
      error: () => {
        log('error');
      },
    });
  }
}

function saveGoogleDoc() {
  $('#loading-screen').show();
  log('save google doc wrapper');
  const subparams = ['note and gdoc saved!', 'slow'];
  const params = [showSuccess, subparams];
  saveNote(saveGoogleDocData, params);
}

function savePDFData() {
  ga('send', 'event', 'Extensions', 'PDF', 'New');
  document.location.href = `/pdf/create?id=${$('body').attr('id')}`;
  showSuccess('Note saved and PDF downloading', 'slow');
}

function savePDF() {
  log('save google doc wrapper');
  const params = ['showSucces'];
  saveNote(savePDFData, params);
}

new Clipboard('.publish-note');

function getBaseURL() {
  const pathArray = location.href.split('/');
  const protocol = pathArray[0];
  const host = pathArray[2];
  const url = `${protocol}//${host}`;
  return url;
}

$('.show-output-button').click(() => {
  toggleShowOutput();
});

// compose notes button click functionality
$('.save-button').click((e) => {
  e.preventDefault();
  const params = ['new note created!', 'slow'];
  saveNote(showSuccess, params);
});

// This function has too much mixed data / view logic in it. Call back or something required.
function publishNote() {
  $.ajax({
    url: 'notes/publish',
    type: 'POST',
    dataType: 'json',
    data: {
      id: $('body').attr('id'),
      publish: $('body').attr('published'),
    },
    success: (data) => {
      ga('send', 'event', 'Notes', 'Share', 'Publish');
      showSuccess('public status changed', 3000);
      if (data.published) {
        $('.single-note-publish').text('make private');
        $('body').attr('published', 1);
      } else {
        $('.single-note-publish').text('publish');
        $('body').attr('published', 0);
      }
      log(data);
    },
    error: (data) => {
      ga('send', 'event', 'Notes', 'Error', 'Publish');
      alert('SOMETHING WENT WRONG - email tomasienrbc@gmail.com and yell at him');
      log(data);
    },
  });
}

$('.single-note-edit').click(() => {
  ga('send', 'event', 'Note', 'Edit', 'Old');
});

$('.guest-user').click(() => {
  createTempUser();
});

$('.permanent-user').click(() => {
  createNewUser();
});

$('.google-user').click(() => {
  createGoogleUser();
});

// toggle public / private
$('.single-note-publish').click((e) => {
  e.preventDefault();
  publishNote();
});

$('button.close-info').click((event) => {
  event.preventDefault();
  $('#login-screen').fadeOut('fast');
});

// if signup form is submitted, block it and submit via AJAX
$('form.signup-form').submit((e) => {
  ga('send', 'event', 'Users', 'New', 'Permanent');
  e.preventDefault();
  const form = `${$(this).serialize()}&note_text=${$('textarea.note-area').val()}`;
  $.ajax({
    url: 'users/create',
    type: 'POST',
    dataType: 'json',
    data: form,
    success: (data) => {
      log(data);
      log(data.success);
      if (data.success) {
        // hide screens we don't need and set href of "view note"
        $('#login-screen').fadeOut('fast');
        $('.login-button').hide();
        $('.view-note').show().attr('href', `?note=${data.insert_id}`);
        showSuccess('successfully created user and your first note', 3000);
      }
    },
    error: () => {
      ga('send', 'event', 'Notes', 'Error', 'Signup');
      log('error');
    },
  });
});

// password form
$('form.forgot-password-form').submit((event) => {
  event.preventDefault();
  const form = $(this).serialize();
  $.ajax({
    url: 'password/email',
    type: 'POST',
    dataType: 'json',
    data: form,
    statusCode: {
      200: (data) => {
        log(data);
        log(data.success);
        if (data.success) {
          // hide screens we don't need and set href of "view note"
          $('#login-screen').fadeOut('fast');
          showSuccess('password reset email sent', 3000);
        }
      },
      500: (data) => {
        ga('send', 'event', 'Notes', 'Error', 'Password Reset');
        log(data);
        log('error');
      },
    },
  });
  return false;
});

// click events for UI on add notes screen
$('.status-bar a.close').click(() => {
  hideSuccess('+ blank slate', 'slow');
});

// close the first time info screen
$('.info-close').click(() => {
  $('body').removeClass('menu-showing');
  $('textarea.note-area').focus();
});

// show info screen
$('.compose-info-button').click(() => {
  $('#info-screen').fadeIn('fast');
});

// hide info screen
$('.close-info').click(() => {
  $('#info-screen').fadeOut('fast');
});

$('#info-screen .overlay').click(() => {
  $('#info-screen').fadeOut('fast');
});

// show login screen
$('.login-button').click(() => {
  $('#login-screen').fadeIn('fast');
  $('.login-form').find('input[type=email], input[type=password]').val('');
  $('.login-form').show();
  $('.signup-form').hide();
  $('.login-form input[type=email]').focus();
});

// publish and copy share URL

$('.publish-note').mouseenter(() => {
  $('.top-left .message').hide();
  const url = getBaseURL();
  const shareID = $('body').attr('id');
  $('.top-left .share-url').text(`${url}?note=${shareID}`);
});

$('.publish-note').mouseleave(() => {
  $('.top-left .message').show();
  $('.top-left .share-url').text('');
});

$('.publish-note').click(() => {
  hideSuccess('+ blank slate', 'fast');
  if ($('body').attr('published') === 0) {
    publishNote();
  }
  showSuccess('Note published and share URL copied', 'slow');
});

// JS Event for login form
$('.login-form').submit(() => {
  ga('send', 'event', 'Users', 'Returning', 'Login');
});

// hide login screen
$('#login-screen .overlay').click(() => {
  $('#login-screen').fadeOut('fast');
});

$('.forgot-password-link').click(() => {
  $('.login-form').hide();
  $('.forgot-password-form').show();
  $('.forgot-password-form input[type="email"]').focus();
});

// GOOOOOOOGGGLLLE DOCS FUNCTIONALITY - SHOULD PROBABLY BE OWN FILE AND ONLY LOADED IF NEEDED

$('.save-google-doc-button').click(() => {
  saveGoogleDoc();
});

// PDF Functionality
$('.save-pdf-button').click(() => {
  savePDF();
});

if ($('.view-external-link').hasClass('google-doc')) {
  $('.view-external-link').text('google doc');
  log('has google doc class, show success');
  showSuccess('Google Doc saved!', 'slow');
}
