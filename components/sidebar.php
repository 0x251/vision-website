<script>
    let componentsToCloseSelectors = [
        ".title", ".sidebar-title", ".sidebar-profile-name", ".sidebar", ".sidebar-profile-picture",
        ".sidebar-profile-container", ".sidebar-toggle-container", ".sidebar-component", ".sidebar-icon", ".sidebar-text"
    ]

    function closeComponents(elements) {
        elements.forEach(element => {
            element.classList.add("closed");
        });
    }

    function openComponents(elements) {
        elements.forEach(element => {
            element.classList.remove("closed");
        });
    }

    function selectTab(tabTitle) {
        tabTitle = tabTitle.toLowerCase();
        let tab = document.querySelector(`.${tabTitle}-tab`);

        if (tab == null) {
            error(`Tab ${tabTitle} does not exist`);
        }

        tab.classList.add("active");
    }

    document.addEventListener("DOMContentLoaded", function() {
        let componentsToClose = componentsToCloseSelectors.map(selector => document.querySelectorAll(selector));
        let sidebarToggle = document.querySelector(".sidebar-toggle-container");
        let sidebarOpen = true;

        function closeSidebar() {
            componentsToClose.forEach(component => {
                closeComponents(component);
            });
        }

        function openSidebar() {
            componentsToClose.forEach(component => {
                openComponents(component);
            });
        }

        function toggleSidebar() {
            if (sidebarOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }

            sidebarOpen = !sidebarOpen;
        }

        sidebarToggle.addEventListener("click", toggleSidebar);

        let screenWidth = window.innerWidth;
        if (screenWidth < 550) {
            closeSidebar();
            sidebarOpen = false;
        }
 
        let activePage = "<?php echo $activePage; ?>";
        
        if (activePage == "") {
            error("Active page is not set");
        }

        selectTab(activePage);
    });
</script>

