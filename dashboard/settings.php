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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" rel="stylesheet">
    <link href="assets/styling/Global.css" rel="stylesheet">
</head>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var notyf = new Notyf({position: {x: 'right', y: 'bottom'}});

        const licenseKeyInput = document.querySelector(".licenseKeyInput");
        const redeemLicenseButton = document.querySelector(".redeemLicenseButton");
        const resetHwidButton = document.querySelector(".resetHwidButton");
        const hwidInput = document.querySelector(".hwidInput");

        const UrlButton = document.querySelector(".UrlButton");
        const urlinput = document.querySelector(".urlinput");

        function redeemKey(key) {
            axios.post("dash/redeem-key", {
                key: key
            }).then(response => {
               if(response.data.success){
                    notyf.success(response.data.message);
                } else {
                    notyf.error(response.data.message);
                }
            }).catch(error => {
                notyf.error("An error occured while redeeming your license key.");
            })
        }

        function ResetHWID(key) {
            axios.post("dash/reset-hwid", {
                key: key
            }).then(response => {
               if(response.data.success){
                    notyf.success(response.data.message);
                } else {
                    notyf.error(response.data.message);
                }
            }).catch(error => {
                notyf.error("An error occured while reseting HWID.");
            })
        }

        function SetPfp(url) {
            axios.post("dash/profile-pic", {
                url: url
            }).then(response => {
               if(response.data.success){
                    notyf.success(response.data.message);
                } else {
                    notyf.error(response.data.message);
                }
            }).catch(error => {
                notyf.error("An error occured while setting profile pic.");
            })
        }

        redeemLicenseButton.addEventListener("click", rateLimit(function() {
            if (!licenseKeyInput.value.includes('-') || !licenseKeyInput.value.includes('VISION')) {
                notyf.error("Invalid license key format.");
                return;
            }
            
            redeemKey(licenseKeyInput.value);
        }, 5000));

        resetHwidButton.addEventListener("click", rateLimit(function() {
            if (hwidInput.value !== "<?php echo htmlspecialchars($_SESSION['username']); ?>") {
                notyf.error("HWID does not match the current user.");
                return;
            }
            ResetHWID(hwidInput.value);
        }, 5000));

        function isValidImageUrl(url) {
            return(url.match(/\.(jpeg|jpg|gif|png|webp)$/) != null);
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
                    notyf.error("Rate limit exceeded. Please wait before making another request.");
                }
            }
        }
        UrlButton.addEventListener("click", rateLimit(function() {
            if (isValidImageUrl(urlinput.value)) {
                SetPfp(urlinput.value);
            } else {
                notyf.error("Invalid Profile pic URL or image format.");
            }
        }, 5000));
    })
</script>

<?php
include "components/info/panel.php";
include "components/info/button.php";
include "components/info/textinput.php";
include "components/contentarea.php";
?>

<body>
    <?php
    $activePage = "settings";
    include("components/sidebar.php");
    ?>

    <?php openContentArea("Settings") ?>

        <?php
            openPanel("HWID");
                textInput("Enter Vision username", "hwidInput");
                button("Reset Hwid", "resetHwidButton");
            closePanel();
        ?>

        <?php
            openPanel("License Key");
                textInput("Enter License Key", "licenseKeyInput");
                button("Redeem License", "redeemLicenseButton");
            closePanel();

            openPanel("Profile Picture");
                textInput("Enter URL", "urlinput");
                button("Save", "UrlButton");
            closePanel();
        ?>

    <?php closeContentArea() ?>
</body>

</html>