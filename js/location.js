function locationarround () {
    navigator.geolocation.getCurrentPosition(arroundForward);
}

function arroundForward (loc) {
    window.location.href='index.php?site=arround&lat='
        + loc.coords.latitude
        + '&lng='
        + loc.coords.longitude
        + '&radius='
        + document.getElementById("inputState").value;
}

function locationsearch () {
    navigator.geolocation.getCurrentPosition(searchForward);
}

function searchForward (loc) {
    window.location.href='index.php?site=suchen&lat='
        + loc.coords.latitude
        + '&lng='
        + loc.coords.longitude
        + '&radius='
        + document.getElementById("inputState").value;
}