<style>
    .settings-container-wrapper {
        display: flex;
        flex-direction: column;

        max-width: 400px;
        width: 100%;
        height: max-content;

        margin: 20px;
        padding-left: 20px;
        padding-right: 20px;
    }

    @media screen and (max-width: 550px) {
        .settings-container-wrapper {
            margin: 0;
            width: 80%;
        }

        
    }

    .settings-title {
        font-size: 20px;
        font-weight: 700;
        margin-left: 5px;
        margin-bottom: 10px;
        font-family: 'Roboto', sans-serif;
    }

    .settings-container {
        display: flex;
        flex-direction: column;

        width: 100%;
        height: 100%;

        border: 1px solid #3b3945;
        background-color: #24222a;
        border-radius: 8px;
        padding: 10px;
    }
</style>

<?php
function openPanel($title)
{
    echo "
            <div class=\"settings-container-wrapper\">
                <p class=\"settings-title\">
                    $title
                </p>

                <div class=\"settings-container\">
        ";
}

function closePanel()
{
    echo "
                </div>
            </div>
        ";
}
?>