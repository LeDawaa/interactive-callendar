<?php
    session_start();

    print('<table class="tableau" id="tableau">');

        $json = json_decode(file_get_contents('week.json'), true);
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
                foreach($groups as $group) {
                    $filled = false;
                    foreach($value as $val) {
                        $groupIsAll = $val["groupe"] == "All";
                        if($val["date"] == $day->format('Y-m-d') && ($groupIsAll || $val["groupe"] == $group)) {
                            if($val["duree"] != -1) {
                                $colspan = $groupIsAll ? " colspan=4" : "";
                                $isModalActive = $_SESSION['authenticated'] ? "data-modal-target=#modal " : "";
                                $class_content = $val["type"] . "<br>". $val["matiere"] . "<br>" . $val["enseignant"] . "<br>". $val["salle"];
                                $bg_color = 'style="background-color: #' . substr(dechex(crc32($val["matiere"])), 0, 6) . 'a0;"';
                                $id = ($groupIsAll) ? "all" : (($val["group"] == "G4") ? "last" : (($val["group"] == "G1") ? "first" : "middle"));
                                print("<td " . $isModalActive . $bg_color . " rowspan=" . $val["duree"] . $colspan . " class=" . $val["id"] . " id=" . $id . "> " . $class_content . " </td>");
                            } $filled = true;
                            if ($groupIsAll) goto out;
                        }
                    } if(!$filled) {
                        $classname = str_replace("h", "_", $time) . "_" . $day->format('Y-m-d') . "_" . $group;
                        $isModalActive = $_SESSION['authenticated'] ? "data-modal-target=#modal" : "";
                        $id = ($group == "G4") ? "last" : (($group == "G1") ? "first" : "middle");
                        print("<td " . $isModalActive . " class=" . $classname . " id=" . $id . "></td>");
                    } 
                } out:
            } print("</tr>");
        }

    print('</table>')
?>