function login() {
    let pass = _("password").value
    var formdata = new FormData()
    formdata.append("userId", _("userId").value)
    formdata.append("pass", pass)
    var ajaxLogin = new XMLHttpRequest()
    ajaxLogin.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var userDetails = JSON.parse(this.responseText)
            if (userDetails.success == true) {
                _("loginName").innerHTML = userDetails.displayName
                $('#loginModal').modal('hide')
            } else {
                _("loginError").innerHTML = "<i class=\"far fa-exclamation-triangle\"></i>" + userDetails.message
            }
        }
    }
    ajaxLogin.open("POST", "/user/login/");
    ajaxLogin.send(formdata);
}

function navigateTo(url) {
    window.location.href = url;
}