createUrlDiv();
document.getElementsByName("input_3")[0].addEventListener("blur", textSuccess);
modalDupMessage();

//from https://stackoverflow.com/questions/44933411/allow-only-letters-numbers-and-hyphen-in-input 
//document.getElementById("input_1_3").addEventListener("input", function(e) {
document.getElementsByName("input_3")[0].addEventListener("input", function(e) {
  
    // Get all inputs in the form we specifically are looking at, this selector can be
    // changed if this is supposed to be applied to specific inputs
    var inputs = document.getElementsByName('input_3');
    var forbiddenChars = /[^a-z\d\-]/ig;
    
    // check all the inputs we selected
    for(var input of inputs) {
        // Just in case the selector decides to do something weird
        if(!input) continue;
        
        // Check that there aren't any forbidden chars
        if(forbiddenChars.test(input.value)) {
          textFail();
            alert('Your site name had forbidden characters. Please only use lowercase letters, numbers or hyphens.');
            input.value = input.value.replace(forbiddenChars,'');//remove that bad character                    
            return false;
        } else {
           textSuccess();
        }
    }
  document.getElementById('domain-name').innerHTML =  input.value + '.opened.ca';
  
});

function createUrlDiv(){
  const fieldId = document.getElementsByName("input_3")[0].parentNode;
  const domain = document.createElement("div");
  console.log(fieldId.appendChild(domain));
  domain.setAttribute("id", "domain-name");
  domain.innerHTML = 'YOUR_DOMAIN_NAME.opened.ca';
}


function textSuccess(){
  const domainName = document.getElementById('domain-name');
  // if (domainName.className === "success"){
  //   domainName.setAttribute("class", "");
  // } else {
    domainName.setAttribute("class", "success");  
  //}
}

function textFail(){
    const domainName = document.getElementById('domain-name');
    domainName.classList.remove('success'); 
}


//set modal message in clone-details.php at the bottom via php
function modalDupMessage(){
  const modal = document.getElementById('cloneModal')
  const button = document.querySelectorAll('.gform_button')[0]
  button.onclick = function() {
    modal.style.display = "block";
  }
}
