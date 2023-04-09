<?php

session_start();

[$id, $date, $start_hour, $start_min, $end_hour, $end_min, $type, $group, $matiere, $enseignant, $salle] = $_POST['data'];

$week = json_decode(file_get_contents('week.json'), true);

$start_time = new DateTime($start_hour . ':' . $start_min);
$end_time = new DateTime($end_hour . ':' . $end_min);
$interval = new DateInterval('PT15M');
$current_time = clone $start_time;
$week[3] += ($id == "-1");

while ($current_time < $end_time) {
    $index = ltrim($current_time->format('H\hi'), "0");

    $is_time_slot_taken = array_filter($week[2][$index], function ($value) use ($date, $group) {
        return ($value["date"] == $date && ($value["groupe"] == $group || $value["groupe"] == "All"));
    });

    if (!empty($is_time_slot_taken)) {
        echo "Erreur: une séance est déjà planifiée à cette heure";
        return;
    }

    array_push($week[2][$index], [
        "id" => ($id != "-1") ? $id : strval($week[3]),
        "type" => $type,
        "matiere" => $matiere,
        "enseignant" => $enseignant,
        "salle" => $salle,
        "date" => $date,
        "groupe" => ($type === 'Cours') ? "All" : $group,
        "duree" => ($current_time == $start_time) ? strval(($end_hour - $start_hour) * 4 + ($end_min - $start_min) / 15) : "-1"
    ]);

    $current_time->add($interval);
}

file_put_contents('week.json', json_encode($week, JSON_PRETTY_PRINT));
echo "Séance planifiée avec succès";
return;

?>