//from https://stackoverflow.com/questions/44933411/allow-only-letters-numbers-and-hyphen-in-input 
document.getElementById("input_1_3").addEventListener("submit", function(e) {
    // Get all inputs in the form we specifically are looking at, this selector can be
    // changed if this is supposed to be applied to specific inputs
    var inputs = document.querySelectorAll('#input_1_3 input');
    var forbiddenChars = /[^a-z\d\-]/ig;
    
    // check all the inputs we selected
    for(var input of inputs) {
        // Just in case the selector decides to do something weird
        if(!input) continue;
        
        // Check that there aren't any forbidden chars
        if(forbiddenChars.test(input.value)) {
            // This line is just to do something on a failure for Stackoverflow
            // I suggest removing this and doing something different in application
            console.log('Your subdomain name ' + input.name + ' has forbidden characters.');
            
            // Do stuff here

            // Prevent submit even propagation (don't submit)
            e.preventDefault();
            return false;
        }
    }
});