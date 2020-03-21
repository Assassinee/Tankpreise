<?php
$loginbox = '<div class="bg">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12"></div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <form class="form-container" action="' . $_SERVER['REQUEST_URI'] . '" method = "POST" target="_self" accept-charset="UTF-8">
                    <div class="form-group">
                        <!--<label for="exampleInputPassword1" style="color: #fff;">Password</label>-->
                        <input type="password" name="passwortfeld" class="form-control" id="exampleInputPassword1" placeholder="Password" autofocus>
                    </div>
                    <button type="submit" name= "submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12"></div>
        </div>
    </div>
</div>';

if(isset($_POST['submit'])) {

    if($_POST['passwortfeld'] == $webseitenpasswort) {

        $_SESSION['eingeloggt'] = true;
        header("location: $_SERVER[REQUEST_URI]");
    }
    else {
        echo "<div id='fehlermeldung'><div id='fehlermeldungtitel'><h2>Login fehlgeschlagen</h2></div><div id='fehlermeldungmeldung'>Das angegebene Passwort ist falsch</div></div>";
        echo $loginbox;
    }
} else {

    echo $loginbox;
}
?>
