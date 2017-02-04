var footnoteChars = ['º','¹','²','³','⁴','⁵','⁶','⁷','⁸','⁹'];

function splitValue(value, index1, index2) {
  return [value.substring(0, index1), value.substring(index2)];
}

function addMarkdownChars(chars) {
  // set up requires we find where the user is in the documents and what the content is, split it up so we can use it
  const ta = document.getElementsByTagName('textarea')[0];
  let content = ta.value;
  // const currentLineNumber = content.substr(0, ta.selectionStart).split('\n').length;
  const currentCharNumber = ta.selectionStart;
  const currentCharNumberEnd = ta.selectionEnd;
  const currentSelection = window.getSelection();
  const splitContent = splitValue(content, currentCharNumber, currentCharNumberEnd);
  log(splitContent);
  // the content should be the start of the original content, the new characters, plus the end of the original content
  // if there are 2 characters, wrap selection
  if (chars.length === 2) {
    content = splitContent[0] + chars[0] + currentSelection + chars[1] + splitContent[1];
  } else {
    // else it's just a character to insert
    content = splitContent[0] + chars[0] + splitContent[1];
  }
  // re-fill the textarea with the content and focus the cursor so the user can continue typing
  $('textarea').val(content);
  ta.focus();
  ta.selectionStart = currentCharNumberEnd + chars[0].length;
  ta.selectionEnd = currentCharNumberEnd + chars[0].length;
}

function bold() {
  addMarkdownChars(['**', '**']);
}

function italics() {
  addMarkdownChars(['*', '*']);
}

function strikeThrough() {
  addMarkdownChars(['~', '~']);
}

$(document).keydown((e) => {
  // SAVE: Behavior for Control/Command S - SAVE
  if ((e.metaKey === true || e.ctrlKey === true) && e.keyCode === 83) {
    const params = ['note saved!', 'slow'];
    e.preventDefault();
    saveNote(showSuccess, params);
  } else if ((e.metaKey === true || e.ctrlKey === true) && e.keyCode === 71) {
    e.preventDefault();
    saveGoogleDoc();
  } else if ((e.metaKey === true || e.ctrlKey === true) && e.keyCode === 80) {
    e.preventDefault();
    savePDF();
  } else if ((e.metaKey === true || e.ctrlKey === true) && e.shiftKey && e.keyCode === 86) {
    // toggle showing edit / show output mode. Not ergonomic to me.
    e.preventDefault();
    toggleShowOutput();
  } else if (e.ctrlKey === true && (e.keyCode >= 48 && e.keyCode <= 57)) {
      // footnotes
    e.preventDefault();
    addMarkdownChars(footnoteChars[String.fromCharCode(e.keyCode)]);
  } else if ((e.metaKey === true || e.ctrlKey === true) && e.keyCode === 66) {
    e.preventDefault();
    bold();
  } else if ((e.metaKey === true || e.ctrlKey === true) && e.keyCode === 73) {
    e.preventDefault();
    italics();
  }
    // if this is a first time user that has typed more than a few lettesr
  if ($('body').hasClass('firsttime') && $('textarea.note-area').val().length > 2) {
    $('body').addClass('menu-showing');
    $('body').removeClass('firsttime');
  } else if (e.metaKey === true && e.keyCode === 66) {
    e.preventDefault();
  }
  if ($('.success').is(':visible') && e.keyCode !== 91) {
    hideSuccess('+ blank slate', 'slow');
  }
});
