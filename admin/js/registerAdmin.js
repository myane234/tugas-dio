const passwordI = document.getElementById('password');


function showPassword() {
    if(passwordI.type === 'password') {
        passwordI.type = 'text'
    } else {
        passwordI.type = 'password'
    }
}