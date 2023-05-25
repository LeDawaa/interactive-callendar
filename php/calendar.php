<!DOCTYPE html>

<?php
    session_start();
    
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
        header("Location: ../index.php");
        exit();
    }

    if (!isset($_SESSION['current_time'])) {
        $_SESSION['current_time'] = new DateTime();
        $_SESSION['current_time']->modify('monday this week');
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Project</title>

        <script type="module" src="../script.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <link rel="stylesheet" href="../css/calendar_style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous">
    </head>

    <body>

        <div id="container">
            <div id="upper">
                <label for="username", class="username-label">Logged in as <br><?php echo $_SESSION['authenticated'] ? $_SESSION['username'] : "Student" ?></label>
                <div class="middle-container">
                    <button name="previous_week" id="previous_week"><i class="fas fa-arrow-left"></i></button>
                    <div id="current-week"> <?php include 'get_current_week.php'; ?> </div>
                    <button name="next_week" id="next_week"><i class="fas fa-arrow-right"></i></button>
                </div>
                <button name="logout" id="logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>

            <div id="myTable">
                <?php include 'fetch_table.php'; ?>
            </div>
        </div>

        <?php
            $hours = ["hour" => ["8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19"]];
            $mins = ["minute" => ["00", "15", "30", "45"]];
            function comboBox($idname, $tuple) {
                foreach($tuple as $type => $values) {
                    print("<select id=" . $idname . " name=" . $type . "s id=" . $type . "-select>");
                    foreach($values as $value)
                        print("<option id=" . $idname . $value . " value=" . $value . " >" . $value . "</option>");
                    print("</select>");
                }
            }
        ?>
        
        <div class="modal" id="modal">
            <div class="modal-header" id="modal-header">
                <div class="day_choice" id="day_choice"></div>
                <div class="repeat_div" id="repeat_div">
                    <label for="repeat">Répétitions: </Répéter:>
                    <input type="number" id="repeat" name="repeat" min="1" value="1" style="width: 30px; margin-right: 20px;">
                </div>
                <button data-close-button class="close-button">&times;</button>
            </div>
            <div class="modal-content">
                <div class="modal-content-start">
                    <div class="start_label">Début:</div>
                    <div class="start_select">
                        <?php
                            comboBox("start_hour", $hours);
                            print(":");
                            comboBox("start_min", $mins);
                        ?>
                    </div>
                </div>
                <div class="modal-content-end">
                    <div class="end_label">Fin:</div>
                    <div class="end_select">
                        <?php
                            comboBox("end_hour", $hours);
                            print(":");
                            comboBox("end_min", $mins);
                        ?>
                    </div>
                </div>
                <div class="subject">
                    <div>Matière: </div>
                    <input type="text" name="matiere" id="matiere" placeholder="Matière">
                </div>
                <div class="teacher">
                    <div>Enseignant: </div>
                    <input type="text" name="enseignant" id="enseignant" placeholder="Enseignant">
                </div>
                <div class="room">
                    <div>Salle: </div>
                    <input type="text" name="salle" id="salle" placeholder="Salle">
                </div>
                <div class="type">
                    <div class="type_label">Type:</div>
                    <select id="type_select" name="type_select">
                        <option value="Cours">Cours</option>
                        <option value="TD">TD</option>
                        <option value="TP">TP</option>
                        <option value="Partiel">Partiel</option>
                        <option value="Examen">Examen</option>
                    </select>
                </div>
                <div class="group">
                    <div class="group_label">Groupe:</div>
                    <select disabled id="group_select" name="group_select">
                        <option value="G1">G1</option>
                        <option value="G2">G2</option>
                        <option value="G3">G3</option>
                        <option value="G4">G4</option>
                    </select>
                </div>
                <div class="error" id="error" style="display:none;">An error occurred while saving the course.</div>
                <button class="create-button" id="save-button">Valider</button>
                <button class="del-button" id="delete-button">Supprimer</button>
            </div>
            <div id='result'></div>
        </div>
        <div id="overlay"></div>
    </body>
</html>