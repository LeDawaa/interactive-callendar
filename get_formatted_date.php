<?php
    if (isset($_GET['day'])) {
        $day = $_GET['day'];
        $dateTime = new DateTime($day);
        echo $dateTime->format('l, d F Y');
    }
?>