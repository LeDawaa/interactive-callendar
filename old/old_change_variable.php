<?php
    session_start();

    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'logout') {
            $_SESSION['loggedin'] = false;
            echo 'success logout';
        } else if ($_POST['action'] == 'changeWeek') {
            if (isset($_POST['value'])) {
                if ($_POST['value'] == 'next' || $_POST['value'] == 'previous') {
                    $sign = $_POST['value'] == 'next' ? '+' : '-';
                    $_SESSION['current_time']->modify($sign . '1 week');
                    echo 'success changeWeek';
                }
            }
        }
    }
?>