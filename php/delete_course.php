<?php

    session_start();
    
    $id = $_POST['id'];

    $week = json_decode(file_get_contents('../db/week.json'), true);

    foreach ($week[2] as &$time_slot) {
        foreach ($time_slot as $key => $item) {
            if ($item["id"] == $id) {
                array_splice($time_slot, $key, 1);
            }
        }
    }
    
    file_put_contents('../db/week.json', json_encode($week, JSON_PRETTY_PRINT));
    echo "Séance supprimée avec succès";
    return;
?>