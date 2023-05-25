<?php
    session_start();

    print('<table class="tableau" id="tableau">');
        $json = json_decode(file_get_contents('../db/week.json'), true);
        $week = $json[0];
        $groups = $json[1];
        $times = $json[2];

        $startDate = clone $_SESSION['current_time'];
        $startDate->modify('monday this week');
        $weekDays = array_map(function ($i) use ($startDate) { return (clone $startDate)->add(new DateInterval("P{$i}D")); }, range(0, 4));

        print("<tr class=days><th></th>");
        foreach($weekDays as $day)
            print("<th colspan=4>" . $day->format('l, d F Y') . "</th>");
        print("</tr>");
        
        print("<tr class=groups><th>Heure</th>");
        foreach($weekDays as $day)
            foreach($groups as $group)
                print("<th colspan=1>" . $group . "</th>");
        print("</tr>");

        foreach($times as $time => $value) {
            print("<tr class=times> <th>" . $time . "</th>");
            foreach($weekDays as $day) {
                $franceHolidays2023 = [
                    '2023-01-01' => 'Jour de l\'An',
                    '2023-04-09' => 'Dimanche de Pâques',
                    '2023-04-10' => 'Lundi de Pâques',
                    '2023-05-01' => 'Fête du Travail',
                    '2023-05-08' => 'Victoire 1945',
                    '2023-05-18' => 'Ascension',
                    '2023-05-28' => 'Dimanche de Pentecôte',
                    '2023-05-29' => 'Lundi de Pentecôte',
                    '2023-07-14' => 'Fête Nationale',
                    '2023-08-15' => 'Assomption',
                    '2023-11-01' => 'Toussaint',
                    '2023-11-11' => 'Armistice 1918',
                    '2023-12-25' => 'Noël',
                ];

                foreach($franceHolidays2023 as $date=>$event) {
                    $datetime = new DateTime($date);
                    if ($day->format('md') == $datetime->format('md')) {
                        if($time == '8h15') print("<td colspan=4 rowspan=44 class=vacation>" . $event . "</td>");
                        goto out;
                    }
                }
                foreach($groups as $group) {
                    $filled = false;
                    foreach($value as $val) {
                        $groupIsAll = $val["groupe"] == "All";
                        if($val["date"] == $day->format('Y-m-d') && ($groupIsAll || $val["groupe"] == $group)) {
                            if($val["duree"] != -1) {
                                $colspan = $groupIsAll ? " colspan=4" : "";
                                $isModalActive = $_SESSION['authenticated'] ? "data-modal-target=#modal " : "";
                                $class_content = $val["type"] . "<br>". $val["matiere"] . "<br>" . $val["enseignant"] . "<br>". $val["salle"];
                                $bg_color = 'style="cursor: url(cursors/handwriting.png) 16 16, auto; background-color: #' . substr(dechex(crc32($val["matiere"])), 0, 6) . 'a0;"';
                                $id = ($groupIsAll) ? "all" : (($val["group"] == "G4") ? "last" : (($val["group"] == "G1") ? "first" : "middle"));
                                print("<td " . $isModalActive . $bg_color . " rowspan=" . $val["duree"] . $colspan . " class=" . $val["id"] . " id=" . $id . "> " . $class_content . " </td>");
                            } $filled = true;
                            if ($groupIsAll) goto out;
                        }
                    } if(!$filled) {
                        $classname = str_replace("h", "_", $time) . "_" . $day->format('Y-m-d') . "_" . $group;
                        $isModalActive = $_SESSION['authenticated'] ? "data-modal-target=#modal" : "";
                        $id = ($group == "G4") ? "last" : (($group == "G1") ? "first" : "middle");
                        print("<td " . $isModalActive . " class=" . $classname . " id=" . $id . " style=\"cursor: url('cursors/precision.png') 32 32, auto;\"></td>");
                    } 
                } out:
            } print("</tr>");
        }
    print('</table>')
?>