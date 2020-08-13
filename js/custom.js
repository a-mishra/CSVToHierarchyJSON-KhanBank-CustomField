document.getElementById("uploadBtn").onchange = function () {
    // alert('file uploaded' + this.files[0].name);
    document.getElementById("uploadFile").value = this.files[0].name;
};


function copyGeneratedJSON() {
    /* Get the text field */
    var copyText = document.getElementById("generatedJSON");
  
    /* Select the text field */
    copyText.select();
    // copyText.setSelectionRange(0, 99999); /*For mobile devices*/
  
    /* Copy the text inside the text field */
    document.execCommand("copy");
  
    /* Alert the copied text */
    // alert("Text Copied");
    // console.log(copyText.value)
  }