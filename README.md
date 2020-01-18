# Tankpreise





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