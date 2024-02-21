<style>
    .paragraph {
        color: #c0b4db;
        margin-top: 10px;
        margin-bottom: 10px;
    }
</style>

<?php
    // <input class=\"settings-text-input\" placeholder=\"$placeholder\">
    function paragraph($text, $class="") {
        echo "
            <p class=\"paragraph $class\">$text</p>
        ";
    }
?>