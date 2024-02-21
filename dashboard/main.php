<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION["login"])){
    header('Location: login');
    exit;
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">

    <link href="assets/styling/Global.css" rel="stylesheet">
</head>

<?php
    include "components/info/panel.php";
    include "components/info/button.php";
    include "components/info/textinput.php";
    include "components/info/paragraph.php";
    include "components/contentarea.php";
?>

<style>
    .auth-password {
        filter: blur(5px);
        transition: filter 0.1s;
    }

    .auth-password:hover {
        filter: blur(0px);
    }
    
    

    .auth-password .copy-button {
        position: absolute;
        right: 0;
    }


</style>

<body>

    <?php
        $activePage = "dashboard";
        include("components/sidebar.php")
    ?>

            <div class="content-area">
            <p class="content-title">
                Vision Dashboard
            </p>

            <div class="info-area">
                <div class="settings-container-wrapper">
                    <p class="settings-title">
                        Username
                    </p>

                    <div class="settings-container">
                        <p class="paragraph"><?php echo strip_tags(htmlspecialchars($_SESSION['username'])); ?></p>
                    </div>
                </div>

                <div class="settings-container-wrapper">
                    <p class="settings-title">
                    Expires
                    </p>

                    <div class="settings-container">
                        <p class="paragraph" id="timeleft">Not available</p>
                    </div>
                </div>

                <div class="settings-container-wrapper">
                    <p class="settings-title">
                    Status
                    </p>

                    <div class="settings-container">
                        <p class="paragraph" id="internal">Not available</p>
                        <p class="paragraph" id="external">Not available</p>
                    </div>
                </div>

                <div class="settings-container-wrapper">
                    <p class="settings-title">
                    Auth Username
                    </p>

                    <div class="settings-container">
                        <p class="paragraph" id="auth_username">Not available</p>
                    </div>
                </div>
                <div class="settings-container-wrapper">
                    <p class="settings-title">
                    Auth Password
                    </p>
                    <div class="settings-container">
                        <p class="paragraph" id="auth_password">Not available</p>
                        
                    </div>
                </div>
            </div>


</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>
const eventSource = new EventSource('dash/check');

eventSource.onmessage = function(event) {
    
    const data = JSON.parse(event.data);

    if (!data.success) {
            if(document.getElementById('timeleft')) document.getElementById('timeleft').innerHTML = '<span style="color: #ff6666;">Not available</span>';
            if(document.getElementById('status')) document.getElementById('status').innerHTML = '<span style="color: #ff6666;">Not Active</span>';
            if(document.getElementById('auth_username')) document.getElementById('auth_username').innerHTML = '<span style="color: #ff6666;">Not available</span>';
            if(document.getElementById('auth_password')) document.getElementById('auth_password').innerHTML = '<span style="color: #ff6666;">Not available</span>';
            if(document.getElementById('internal')) document.getElementById('internal').innerHTML = 'Internal • <span style="color: #ff6666;">Not Active</span>';
            document.getElementById('external').innerHTML = 'External • <span style="color: #ff6666;">Not Active</span>';
        } else {
            if (document.getElementById('timeleft')) {
                
                var endTime = data.timeleft;

                var countdownInterval = setInterval(function() {
                    var now = Math.floor(Date.now() / 1000);
                    var timeLeftInSeconds = endTime - now;

                    
                    if (timeLeftInSeconds <= 0) {
                        clearInterval(countdownInterval);
                        document.getElementById('timeleft').innerHTML = 'Expired';
                        return;
                    }

                    var daysLeft = Math.floor(timeLeftInSeconds / (3600 * 24));
                    var hoursLeft = Math.floor((timeLeftInSeconds % (3600 * 24)) / 3600);
                    var minutesLeft = Math.floor((timeLeftInSeconds % 3600) / 60);
                    var secondsLeft = timeLeftInSeconds % 60;

                    document.getElementById('timeleft').innerHTML =
                        '<span>' +
                            '<strong>' + daysLeft + '</strong> days, ' +
                            '<strong>' + hoursLeft + '</strong> hours, ' +
                            '<strong>' + minutesLeft + '</strong> minutes, ' +
                            '<strong>' + secondsLeft + '</strong> seconds left' +
                        '</span>';
                }, 1000); 
            }
            if(document.getElementById('status')) document.getElementById('status').innerHTML = '<span style="color: #4CAF50;">Active</span>';
            if(document.getElementById('auth_username')) document.getElementById('auth_username').innerHTML = '<strong><span style="color: #663399;">' + data.auth_username + '</span></strong>';
            if(document.getElementById('auth_password')) document.getElementById('auth_password').innerHTML = '<strong><span style="color: #663399;" class="auth-password">' + data.auth_password + '</span></strong>';
            if(data.level == 'Internal') {
                document.getElementById('internal').innerHTML = 'Internal • <strong><span style="color: #00cc66;">Active</span></strong>';
            } else {
                document.getElementById('internal').innerHTML = 'Internal • <span style="color: #ff6666;">Not Active</span>';
            }

            if (data.level != "External"){
                document.getElementById('external').innerHTML = 'External • <span style="color: #ff6666;">Not Active</span>';
            } else {
                document.getElementById('external').innerHTML = 'External • <strong><span style="color: #00cc66;">Active</span></strong>';
            }
    }
}


eventSource.onerror = function(error) {
        console.error('Error:', error);
        
        eventSource.close();
};

window.addEventListener("beforeunload", function() {
    if (eventSource) {
        eventSource.close();
    }
});
</script>


</html>