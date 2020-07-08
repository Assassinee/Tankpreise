</main>
<footer class="footer">
    <ul>
        <li class="benzinart">
            <a href="#"><?php echo $languagetext['footer']['fuel']; ?>: <?php echo $BENZINART; ?></a>
            <ul>
                <li>
                    <a href="<?php echo $_SERVER['REQUEST_URI'] ?>&benzinart=E5">E5</a>
                </li>
                <li>
                    <a href="<?php echo $_SERVER['REQUEST_URI'] ?>&benzinart=E10">E10</a>
                </li>
                <li>
                    <a href="<?php echo $_SERVER['REQUEST_URI'] ?>&benzinart=Diesel">Diesel</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="index.php?site=info"><?php echo $languagetext['footer']['info']; ?></a>
        </li>
        <li>
            <a href="index.php?site=Diagramm"><?php echo $languagetext['footer']['Diagram']; ?></a>
        </li>
        <li>
            <a href="index.php?site=DiagrammWoche"><?php echo $languagetext['footer']['DiagramWeek']; ?></a>
        </li>
        <li>
            <a href="index.php?site=Einstellung"><?php echo $languagetext['footer']['settings']; ?></a>
        </li>
    </ul>
</footer>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
