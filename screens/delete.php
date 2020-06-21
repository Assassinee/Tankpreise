<?php

if(isset($_GET['id']))
{
    try
    {
        $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
    } catch (PDOException $e) {

        echo $e->getMessage();
        exit();
    }

    $id = $_GET['id'];

    if(isset($_GET['con']) && $_GET['con'] == 1)
    {
        $sql = 'DELETE From tankstellen where TankstellenID = :id';

        $command = $db->prepare($sql);

        $command->bindParam(':id', $id);

        $command->execute();

        $command->errorInfo();

        if($error = $command->errorInfo()[0] == 0)
        {
            $_SESSION['Erfolg']['Titel'] = $languagetext['delete']['deleted'];
            $_SESSION['Erfolg']['Meldung'] = $languagetext['delete']['deletedmsg'];
            header('location: index.php?site=Einstellung');
        } else {

            $_SESSION['Fehler']['Titel'] = $languagetext['delete']['mysqlerror'];
            $_SESSION['Fehler']['Meldung'] = $mysqli->error;
            header('location: index.php?site=Einstellung');
        }
    } else {

        $sql = 'Select TankstellenID, Name, Farbe, Beschreibung From tankstellen WHERE TankstellenID = :id';

        $command = $db->prepare($sql);

        $command->bindParam(':id', $id);

        $command->execute();

        $gasStation = $command->fetch();

        $tablelle = '<div id="tankstellenuebersicht">
                <h1 style="text-align: center;">'.$languagetext['delete']['deleteconf'].'</h1>
                <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark"><tr><th>'.$languagetext['delete']['id'].'</th><th>'.$languagetext['delete']['name'].'</th><th>'.$languagetext['delete']['colour'].'</th><th>'.$languagetext['delete']['description'].'</th></tr></thead>';

        $tablelle .= '<tr><td>'.$gasStation['TankstellenID'].'</td><td>'.$gasStation['Name'].'</td><td>'.$gasStation['Farbe'].'<div style="height: 15px; width: 15px; background-color: rgba('.$gasStation['Farbe'].',1);"></div></td><td>'.$gasStation['Beschreibung'].'</td></tr>';

        $tablelle .= '</table></div>';

        $form = '">\'' .$languagetext['delete']['cancel'].'</button>
                    <button type="button" class="btn btn-primary mb-2" style="margin-left: 10px;" onclick="parent.location=\'index.php?site=loeschen&id=' . $id . '&con=1' . '\'">'.$languagetext['delete']['delete'].'</button>
                </form>
             </div>';

        echo $tablelle;
        echo $form;
    }
} else {

    $_SESSION['Fehler']['Titel'] = $languagetext['delete']['error'];
    $_SESSION['Fehler']['Meldung'] = $languagetext['delete']['errormsg'];

    header('location: index.php?site=Einstellung');
}

