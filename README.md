# Tankpreise

![GitHub](https://img.shields.io/github/license/Assassinee/Tankpreise)
![GitHub release (latest by date including pre-releases)](https://img.shields.io/github/v/release/Assassinee/Tankpreise?include_prereleases)
![GitHub All Releases](https://img.shields.io/github/downloads/Assassinee/Tankpreise/total)
![GitHub last commit](https://img.shields.io/github/last-commit/Assassinee/Tankpreise)
![GitHub (Pre-)Release Date](https://img.shields.io/github/release-date-pre/Assassinee/Tankpreise)

Tankpreise ist ein Projekt, um sich immer die aktuellen Benzinpreise anzeigen zu lassen. Es gibt zusätzlich Graphen, um sich den Verlauf der Preise Grafisch anzeigen zu lassen.

## Screenshots
<table>
    <tr>
        <td>
            <img alt="Diagramm" src="Screenshots/Diagramm.png">
        </td>
        <td>
            <img alt="Tankstellensuche" src="Screenshots/Tankstellensuche.png">
        </td>
        <td>
            <img alt="Einstellungen" src="Screenshots/Einstellungen.png">
        </td>
    </tr>
</table>

## Voraussetzungen:
Um die Webanwendung selber zu benutzen benötigst du:

- Eine Datenbank deiner Wahl
- Einen Webserver mit PHP 7.1
- API-Key's für die eingebundenen Dienste

## Installation
Lade dir den [letzten Release](https://github.com/Assassinee/Tankpreise/releases/latest) herunter und kopiere die Daten auf deinen Webserver. Bei dem ersten aufrufen der Webanwendung wirst du automatisch durch den Einrichtungsprozess  geführt.

[direkter Download](https://github.com/Assassinee/Tankpreise/releases/latest/download/asset-name.zip)

## Dienste:
Aktuell werden folgende Dienste von dieser Webanwendung unterstützt.

### Karte:
Wird für die Anzeige der Tankstellen auf einer Karte benötigt.
- [Google Maps](https://developers.google.com/maps/documentation/javascript/tutorial?hl=de)

### Geocoding:
Wird für die Übersetzung von einer Adresse in Koordinaten benötigt.
- [Google Geocoding](https://developers.google.com/maps/documentation/geocoding/start)

### Benzindaten:
Wird für die Abfrage der Benzinpreise benötigt.
- [Tankerkoenig](https://creativecommons.tankerkoenig.de/)

## Module:
Es ist möglich die Funktionalität der Anwenung durch Module zu erweitern.
- [Telegram Bot](TELEGRAM.md)

## Entwicklung
![GitHub issues](https://img.shields.io/github/issues/Assassinee/Tankpreise)
![GitHub closed issues](https://img.shields.io/github/issues-closed/Assassinee/Tankpreise)
![GitHub closed pull requests](https://img.shields.io/github/issues-pr-closed/Assassinee/Tankpreise)

Wenn du eine gute Idee für die Webanwenung hast, hilf gerne bei der Entwicklung mit.

## Autor
- [Maximilian Kosowski (Assassinee)](https://github.com/Assassinee)

## Lizenz
Dieses Projekt verwendet die [GNU AGPLv3](LICENSE) GNU Affero General Public License v3.0 - schau dir die [LICENSE.md](LICENSE) Datei für mehr Details an.
