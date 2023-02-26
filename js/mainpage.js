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
