<style>
.settings-text-input {
    border: 1px solid #3b3945;
    background-color: #24222a;
    border-radius: 5px;

    margin-top: 10px;
    margin-bottom: 10px;
    height: 30px;

    padding-left: 10px;
    color: #fbfbfc;
}


</style>

<?php
    // <input class=\"settings-text-input\" placeholder=\"$placeholder\">
    function textInput($placeholder, $class) {
        echo "
            <input class=\"settings-text-input $class\" placeholder=\"$placeholder\">
        ";
    }
?>