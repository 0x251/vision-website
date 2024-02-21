<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Panel</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<style>
    body {
        background: hsla(252, 40%, 29%, 1);
        background: linear-gradient(90deg, hsla(252, 40%, 29%, 1) 0%, rgb(107, 64, 151) 100%);

  background: -moz-linear-gradient(90deg, hsla(252, 40%, 29%, 1) 0%, rgb(55, 31, 78) 100%);

  background: -webkit-linear-gradient(90deg, hsla(252, 40%, 29%, 1) 0%, rgb(46, 25, 66) 100%);

        filter: progid: DXImageTransform.Microsoft.gradient( startColorstr="#392d69", endColorstr="#b57bee", GradientType=1 );

        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Roboto', sans-serif;
        color: #fff;
    }
    .login-container {
        width: 100%;
        max-width: 400px;
        margin: 20px;
        padding: 40px;
        background: linear-gradient(135deg, #000000 0%, #131111 100%);
        backdrop-filter: blur(10px); /* Blurry effect */
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        text-align: center;
        border: 1px solid #6f42c1; /* Optional: light border for better contrast */
    }
    .logo {
        width: 150px;
        margin: 0 auto 30px;
        display: block;
    }
    .login-input {
        width: calc(100% - 40px);
        padding: 10px 20px;
        margin-bottom: 1rem;
        border-radius: 8px;
        border: 2px solid transparent;
        background-color: #000; /* Black input field */
        color: #fff;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    .login-input:focus {
        outline: none;
        border-color: #6f42c1; /* Purple ring on focus */
    }
    .login-button {
        width: calc(100% - 20px);
        height: 40px;
        padding: 10px 20px;
        border: none;
        margin-top: 2px;
        border-radius: 8px;
        background-color: #6f42c1;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 300ms ease-in-out;
    }
    .login-button:hover {
        background-color: #5936a2;
    }

    .register-button {
        width: calc(100% - 20px);
        height: 40px;
        padding: 10px 20px;
        border: none;
        margin-top: 12px;
        border-radius: 8px;
        background-color: #6f42c1;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 300ms ease-in-out;
    }
    .register-button:hover {
        background-color: #5936a2;
    }
</style>
<script src="https://cdn.jsdelivr.net/gh/scottschiller/Snowstorm@master/snowstorm-min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
</head>
<body>

<div class="login-container">
    <img src="https://media.discordapp.net/attachments/1183410827521425459/1186825647516033064/image-255.png?ex=6594a884&is=65823384&hm=eb9cce540cf37bd1ea7e6038d5797d0edb8dfeec0dd5e9be888f8396da7170f3&=&format=webp&quality=lossless&width=628&height=473" alt="Company Logo" class="logo">
    <h1>Login</h1>
    <form id="loginForm">
        <input type="text" id="username" placeholder="Username" class="login-input" required>
        <input type="password" id="password" placeholder="Password" class="login-input" required>
        <button type="submit" class="login-button">Log In</button>
    </form>
    <a href="register"><button class="register-button">Register</button></a>
</div>

<script>
  window.onload = function() {
    // If you want to customize snowStorm properties, do it here.
    snowStorm.snowColor = '#fff'; // White snowflakes
    // ...other configurations if needed
  };

  $(document).ready(function(){
    $("#loginForm").on('submit', function(e){
      e.preventDefault();
      var username = $("#username").val();
      if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        var notyf = new Notyf({position: {x: 'right', y: 'top'}});
        notyf.error('Invalid characters in username');
        return;
      }

      axios.post("api/login-auth.php", 
        { 
          username: username, 
          password: $("#password").val() 
        })
        .then(function (response) {
          
          var notyf = new Notyf({position: {x: 'right', y: 'top'}});
          if(response.data.success){
            notyf.success('Login successful');
            setTimeout(function(){
              window.location.href = "dash";
            }, 2000);
          } else {
            notyf.error(response.data.message);
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    });
  });
</script>


</body>
</html>