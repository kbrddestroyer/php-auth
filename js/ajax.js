async function setAjaxPostRequest(data)
{
    const response = await fetch("php/main.php", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json; charset=UTF-8"
          }
    });    
    return response.json();
}

function login(e)
{
    e.preventDefault();
    // The function defined LOGIN button controller
    // and sends POST request onto PHP script

    //var login = document.getElementById("login");
    //var password = document.getElementById("password");
    var login_form = document.forms.login_form;
    if (!login_form) throw (new Error("No required form found"));
    var login = login_form.elements.login;
    var password = login_form.elements.password;
    password.setCustomValidity(""); 
    
    if (!login_form.reportValidity()) return;
    
    data = {
        'type': 'login',
        'login': login.value, 
        'password': password.value
    }
    setAjaxPostRequest(data).then((data) => 
    {
        if (data['success'])
            location.href = "main.html";        // Redirect   
        else
        {
            switch (data['error'])
            {
                // Other tech. errors here:
                default: {
                    password.setCustomValidity("Неверный логин или пароль!");    
                    password.reportValidity();
                } break;
            }
        }
        //login_form.reportValidity();
    });
}

function register(e)
{
    e.preventDefault();
    // This function defines REGISTRATION button controller
    // and sends POST request onto PHP script

    //var login = document.getElementById("login");
    //var password = document.getElementById("password");
    //var confirm_password = document.getElementById("confirm_password");
    //var email = document.getElementById("email");
    //var name = document.getElementById("name");
    
    var registration_form = document.forms.registration;
    // Auth data
    var auth_fieldset = registration_form.elements.auth_fieldset;
    var login = auth_fieldset.elements.login;
    var password = auth_fieldset.elements.password;
    var confirm_password = auth_fieldset.elements.confirm_password;
    // Account additional data
    var additional_data = registration_form.elements.additional_data;
    var email = additional_data.elements.email;
    var name = additional_data.elements.name;
    login.setCustomValidity("");
    confirm_password.setCustomValidity("");
    if (!registration_form.reportValidity()) return;

    data = {
        'type': 'register',
        'login': login.value, 
        'password': password.value,
        'confirm_password': confirm_password.value, 
        'email': email.value,
        'name': name.value
    }
    setAjaxPostRequest(data).then((data) => {
        if (data['success'])
        {
            location.href = "main.html";
        }
        else
        {
            // Validate here
            switch (data['error'])
            {
                case 1:
                {
                    confirm_password.setCustomValidity("Пароли не совпадают!");
                    confirm_password.reportValidity();
                } break;
                case 2:
                {
                    login.setCustomValidity("Аккаунт уже существует!");
                    login.reportValidity();
                } break;
            }
        }
        return data;
    });
}

window.onload = function() {
    var _login = document.getElementById("submit_login");
    var _register = document.getElementById("submit_registration");
    if (_login)
        _login.addEventListener("click", login, true);
    if (_register)
        _register.addEventListener("click", register, true);
}