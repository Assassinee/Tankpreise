<?php
if(isset($_SESSION['eingeloggt']) && $_SESSION['eingeloggt'] == true) {

    $mysqli = new mysqli($dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass'], $dbConfig['Database']);

    $sql = "Select TankstellenID, Name, Farbe, Beschreibung From tankstellen";

    $result = $mysqli->query($sql);

    $row = $result->fetch_row();

    $tablelle = '<div id="tankstellenuebersicht">
                <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark"><tr><th>TankstellenID</th><th>Name</th><th>Farbe</th><th>Beschreibung</th><th>bearbeiten</th><th>löschen</th></tr></thead>';

    while ($row != null) {

        $tablelle .= "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]<div style='height: 15px; width: 15px; background-color: rgba($row[2],1);'></div></td><td>$row[3]</td><td><a href='?site=bearbeiten&id=$row[0]'>bearbeiten</a></td><td><a href='?site=loeschen&id=$row[0]'>löschen</a></td></tr>";

        $row = $result->fetch_row();
    }

    $tablelle .= '</table></div>';

    $form = '<div id="tankstellehinzufuegen" >
                <form class="form-inline" action="add.php" method = "POST" target="_self" accept-charset="UTF-8">
                    <div class="form-group mx-sm-2 mb-2">
                        <input type="text" class="form-control" name="tankstellenid" placeholder="TankstellenID">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary mb-2">hinzufuegen</button>
                    <button type="button" class="btn btn-primary mb-2" style="margin-left: 10px;" onclick="parent.location=\'index.php?site=suchen\'">suchen</button>
                </form>
             </div>';

    if(isset($_SESSION['Fehler'])) {

        $fehlerTitel = $_SESSION['Fehler']['Titel'];
        $fehlerText  = $_SESSION['Fehler']['Meldung'];

        echo "<div id='fehlermeldung'><div id='fehlermeldungtitel'><h2>" . $fehlerTitel . "</h2></div><div id='fehlermeldungmeldung'>" . $fehlerText . "</div></div>";
        $_SESSION['Fehler'] = null;
    }
    if(isset($_SESSION['Erfolg'])) {

        $fehlerTitel = $_SESSION['Erfolg']['Titel'];
        $fehlerText  = $_SESSION['Erfolg']['Meldung'];

        echo "<div id='erfolgmeldung'><div id='erfolgmeldungtitel'><h2>" . $fehlerTitel . "</h2></div><div id='erfolgmeldungmeldung'>" . $fehlerText . "</div></div>";
        $_SESSION['Erfolg'] = null;
    }

    echo $tablelle;
    echo $form;
} else {
    require_once('login.php');
}