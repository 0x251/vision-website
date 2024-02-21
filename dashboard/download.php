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
    <title>Dashboard - Download</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" rel="stylesheet">

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

.settings-button {
        border: 1px solid #3b3945;
        background-color: #473f85;
        border-radius: 5px;

        margin-top: 10px;
        margin-bottom: 10px;
        height: 40px;

        padding-left: 10px;
        color: #fbfbfc;

        cursor: pointer;

        transition: background-color 0.3s ease-in-out;
    }
    .settings-title {
        font-size: 20px;
        font-weight: 700;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 10px;
        font-family: 'Roboto', sans-serif;
    }

    .settings-button:hover {
        background-color: #37306e;
    }


</style>



<body>

    <?php
        $activePage = "download";
        include("components/sidebar.php")
    ?>

<?php openContentArea("Vision Downloads") ?>

<?php
    openPanel("External Download");
    #closePanel();
?>
    <p class="paragraph" id="External">Not available</p>
<?php
button("External Download", "ExternalDownload");
closePanel(); ?>
<?php
    openPanel("Internal Download");
        
?>
    <p class="paragraph" id="Internal">Not available</p>
<?php
button("Internal Download", "InternalDownload");
closePanel(); ?>

<?php closeContentArea() ?>


</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var notyf = new Notyf({position: {x: 'right', y: 'bottom'}});

    const InternalDownload = document.querySelector(".InternalDownload");
    const ExternalDownload = document.querySelector(".ExternalDownload");

    function Download(key) {
        fetch("dash/download", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                file_key: key
            })
        })
        .then(response => {
            if (!response.ok) {
                
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Unknown error occurred');
                });
            }

            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json(); 
            } else {
                return response.blob();
            }
        })
        .then(data => {
            console.log(data.success)
            if (data.success === false) {
                notyf.error(data.message);
                return; 
            } else if (data instanceof Blob) {
                const url = window.URL.createObjectURL(data);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = "vision_" + Math.random().toString(36).substr(2, 9) + ".zip"; 
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                notyf.success('Downloaded Vision Binary!');
            } else {
                notyf.error('Failed to Download Vision Binary!');
            }
        })
        .catch(error => {
            
            notyf.error("Please contact vision support with this error: " + error.message);
        });
    }
    function rateLimit(fn, delay) {
        let waiting = false;
        let requestCount = 0;
        return function () {
            if (!waiting && requestCount < 3) {
                fn.apply(this, arguments);
                waiting = true;
                requestCount++;
                setTimeout(function () {
                    waiting = false;
                    requestCount = 0;
                }, delay);
            } else {
                notyf.error("Rate limit exceeded. Please wait before making another request to vision endpoint.");
            }
        }
    }

    InternalDownload.addEventListener("click", rateLimit(function() {
        Download("Internal");
    }, 5000));

    ExternalDownload.addEventListener("click", rateLimit(function() {
        Download("External");
    }, 5000));
});


const eventSource = new EventSource('dash/check');

eventSource.onmessage = function(event) {
    
    const data = JSON.parse(event.data);

    if (!data.success) {
            if(document.getElementById('Internal')) document.getElementById('Internal').innerHTML = '<span style="color: #ff6666;">Not available</span>';
            if(document.getElementById('External')) document.getElementById('External').innerHTML = '<span style="color: #ff6666;">Not available</span>';
        } else {
            if (data.level == "Internal"){
                if (document.getElementById('Internal')) {
                    
                    var endTime = data.timeleft;

                    var countdownInterval = setInterval(function() {
                        var now = Math.floor(Date.now() / 1000);
                        var timeLeftInSeconds = endTime - now;

                        
                        if (timeLeftInSeconds <= 0) {
                            clearInterval(countdownInterval);
                            document.getElementById('Internal').innerHTML = 'Expired';
                            return;
                        }

                        var daysLeft = Math.floor(timeLeftInSeconds / (3600 * 24));
                        var hoursLeft = Math.floor((timeLeftInSeconds % (3600 * 24)) / 3600);
                        var minutesLeft = Math.floor((timeLeftInSeconds % 3600) / 60);
                        var secondsLeft = timeLeftInSeconds % 60;

                        document.getElementById('Internal').innerHTML =
                            '<span>' +
                                '<strong>' + daysLeft + '</strong> days, ' +
                                '<strong>' + hoursLeft + '</strong> hours, ' +
                                '<strong>' + minutesLeft + '</strong> minutes, ' +
                                '<strong>' + secondsLeft + '</strong> seconds left' +
                            '</span>';
                    }, 1000); 
                }
            }
            else {
                document.getElementById('Internal').innerHTML = '<span style="color: #ff6666;">Not Active</span>';
                document.querySelector('.InternalDownload').disabled = true;
            }

            if (data.level != "External"){
                document.getElementById('External').innerHTML = '<span style="color: #ff6666;">Not Active</span>';
                document.querySelector('.ExternalDownload').disabled = true;
                
            } else {
                if(document.getElementById('External')) {
                    var endTime = data.timeleft;

                    var countdownInterval = setInterval(function() {
                        var now = Math.floor(Date.now() / 1000);
                        var timeLeftInSeconds = endTime - now;

                        
                        if (timeLeftInSeconds <= 0) {
                            clearInterval(countdownInterval);
                            document.getElementById('External').innerHTML = 'Expired';
                            return;
                        }

                        var daysLeft = Math.floor(timeLeftInSeconds / (3600 * 24));
                        var hoursLeft = Math.floor((timeLeftInSeconds % (3600 * 24)) / 3600);
                        var minutesLeft = Math.floor((timeLeftInSeconds % 3600) / 60);
                        var secondsLeft = timeLeftInSeconds % 60;

                        document.getElementById('External').innerHTML =
                            '<span>' +
                                '<strong>' + daysLeft + '</strong> days, ' +
                                '<strong>' + hoursLeft + '</strong> hours, ' +
                                '<strong>' + minutesLeft + '</strong> minutes, ' +
                                '<strong>' + secondsLeft + '</strong> seconds left' +
                            '</span>';
                    }, 1000); 
                }
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