<style>
    .content-area {
        flex: 1;
        height: 100vh;
    }

    .info-area {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;

        width: 100%;
        height: min-content;
    }

    .content-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        margin-top: 20px;
        margin-bottom: 20px;
    }
</style>

<?php
function openContentArea($title)
{
    echo "<div class=\"content-area\">
            <p class=\"content-title\">
                $title
            </p>

            <div class=\"info-area\">

        ";
}

function closeContentArea()
{
    echo "
            </div>
        </div>
        ";
}

?>