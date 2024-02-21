<style>
    .settings-button {
        border: 1px solid #3b3945;
        background-color: #473f85;
        border-radius: 5px;

        margin-top: 10px;
        margin-bottom: 10px;
        height: 30px;

        padding-left: 10px;
        color: #fbfbfc;

        cursor: pointer;

        transition: background-color 0.3s ease-in-out;
    }

    .settings-button:hover {
        background-color: #37306e;
    }
</style>

<?php
    function button($buttonText, $class) {
        echo "
            <button class=\"settings-button $class\">
                $buttonText
            </button>
        ";
    }
?>