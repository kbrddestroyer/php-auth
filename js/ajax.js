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
            location.href = "main.html"; 
        else
        {
            switch (data['error'])
            {
                default: {
                    password.setCustomValidity("Неверный логин или пароль!");    
                    password.reportValidity();
                } break;
            }
        }
    });
}

function register(e)
{
    e.preventDefault();
    // This function defines REGISTRATION button controller
    // and sends POST request onto PHP script

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
    password.setCustomValidity("");
    login.setCustomValidity("");
    confirm_password.setCustomValidity("");
    email.setCustomValidity("");
    name.setCustomValidity("");
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
                case 4:
                {
                    password.setCustomValidity("Пароль должен содержать только буквы и цифры");
                    password.reportValidity();
                } break;
                case 5:
                {
                    login.setCustomValidity("Логин не должен содержать пробелы");
                    login.reportValidity();
                } break;
                case 6:
                {
                    password.setCustomValidity("Пароль не должен содержать пробелы");
                    password.reportValidity();
                } break;
                case 7:
                {
                    confirm_password.setCustomValidity("Пароли не совпадают!");
                    confirm_password.reportValidity();
                } break;
                case 8:
                {
                    email.setCustomValidity("Доменная часть не задана или задана неверно");
                    email.reportValidity();
                } break;
                case 9:
                {
                    name.setCustomValidity("ФИО введено некорректно");
                    name.reportValidity();
                } break;
                case 11:
                {
                    login.setCustomValidity("Аккаунт уже существует!");
                    login.reportValidity();
                } break;
                case 12:
                {
                    email.setCustomValidity("Аккаунт уже существует!");
                    email.reportValidity();
                } break;
            }
        }
        return data;
    });
}

function logout(e)
{
    e.preventDefault();
    data = {
        'type': 'logout'
    };
    setAjaxPostRequest(data).then((data) => {
        if (data['success'])
        {
            location.href = 'index.html';
        }    
    });
}

function checkLogin()
{
    data = {
        'type': 'check_auth'
    }
    setAjaxPostRequest(data).then((data) => 
    {
        if (!data['success'])
            location.href = 'index.html';
        var greetings = document.getElementById("greetings");
        greetings.innerHTML = data['name'];
    });
    
}

window.onload = function() {
    var _login = document.getElementById("submit_login");
    var _register = document.getElementById("submit_registration");
    var _exit = document.getElementById("submit_exit");
    if (_login)
        _login.addEventListener("click", login, true);
    if (_register)
        _register.addEventListener("click", register, true);
    if (_exit)
    {
        _exit.addEventListener("click", logout, true);
        checkLogin();
    }
}