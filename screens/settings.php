<?php

try
{
    $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
} catch (PDOException $e) {

    echo $e->getMessage();
    exit();
}

$sql = "Select TankstellenID, Name, Farbe, Beschreibung From tankstellen";

$command = $db->query($sql);

$gasStations = Array();

$table = '<div id="tankstellenuebersicht">
            <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark"><tr><th>'.$languagetext['settings']['id'].'</th><th>'.$languagetext['settings']['name'].'</th><th>'.$languagetext['settings']['color'].'</th><th>'.$languagetext['settings']['description'].'</th><th>'.$languagetext['settings']['edit'].'</th><th>'.$languagetext['settings']['delete'].'</th></tr></thead>';

foreach ($command as $key => $value)
{
    $table .= '<tr><td>' . $value['TankstellenID'] . '</td><td>' . $value['Name'] . '</td><td>'
                . $value['Farbe'] . '<div style="height: 15px; width: 15px; background-color: rgba('
                . $value['Farbe'] . ',1);"></div></td><td>' . $value['Beschreibung'] . '</td><td>'
                . '<a href="?site=bearbeiten&id=' . $value['TankstellenID'] . '">'.$languagetext['settings']['edit'].'</a></td>'
                . '<td><a href="?site=loeschen&id=' . $value['TankstellenID'] . '">'.$languagetext['settings']['delete'].'</a></td></tr>';
}

$table .= '</table></div>';

$form = '<div id="tankstellehinzufuegen" >
            <form class="form-inline" action="add.php" method = "POST" target="_self" accept-charset="UTF-8">
                <div class="form-group mx-sm-2 mb-2">
                    <input type="text" class="form-control" name="tankstellenid" placeholder="TankstellenID">
                </div>
                <button type="submit" name="submit" class="btn btn-primary mb-2">'.$languagetext['settings']['add'].'</button>
                <button type="button" class="btn btn-warning mb-2" style="margin-left: 10px;" onclick="parent.location=\'index.php?site=suchen\'">'.$languagetext['settings']['search'].'</button>
            </form>
         </div>';

if(isset($_SESSION['Fehler'])) {

    $errorTitle = $_SESSION['Fehler']['Titel'];
    $errorText  = $_SESSION['Fehler']['Meldung'];

    echo "<div id='fehlermeldung'><div id='fehlermeldungtitel'><h2>" . $errorTitle . "</h2></div><div id='fehlermeldungmeldung'>" . $errorText . "</div></div>";
    $_SESSION['Fehler'] = null;
}
if(isset($_SESSION['Erfolg'])) {

    $successTitle = $_SESSION['Erfolg']['Titel'];
    $successText  = $_SESSION['Erfolg']['Meldung'];

    echo "<div id='erfolgmeldung'><div id='erfolgmeldungtitel'><h2>" . $successTitle . "</h2></div><div id='erfolgmeldungmeldung'>" . $successText . "</div></div>";
    $_SESSION['Erfolg'] = null;
}

echo $table;
echo $form;