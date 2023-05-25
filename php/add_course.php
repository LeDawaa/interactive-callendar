<?php
    session_start();

    [$id, $date, $start_hour, $start_min, $end_hour, $end_min, $type, $group, $matiere, $enseignant, $salle, $repeat] = $_POST['data'];
    $week = json_decode(file_get_contents('../db/week.json'), true);

    $start_time = new DateTime($start_hour . ':' . $start_min);
    $end_time = new DateTime($end_hour . ':' . $end_min);
    $interval = new DateInterval('PT15M');
    $current_time = clone $start_time;
    $ind = $week[3];
    $week[3] += ($id == "-1") * intval($repeat);

    while ($current_time < $end_time) {
        $day = new DateTime(date("Y-m-d", strtotime($date)));
        for ($i = 1; $i <= intval($repeat); $i++) {
            $index = ltrim($current_time->format('H\hi'), "0");

            $is_time_slot_taken = array_filter($week[2][$index], function ($value) use ($day, $group) {
                return ($value["date"] == $day->format('Y-m-d') && ($value["groupe"] == $group || $value["groupe"] == "All"));
            });

            if (!empty($is_time_slot_taken)) {
                echo "Erreur: une séance est déjà planifiée à cette heure";
                return;
            }

            array_push($week[2][$index], [
                "id" => ($id != "-1") ? $id : strval($ind + $i),
                "type" => $type,
                "matiere" => $matiere,
                "enseignant" => $enseignant,
                "salle" => $salle,
                "date" => $day->format('Y-m-d'),
                "groupe" => ($type === 'Cours') ? "All" : $group,
                "duree" => ($current_time == $start_time) ? strval(($end_hour - $start_hour) * 4 + ($end_min - $start_min) / 15) : "-1"
            ]);

            $day->modify('+1 week');
        } $current_time->add($interval);
    }

    file_put_contents('../db/week.json', json_encode($week, JSON_PRETTY_PRINT));
    echo "Séance planifiée avec succès";
    return;
?>