<?php

if(isset($_GET['id']))
{
    try
    {
        $db = new PDO($dbConfig['Typ'] . ':dbname=' . $dbConfig['Database'] . ';host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
    } catch (PDOException $e)
    {
        echo $e->getMessage();
        exit();
    }

    $id = $_GET['id'];

    if(isset($_POST['submit']))
    {
        if(isset($_POST['tsname']) && isset($_POST['tsdesc']) && isset($_POST['fieldRGB']))
        {
            $sql = 'Update tankstellen Set Name = :tsname, Beschreibung = :tsdesc, Farbe = :tscolor where TankstellenID = :id';

            $command = $db->prepare($sql);

            $tsName = $_POST['tsname'];
            $command->bindParam(':tsname', $tsName);

            $tsDesc = $_POST['tsdesc'];
            $command->bindParam(':tsdesc', $tsDesc);

            $tsColor = $_POST['fieldRGB'];
            $command->bindParam(':tscolor', $tsColor);

            $command->bindParam(':id', $id);

            $command->execute();

            if($error = $command->errorInfo()[0] == 0)
            {
                $_SESSION['Erfolg']['Titel'] = $languagetext['edit']['changetitle'];
                $_SESSION['Erfolg']['Meldung'] = $languagetext['edit']['changetext'];
                header('location: index.php?site=Einstellung');
            } else {
                $_SESSION['Fehler']['Titel'] = $languagetext['edit']['dberrortitle'];
                $_SESSION['Fehler']['Meldung'] = $error[2];
                header('location: index.php?site=Einstellung');
            }
        } else {

            $_SESSION['Fehler']['Titel'] = $languagetext['edit']['errortitle'];
            $_SESSION['Fehler']['Meldung'] = $languagetext['edit']['errortext'];
            header('location: index.php?site=Einstellung');
        }
    } else {

        $sql = 'Select TankstellenID, Name, Farbe, Beschreibung From tankstellen WHERE TankstellenID = :id';

        $command = $db->prepare($sql);

        $command->bindParam(':id', $id);

        $command->execute();

        $data = $command->fetchAll();

        $id = $data[0]['TankstellenID'];
        $name = $data[0]['Name'];
        $color = $data[0]['Farbe'];
        $description = $data[0]['Beschreibung'];

        $table = '<form class="form-inline" action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">';

        $table .= '<div id="tankstellenuebersicht">
                    <h1 style="text-align: center;">'. $languagetext['edit']['table']['title'] .'</h1>
                    <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark"><tr><th>'.$languagetext['edit']['table']['id'].'</th><th>'
                    .$languagetext['edit']['table']['name'].'</th><th>'.$languagetext['edit']['table']['color']
                    .'</th><th>'.$languagetext['edit']['table']['description'].'</th></tr></thead>';

        $colorRGB = explode(',', $color);

        $colorHEX = sprintf("#%02x%02x%02x", $colorRGB[0], $colorRGB[1], $colorRGB[2]);

        $table .= '<tr><td>' . $id . '</td><td><input type="text" name="tsname" value="' . $name
            . '" class="form-control"></td><td><table><tr><td>#Hex</td><td>RGB</td></tr><tr><td><input '
            . 'class="jscolor" value="' . $colorHEX . '" onfocusout="hextoRGB()" id="feldHEX"></td><td>'
            . '<input type="text" onfocusout="rgbToHEX()" name="fieldRGB" id="feldRGB" value="' . $color
            . '"></td></tr></table></div></td><td><input type="text" name="tsdesc" value="' . $description
            . '" class="form-control" style="width: 350px;"></td></tr>';

        $table .= '</table></div>';

        $table .= '<div id="tankstellehinzufuegen" >
                        <button type="button" class="btn btn-primary mb-2" style="margin-left: 10px;" onclick="parent.location=\'index.php?site=Einstellung\'">'.$languagetext['edit']['table']['Cancel'].'</button>
                        <button type="submit" id="submitfocus" class="btn btn-primary mb-2" style="margin-left: 10px;" name="submit">'.$languagetext['edit']['table']['change'].'</button>
                    </div>
                    </form>';

        echo $table;

        echo '<script src="js/colorpicker.js"></script>';
        echo '<script src="js/jscolor.js"></script>';
    }
} else {

    $_SESSION['Fehler']['Titel'] = $languagetext['edit']['error']['title'];
    $_SESSION['Fehler']['Meldung'] = $languagetext['edit']['error']['text'];

    header('location: index.php?site=Einstellung');
}