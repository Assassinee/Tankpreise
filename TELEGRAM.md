# Telegram Modul:
Um das Telegram Modul verwenden zu können benötigt man 2 Dinge.
Als erstes wird ein Telegram-Bot benötigt.
Als zweites muss man seine User-ID herausfinden und in der Config-Datei eintragen.

### Einen neuen Bot erstellen:

Um einen Bot zu erstellen muss man den Bot @BotFather anschreiben.
Dies macht man mit dem Befehl:
```
/newbot
```

Darauf antwortet der BotFather mit: 

```
Alright, a new bot. How are we going to call it? Please choose a name for your bot.
```

Nun kann man sich einen Namen für den Bot aussuchen.
Diesen schreibt man einfach in den Chat.

```
BOTNAME
```

Darauf antwortet der Botfather wiederum mit:

```
Good. Now let's choose a username for your bot. It must end in `bot`. Like this, for example: TetrisBot or tetris_bot.
```

Nun muss man sich noch einen Username für den Bot aussuchen.
Am einfachsten ist es den Namen des Bot's zu nehmen und ein _bot anzuhängen.

```
BOTNAME_BOT
```

Darauf antwortet der Botfather mit:

```
Done! Congratulations on your new bot. You will find it at t.me/<USERNAME>.
You can now add a description, about section and profile picture for your bot, see /help for a list of commands.
By the way, when you've finished creating your cool bot, ping our Bot Support if you want a better username for it.
Just make sure the bot is fully operational before you do this.

Use this token to access the HTTP API:
<API KEY>
Keep your token secure and store it safely, it can be used by anyone to control your bot.

For a description of the Bot API, see this page: https://core.telegram.org/bots/api
```

Nun kann man den API-Key in der Datei ```config/telegramConfig.php``` eintragen.
Dies macht man bei ```token```.

Als letzten schritt müssen noch alle User eingetragen werden,
auf die Der Bot reagieren soll. Dafür benötigt man seine User-ID.

### Webhook einrichten:
Damit der Bot vernünftig  arbeiten kann wird ein Webhook benötigt.
Der Webhook verarbeitet die ankommenden Nachrichten des Bot's.

Das Einrichten  ist sehr einfach. Es muss nur die URL aufgerufen werden und zwei Anpassungen  vorgenommen werden.
Als erstes muss der APi-Key eingetragen werden und als zweites muss die URL zu der Webhook-Datei angegeben werden.

```
https://api.telegram.org/bot<API-KEY>/setwebhook?url=<Webhook.php>
```


### User-ID herausfinden:

Um seine ID herauszufinden, ist es am einfachsten den Bot @userinfobot
anzuschreiben. Dabei ist es egal was man dem Bot für einen Text schreibt.
Die Antwort ist immer:
```
@USERNAME
Id: XXXXXXXXX
First: NAME
LANG: XX
```

Diese User-ID wird auch in der Datei ```config/telegramConfig.php``` eingetragen.
Dabei ist es egal, was in dem vorderen Feld eingetragen wird. Dies kann einfach als
Notiz genutzt werden zu wem die User-ID gehört.

### Telegram Befehle hinzufügen:
Dieser Schritt ist optional.
Um einen Befehl bei einem Telegram bot hinzuzufügen, muss der BotFather angeschrieben werden.
Dabei sendet man den Befehl ```/mybots```

Darauf antwortet der Bot mit einer List Seiner Bots. Dort wählt man sich den entsprechenden Bot aus.
In dem nächsten Menü geht man aus ```Edit Bot``` und dann auf ```Edit Commands```.

Nun bekommt man Folgende Antwort:
```
OK. Send me a list of commands for your bot. Please use this format:

command1 - Description
command2 - Another description

Send /empty to keep the list empty.
```

jetzt kann man einen oder mehrere Befehle hinzufügen.
Durch das hinzufügen der Befehle schlägt Telegram die Befehle direkt bei der Eingabe vor.



# Bot Befehle:
Der Bot besitzt die folgenden Befehle:
- ```/preisinfo [TYP] [Betrag]```

#### /preisinfo [TYP] [Betrag]
Durch diesen Befehl ist es möglich, dass der Bot einem eine Erinnerung sendet,
wenn der angegebene Betrag erreicht oder unterschritten wird.