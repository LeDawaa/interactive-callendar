<?php
    session_start();

    print("<label for=week, class=week-label>Semaine du <br>");
    $startDate = clone $_SESSION['current_time'];
    print($startDate->format('d/m') . " au ");
    $startDate->modify('friday this week');
    print($startDate->format('d/m') . "</label>");
?>