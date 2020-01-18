function hexToRgbCon(hex) {

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function componentToHex(c) {

    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHexCon(r, g, b) {

    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function rgbToHEX() {

    var rgbFarbe = document.getElementById('feldRGB').value;

    var getrennt = rgbFarbe.split(',');

    var umwandeln = rgbToHexCon(parseInt(getrennt[0]), parseInt(getrennt[1]), parseInt(getrennt[2]));

    document.getElementById('feldHEX').value = umwandeln;

    document.getElementById('feldHEX').focus();
    document.getElementById('submitfocus').focus();
}

function hextoRGB() {

    var hexFarbe = document.getElementById('feldHEX').value;

    var umwandeln = hexToRgbCon(hexFarbe);

    var ausgabe = umwandeln.r + ',' + umwandeln.g + ',' + umwandeln.b;

    document.getElementById('feldRGB').value = ausgabe;
}