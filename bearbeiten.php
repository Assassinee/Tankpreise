<?php
if($_SESSION['eingeloggt'] == true) {

    if(isset($_GET['id'])) {

        $mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

        $id = $_GET['id'];

        if(isset($_POST['submit'])) {

            if(isset($_POST['tsname']) && isset($_POST['tsbesch']) && isset($_POST['feldRGB'])) {

                $tsName = $_POST['tsname'];
                $tsBesch = $_POST['tsbesch'];
                $tsfarbe = $_POST['feldRGB'];

                $sql = "Update tankstellen Set Name = '$tsName', Beschreibung = '$tsBesch', Farbe = '$tsfarbe' where TankstellenID = '$id'";

                $abfrage = $mysqli->query($sql);

                if(!$abfrage) {

                    $_SESSION['Fehler']['Titel'] = 'MYSQL Fehler';
                    $_SESSION['Fehler']['Meldung'] = $mysqli->error;
                    header('location: index.php?site=Einstellung');
                } else {

                    $_SESSION['Erfolg']['Titel'] = 'geändert';
                    $_SESSION['Erfolg']['Meldung'] = 'Die Tankstelle wurde erfolgreich geänder';
                    header('location: index.php?site=Einstellung');
                }
            } else {

                $_SESSION['Fehler']['Titel'] = 'Fehler';
                $_SESSION['Fehler']['Meldung'] = 'Es ist ein unbekannter Fehler aufgetreten';
                header('location: index.php?site=Einstellung');
            }
        } else {

            $sql = "Select TankstellenID, Name, Farbe, Beschreibung From tankstellen WHERE TankstellenID = '$id'";

            $result = $mysqli->query($sql);

            $row = $result->fetch_row();

            $id = $row[0];
            $name = $row[1];
            $farbe = $row[2];
            $beschreibung = $row[3];

            $tablelle = '<form class="form-inline" action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">';

            $tablelle .= '<div id="tankstellenuebersicht">
                        <h1 style="text-align: center;">bearbeiten</h1>
                        <table class="table table-striped table-bordered table-hover">
                        <thead class="thead-dark"><tr><th>TankstellenID</th><th>Name</th><th>Farbe</th><th>Beschreibung</th></tr></thead>';

            $farbeHEX = sprintf("#%02x%02x%02x", 153, 102, 255);

            $tablelle .= "<tr><td>$id</td><td><input type='text' name='tsname' value='$name' class='form-control'></td><td><table><tr><td>#Hex</td><td>RGB</td></tr><tr><td><input class='jscolor' value='$farbeHEX' onfocusout='hextoRGB()' id='feldHEX'></td><td><input type='text' onfocusout='rgbToHEX()' name='feldRGB' id='feldRGB' value='$farbe'></td></tr></table></div></td><td><input type='text' name='tsbesch' value='$beschreibung' class='form-control' style='width: 350px;'></td></tr>";

            $tablelle .= '</table></div>';

            $tablelle .= '<div id="tankstellehinzufuegen" >
                            <button type="button" class="btn btn-primary mb-2" style="margin-left: 10px;" onclick="parent.location=\'index.php?site=Einstellung\'">abbrechen</button>
                            <button type="submit" id="submitfocus" class="btn btn-primary mb-2" style="margin-left: 10px;" name="submit">ändern</button>
                        </div>
                        </form>';

            echo $tablelle;

            echo '<script src="js/colorpicker.js"></script>';
            echo '<script src="js/jscolor.js"></script>';
        }
    } else {

        $_SESSION['Fehler']['Titel'] = 'Fehler';
        $_SESSION['Fehler']['Meldung'] = 'Es wurde keine Tankstelle ausgewählt';

        header('location: index.php?site=Einstellung');
    }
} else {
    header('location: index.php');
}
?>