=== Serverstate ===
Contributors: pluginkollektiv
Tags: server, monitoring, response, uptime, downtime, serverstate
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZAQUT9RLPW8QN
Requires at least: 3.3
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



Dashboard-Widget für Serverstate, das zuverlässige Server Monitoring Tool. Blogausfälle protokollieren, Performance messen.



== Description ==

= Online-Status & Performance =
[Serverstate](https://serverstate.de/?referrer=245049071 "Server Monitoring")* ist ein Monitoring Service, welcher die Erreichbarkeit von Webseiten überwacht und deren Antwortzeiten misst. Im Fall einer Nichterreichbarkeit der Zielseite verschickt der Dienst eine E-Mail, Tweet oder SMS als Benachrichtigung.

Das *Serverstate* Plugin legt im WordPress-Administrationsbereich ein Dashboard-Widget an, welches Antwortzeiten und Erreichbarkeitswerte des Blogs als Statistik abbildet. Wie oft war der Blog offline? Hat sich nach einem Update die Performance verschlechtert? Das *Serverstate* Widget liefert Antworten und schafft einen Überblick über die Erreichbarkeit und Geschwindigkeit in den letzten 30 Tagen.

Direkt im *Serverstate* Widget über den Link *Konfigurieren* werden die Zugangsdaten des *Serverstate* Accounts und die zuständige Sensor-ID (ID des Überwachungsauftrags) hinterlegt. Bedauerlicherweise stellt *Serverstate* keine API-Kommunikation mithilfe eines API-Schlüssels zur Verfügung, sodass Zugangsdaten des *Serverstate* Accounts in WordPress verschlüsselt gespeichert und ausschließlich zum Datenabgleich mit der *Serverstate* Schnittstelle verwendet werden (müssen).

Die auf dem WordPress-Dashboard abgebildete Statistik ist interaktiv, d.h. bei Mausberührungen erscheint die jeweilige Kennzahl zum gewählten Tag: *Antwortzeit in Millisekunden* oder *Erreichbarkeit in Prozent* (sind zwei Diagrammlinien).

*Partnerlink


= Hinweise =
1. Bei neu angelegten *Serverstate* Überwachungsaufträgen kann es mehrere Stunden dauern, bis *Serverstate* hierzu Daten zum Abruf bereitstellt.
1. Das Plugin verfügt über einen internen Cache, wo die Statistik für einen halben Tag aufbewahrt wird. Nach Ablauf der 12 Stunden wird eine Synchronisation durchgeführt.
1. Die in Plugin-Optionen abgefragte Sensor-ID ist eine Zahl, die einem Überwachungsauftrag von *Serverstate* zugewiesen und beim Anzeigen bzw. Bearbeiten des Auftrages in der URL sichtbar ist. Das zu suchende Muster: *?sensor_id=912164573*
1. *Serverstate* ist ein kostenpflichtiger Dienst. Der Kostenfaktor hängt von der Prüfungshäufigkeit ab. [Kurze Vorstellung des Dienstes](https://plus.google.com/110569673423509816572/posts/hWdRrhWyots).


= Systemanforderungen =
* PHP ab 5.2
* WordPress ab 3.3
* Serverstate Account


= Unterstützung =
* Per [Flattr](https://flattr.com/t/1768517)
* Per [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZAQUT9RLPW8QN)


= Autor =
* [Twitter](https://twitter.com/wpSEO)
* [Google+](https://plus.google.com/110569673423509816572)
* [Plugins](http://wpcoder.de)



== Changelog ==

= 0.5.2 =
* Anpassung zwecks Wechsel des TLS-Zertifikats (auf Seite von Serverstate)

= 0.5.1 =
* Kommunikation mit *Serverstate* via HTTPS
* Zusätzliche Sicherheitsprüfungen
* Unterstützung für WordPress 3.9
* Code-Refactoring

= 0.5 =
* Ersatz für *deprecated* Benutzergruppen
* Screenshot nach *assets* verschoben
* Neuer Flattr-Link

= 0.4 =
* Unterstützung für WordPress 3.5
* Lauffähig unter PHP 5.2

= 0.3 =
* AJAX-Loading der Daten für verbesserte Dashboard-Performance

= 0.2 =
* Hinweis auf eine fehlerhafte Sensor-ID
* Umstellung der Cache-Dauer von 1 auf 12 Stunden

= 0.1 =
* Serverstate Plugin geht online



== Screenshots ==

1. Serverstate Dashboard Widget mit Verlauf



== Installation ==

1. Den Plugin-Ordner ins WordPress-Verzeichnis */wp-content/plugins/* übertragen.
1. Das Plugin unter *Plugins* aktivieren.
1. In der gleichen Ansicht auf *Einstellungen* klicken.
1. Im Formular die Serverstate Zugangsdaten und die Sensor-ID eingeben.
1. Die Eingabe speichern.