# Barrierefreier Webshop -> Webwerk Bitshop

## Übersicht
Dieses Plugin bietet einen barrierefreien Webshop für blinde und sehbehinderte Nutzer. Es ermöglicht angemeldeten Benutzern, Hörbücher und Zeitschriften als MP3 herunterzuladen.

## Installation
1. Plugin in das `wp-content/plugins` Verzeichnis hochladen.
2. Aktivieren Sie das Plugin in der WordPress Admin-Oberfläche.

## Verzeichnisstruktur
- `assets`: Enthält statische Ressourcen wie Bilder und externe Skripte.
- `css`: Stylesheets für das Plugin.
- `js`: JavaScript-Dateien für das Plugin.
- `scss`: SCSS-Dateien für die Styles.
- `templates`: Template-Dateien für verschiedene Seiten des Webshops.
- `includes`: PHP-Dateien mit Funktionen und Aktionen.
- `blocks`: Gutenberg-Blöcke für den Webshop.
- `languages`: Sprachdateien für die Übersetzung.
- `README.md`: Diese Datei.

## Funktionen
- `acf-definitions.php`: Definitionen für Advanced Custom Fields.
- `cart-actions.php`: Aktionen und Filter für den Warenkorb.
- `download.php`: Download-Handler für die MP3-Dateien.
- `shop-post-type.php`: Registrierung des benutzerdefinierten Beitragstyps für Produkte.

## Entwickeln
1. Führen Sie `npm install` aus, um die Abhängigkeiten zu installieren.
2. Verwenden Sie `gulp`, um die SCSS-Dateien zu kompilieren und die JavaScript-Dateien zu minimieren.

