<?php
  session_start();

  if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
      case 'logout':
        $_SESSION['loggedin'] = false;
        echo 'success logout';
        break;
      case 'changeWeek':
        $value = $_POST['value'] ?? null;
        if ($value === 'next' || $value === 'previous') {
          $_SESSION['current_time']->modify(($value === 'next') ? '+1 week' : '-1 week');
          echo 'success changeWeek';
        }
        break;
    }
  }
?>