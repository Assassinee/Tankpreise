<?php
if($_SESSION['eingeloggt'] == true) {

    if(isset($_GET['id'])) {

        $mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

        $id = $_GET['id'];

        if(isset($_GET['con']) && $_GET['con'] == 1) {

            $sql = "DELETE From tankstellen where TankstellenID = '$id'";

            $abfrage = $mysqli->query($sql);

            if(!$abfrage) {

                $_SESSION['Fehler']['Titel'] = 'MYSQL Fehler';
                $_SESSION['Fehler']['Meldung'] = $mysqli->error;
                header('location: index.php?site=Einstellung');
            } else {

                $_SESSION['Erfolg']['Titel'] = 'gelöscht';
                $_SESSION['Erfolg']['Meldung'] = 'Die Tankstelle wurde erfolgreich gelöscht';
                header('location: index.php?site=Einstellung');
            }

        } else {

            $sql = "Select TankstellenID, Name, Farbe, Beschreibung From tankstellen WHERE TankstellenID = '$id'";

            $result = $mysqli->query($sql);

            $row = $result->fetch_row();

            $tablelle = '<div id="tankstellenuebersicht">
                    <h1 style="text-align: center;">wirklich löschen?</h1>
                    <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark"><tr><th>TankstellenID</th><th>Name</th><th>Farbe</th><th>Beschreibung</th></tr></thead>';

            $tablelle .= "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]<div style='height: 15px; width: 15px; background-color: rgba($row[2],1);'></div></td><td>$row[3]</td></tr>";

            $tablelle .= '</table></div>';

            $form = '<div id="tankstellehinzufuegen" >
                    <form class="form-inline" action="loeschen.php" method = "POST" target="_self" accept-charset="UTF-8">
                        <button type="button" class="btn btn-primary mb-2" style="margin-left: 10px;" onclick="parent.location=\'index.php?site=Einstellung\'">abbrechen</button>
                        <button type="button" class="btn btn-primary mb-2" style="margin-left: 10px;" onclick="parent.location=\'index.php?site=loeschen&id=' . $id . '&con=1' . '\'">löschen</button>
                    </form>
                 </div>';

            echo $tablelle;
            echo $form;
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