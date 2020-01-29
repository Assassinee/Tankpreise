# Tankpreise

Tankpreise ist ein Projekt um sie immer die aktuellen Benzienpreie anzeigen zu lassen. Es git zusätzlich Graphen um sich den Verlauf der Preise Grafisch anzeigen zu lassen. 

## Getting Started

### Prerequisites

Um die Webanwendung selber zu benutzen benötigst du

- Eine Datenbank deiner Wahl.
- Einen Webserver der PHP unterstüzt.

### Installing

Lade das Reposetory einfach herunter und gehe auf die Startseite. Dann wirst du automatisch durch die Installation geführt.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Authors

* Maximilian Kosowski

See also the list of [contributors](https://github.com/PurpleBooth/a-good-readme-template/contributors) who participated in this project.

## License

This project is licensed under the [Attribution 4.0 International](LICENSE.md) Creative Commons License - see the [LICENSE.md](LICENSE.md) file for details

-----
## Branch erstellen
- `git cheackout -b NAME`

## How to rebase
1. Den Master auschecken: `git checkout master`
2. Den aktuellen Master vom Server ziehen: `git pull`
3. Zurück in deinen aktuellen Branch wechseln `git checkout feature/XXX`
4. Deine aktuellen Commits alle auf ein Commit zurücksetzen mit: `git reset $(git merge-base master feature/XXX)`
5. Alle Änderungen in einen Commit packen: `git add . && git commit -m 'one commit'`
6. Rebasen `git rebase master`
7. In Webstorm unter VCS -> Git -> Resolve Conflicts die Merge Konflike beheben
8. Wenn die Konflikte behoben sind im Terminal `git rebase --continue` eingeben
9. Deine aktuellen Commits erneut alle auf ein Commit zurücksetzen mit: `git reset $(git merge-base master feature/XXX)`
10. Alle Änderungen in einen Commit packen: `git add . && git commit -m 'one commit'`
11. Dann sollte das grundsätzlich funktionieren. 