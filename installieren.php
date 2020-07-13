<?php

if(isset($_POST['submit'])) {

    if(isset($_POST['DBTYP']) && !empty($_POST['DBTYP']) &&
        isset($_POST['DBIP']) && !empty($_POST['DBIP'])&&
        isset($_POST['DBUSER']) && !empty($_POST['DBUSER']) &&
        isset($_POST['DBPW']) && !empty($_POST['DBPW']) &&
        isset($_POST['DBDB']) && !empty($_POST['DBDB']) &&
        isset($_POST['Benzienart']) && !empty($_POST['Benzienart']) &&
        isset($_POST['WPPW']) && !empty($_POST['WPPW']) &&
        isset($_POST['Webseitenstatus'])) {

        //create config
        $configfile = file('config/config.php');

        for ($i = 0; $i < sizeof($configfile); $i++) {

            $configfile[$i] = str_replace('<<DBTYP>>', $_POST['DBTYP'], $configfile[$i]);
            $configfile[$i] = str_replace('<<Database>>', $_POST['DBDB'], $configfile[$i]);
            $configfile[$i] = str_replace('<<User>>', $_POST['DBUSER'], $configfile[$i]);
            $configfile[$i] = str_replace('<<Pass>>', $_POST['DBPW'], $configfile[$i]);
            $configfile[$i] = str_replace('<<Host>>', $_POST['DBIP'], $configfile[$i]);
            $configfile[$i] = str_replace('<<benzinart>>', $_POST['Benzienart'], $configfile[$i]);
            $configfile[$i] = str_replace('<<webseitenpasswort>>', $_POST['WPPW'], $configfile[$i]);
            $configfile[$i] = str_replace('\'<<webseitenzugriff>>\'', $_POST['Webseitenstatus'], $configfile[$i]);
        }

        $handler = fopen('config/config.php', 'w');

        foreach ($configfile as $item) {

            fwrite($handler, $item);
        }
        fclose($handler);

        //create DB
        require_once('config/config.php');
        $dbname = $dbConfig['Database'];

        try
        {
            $db = new PDO($dbConfig['Typ'] . ':host=' . $dbConfig['Host'], $dbConfig['User'], $dbConfig['Pass']);
        } catch (PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }

        $db->exec("CREATE DATABASE $dbname");

        $db->query("use $dbname");

        $db->exec('CREATE TABLE ' . $dbname . '.tankstellen (

                TankstellenID Varchar(50) not null,
                Name Varchar(50) not null,
                Farbe Varchar(11) not null,
                Beschreibung Varchar(255),
                Primary Key (TankstellenID)
            );');

        $db->exec('CREATE TABLE ' . $dbname . '.preise (

                ID integer AUTO_INCREMENT,
                TankstellenID Varchar(50) not null,
                Zeit timestamp,
                Status varchar(50),
                E5 decimal(4, 3),
                E10 decimal(4, 3),
                Diesel decimal(4, 3),
                primary Key (ID),
                foreign key(TankstellenID) references Tankstellen(TankstellenID)
            );');

        unlink('installieren.php');
    } else {

        echo 'Es wurden nicht alle Felder ausgefüllt.';
    }
} else {

    require_once __DIR__ . '/config/config.php';

    if(isset($_GET['language']))
    {
        $language = $_GET['language'];
    }
    require_once __DIR__ . '/lang/loadLang.php';

    $target = $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];

    $form = '
                <html>
                    <head>
                        <title>' . $languagetext['install']['title'] . '</title>
                        <link rel="stylesheet" href="stylesheet/bootstrap.min.css?' . time() . '">
                        <link rel="stylesheet" href="stylesheet/stylesheet.css?' . time() . '">
                    </head>
                    <body>
                    <center><h1>' . $languagetext['install']['header'] . '</h1></center>
                
                    <div style="width: 50%; margin-left: auto; margin-right: auto;">
                        <form method="POST" action="' . $_SERVER['REQUEST_URI'] . '" target="_self" accept-charset="UTF-8">
                            <h1>' . $languagetext['install']['database'] . ':</h1>
                            <div class="form-group row">
                                <label for="DBTYP" class="col-sm-2 col-form-label">' . $languagetext['install']['typ'] . ':</label>
                                <div class="col-sm-10">
                                    <input type="text" name="DBTYP" value="mysql">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="DBIP" class="col-sm-2 col-form-label">' . $languagetext['install']['ip'] . ':</label>
                                <div class="col-sm-10">
                                    <input type="text" name="DBIP" value="127.0.0.1">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="DBDB" class="col-sm-2 col-form-label">' . $languagetext['install']['database'] . ':</label>
                                <div class="col-sm-10">
                                    <input type="text" name="DBDB">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="BSUSER" class="col-sm-2 col-form-label">' . $languagetext['install']['username'] . ':</label>
                                <div class="col-sm-10">
                                    <input type="text" name="DBUSER">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="DBPW" class="col-sm-2 col-form-label">' . $languagetext['install']['pass'] . ':</label>
                                <div class="col-sm-10">
                                    <input type="text" name="DBPW">
                                </div>
                            </div>
                            <h1>' . $languagetext['install']['outher'] . '</h1>
                            <div class="form-group row">
                                <label class="col-sm-2" for="Benzienart">' . $languagetext['install']['petroltyp'] . ':</label>
                                <div class="col-sm-2">
                                    <select class="custom-select" name="Benzienart" id="Benzienart">
                                        <option value="e5" selected>E5</option>
                                        <option value="e10">E10</option>
                                        <option value="diesel">Diesel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="WPPW" class="col-sm-2 col-form-label">' . $languagetext['install']['wppw'] . ':</label>
                                <div class="col-sm-10">
                                    <input type="text" name="WPPW">
                                </div>
                            </div>
                            <fieldset class="form-group">
                                <div class="row">
                                    <legend class="col-form-label col-sm-2 pt-0">' . $languagetext['install']['status'] . ':</legend>
                                    <div class="col-sm-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="Webseitenstatus" id="gridRadios1" value="1" checked>
                                            <label class="form-check-label" for="gridRadios1">
                                                ' . $languagetext['install']['status1'] . '
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="Webseitenstatus" id="gridRadios2" value="0">
                                            <label class="form-check-label" for="gridRadios2">
                                                ' . $languagetext['install']['status2'] . '
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <button type="submit" name="submit" class="btn btn-primary">' . $languagetext['install']['send'] . '</button>
                        </form>
                    </div>
                    </body>
                    <footer class="footer">
                        <ul>
                            <li>
                                <a href="' . $_SERVER['PHP_SELF'] . '?language=EN">English</a>
                            </li>
                            <li>
                                <a href="' . $_SERVER['PHP_SELF'] . '?language=DE">German</a>
                            </li>
                        </ul>
                    </footer>
                </html>';
    echo $form;
}