<style>
    /* body,
    html {
        margin: 0;
        padding: 0;

        font-family: 'Roboto', sans-serif;
    }

    body {
        background: #18171c;
        color: #fbfbfc;
        display: flex;
    } */

    .sidebar {
        width: 250px;
        height: 100vh;
        background: #24222a;

        box-sizing: border-box;

        padding-left: 20px;
        padding-right: 20px;

        display: flex;
        flex-direction: column;

        transition: width 0.3s;
    }

    .sidebar.closed {
        width: 90px;
    }

    @media screen and (max-width: 550px) {
        .sidebar {
            width: 90px;
        }
    }

    .sidebar-toggle-container {
        position: absolute;

        height: 30px;
        width: 20px;

        top: 50%;
        left: 250px;

        transition: left 0.3s;
        cursor: pointer;
    }

    .sidebar-toggle {
        display: block;

        margin-left: 5px;

        height: 100%;
        width: 1px;
        background-color: #fbfbfc;
    }

    .sidebar-toggle-container.closed {
        left: 90px;
    }

    @media screen and (max-width: 550px) {
        .sidebar-toggle-container {
            display: none;
        }
    }

    /* Sidebar Header */

    .sidebar-header {
        display: flex;
        align-items: center;

        align-self: center;
        margin-right: auto;

        margin-top: 30px;
        margin-bottom: 20px;
    }


    .sidebar-title {
        font-size: 25px;
        font-weight: 700;
        margin: 0;
        margin-left: 10px;
    }

    .sidebar-title.closed {
        display: none;
    }

    @media screen and (max-width: 550px) {
        .sidebar-title {
            display: none;
        }
    }

    .sidebar-logo {
        width: 50px;
    }

    /* Seperator */

    .sidebar-seperator {
        width: 100%;
        height: 1px;
        background: #fbfbfc;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    /* Sidebar Component */

    .sidebar-component {
        display: flex;
        align-items: center;
        margin-top: 5px;
        margin-bottom: 5px;
        cursor: pointer;
        height: 40px;

        border-radius: 8px;
        padding-left: 12px;

        transition: background-color 0.3s;
        text-decoration: none;
        color: inherit;
    }

    .sidebar-component.active {
        background-color: #342e62;
    }

    .sidebar-component:hover {
        background-color: #342e62;
    }

    .sidebar-icon {
        width: 25px;
        margin-right: 10px;
    }

    .sidebar-text {
        font-size: 17px;
        font-weight: 700;
    }

    .sidebar-text.closed {
        display: none;
    }

    @media screen and (max-width: 550px) {
        .sidebar-text {
            display: none;
        }
    }

    /* Sidebar Footer */

    .sidebar-seperator-footer {
        margin-top: auto;
        margin-bottom: 10px;
    }

    .sidebar-profile-container {
        display: flex;
        align-items: center;
        margin-left: 10px;
        margin-bottom: 10px;
        height: 50px;
    }

    .sidebar-profile-container.closed {
        margin-left: 0;
    }

    @media screen and (max-width: 550px) {
        .sidebar-profile-container {
            margin-left: 0;
        }
    }

    .sidebar-profile-picture {
        width: 40px;
        border-radius: 50%;
        margin-right: 10px;
        transition: margin-right 0.3s;
    }

    .sidebar-profile-picture.closed {
        margin-right: 0;
        margin-left: 4px;
    }

    @media screen and (max-width: 550px) {
        .sidebar-profile-picture {
            margin-right: 0;
            margin-left: 4px;
        }
    }

    .sidebar-profile-name.closed {
        display: none;
    }

    @media screen and (max-width: 550px) {
        .sidebar-profile-name {
            display: none;
        }
    }
</style>

<?php

// function createSidebarComponent($iconPath, $text, $href)
// {
//     $textClass = strtolower($text);
//     return "
//         <div class=\"sidebar-component $textClass-tab\">
//             <img src=\"$iconPath\" class=\"sidebar-icon\">
//             <p class=\"sidebar-text\"> $text </p>
//         </div>
//     ";
// }

function createSidebarComponent($iconPath, $text, $href="#")
{
    $textClass = strtolower($text);
    return "
        <a class=\"sidebar-component $textClass-tab\" href=\"$href\">

            <img src=\"$iconPath\" class=\"sidebar-icon\">
            <p class=\"sidebar-text\"> $text </p>

        </a>
    ";
}

?>


<div class="sidebar">
    <div class="sidebar-toggle-container">
        <span class="sidebar-toggle"></span>
    </div>

    <div class="sidebar-header">
        <img src="assets/icons/logo.webp" class="sidebar-logo">
        <p class="sidebar-title"> Vision </p>

    </div>

    <span class="sidebar-seperator"></span>

    <?php echo createSidebarComponent("assets/icons/grid.svg", "Dashboard", "dash"); ?>
    <!-- <?php echo createSidebarComponent("assets/icons/user.svg", "Status", "status"); ?> -->
    <?php echo createSidebarComponent("assets/icons/settings.svg", "Settings", "settings"); ?>
    <?php echo createSidebarComponent("assets/icons/download-icon.svg", "Download", "download"); ?>
    <?php echo createSidebarComponent("assets/icons/logout.svg", "Logout", "#"); ?>
    <script>
        document.querySelector('.sidebar-component.logout-tab').addEventListener('click', function() {
            fetch('dash/logout', {
                method: 'GET'
            });
            window.location.href = 'login';
        });
    </script>

    <span class="sidebar-seperator sidebar-seperator-footer"></span>

    <div class="sidebar-profile-container">
        <img class="sidebar-profile-picture"></img>
        <p class="sidebar-profile-name"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('dash/profile')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    if (response.headers.get('content-type').includes('application/json')) {
                        return response.json(); 
                    }
                    return response.blob(); 
                })
                .then(data => {
                    if (data instanceof Blob) {
                        const imageUrl = URL.createObjectURL(data);
                        document.querySelector('.sidebar-profile-picture').src = imageUrl;
                    } else {
                        console.error('Error:', data.error);
                        const errorContainer = document.getElementById('image-error');
                        errorContainer.textContent = data.error;
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error.message);
                });
        });
    </script>
</div>

