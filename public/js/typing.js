// prevent tab functionality
$("textarea").keydown(function(e) {
  var $this, end, start;
  var listArray = ["-","*"];
  if (e.keyCode === 9) {
      
    // TAB key works by adding a tab character 
    if(e.shiftKey) {
        tabBack(this);
        return false;
    }else {
        addTab(this);
        return false;
    }        
    
  } /*
else if(e.keyCode === 13) {

        // ENTER key for list continuation - this is a little messy and double code from above
        var content = this.value;
        var lastLine = content.substr(content.lastIndexOf("\n")+1);
        var firstCharLastLine = $.trim(lastLine)[0];
        var beforeSpace = lastLine.split(firstCharLastLine)[0];
        log(lastLine);      
        if($.inArray(firstCharLastLine, listArray) > -1) {
            log(beforeSpace);
            var s = $(this).val();
            $(this).val(s+"\n"+beforeSpace+firstCharLastLine+" ");
            return false;
        }
    }
*/
});

function addTab(textArea) {
    var listArray = ["-","*"];
    var content = textArea.value;
    var lastLine = content.substr(content.lastIndexOf("\n")+1);
    var firstCharLastLine = $.trim(lastLine)[0]; 
    var numberOfLines = content.split(/\r|\r\n|\n/).length; 
    if($.inArray(firstCharLastLine, listArray) > -1) {
        var lineBreakChar = numberOfLines === 1 ? "" : "\n";
        $(textArea).val(content.substring(0, content.lastIndexOf("\n")) + lineBreakChar + "\t" + lastLine);
    }else {
        start = textArea.selectionStart;
        end = textArea.selectionEnd;
        $this = $(textArea);
        $this.val($this.val().substring(0, start) + "\t" + $this.val().substring(end));
        textArea.selectionStart = textArea.selectionEnd = start + 1;         
    } 
}

function tabBack(textArea) {
    var content = textArea.value;
    var lastLine = content.substr(content.lastIndexOf("\n")+1);
    var firstCharLastLineTrim = $.trim(lastLine)[0];
    var firstCharLastLine = lastLine[0];  
    var numberOfLines = content.split(/\r|\r\n|\n/).length;   
    if(firstCharLastLine === "\t") {
        var lineBreakChar = numberOfLines === 1 ? "" : "\n";
        $(textArea).val(content.substring(0, content.lastIndexOf("\n")) + lineBreakChar + lastLine.substr(1));
    }
}