<?php
    session_start();

    $startDate = clone $_SESSION['current_time'];
    print("<label for=week, class=week-label>Week " . $startDate->format('W') . " <br>");
    print($startDate->format('d/m') . " to ");
    $startDate->modify('friday this week');
    print($startDate->format('d/m') . "</label>");
?>