# LIMA MD website

Run locally with PHP 7.3 or newer:

```sh
php -S localhost:8000
```

Open http://localhost:8000. The website uses plain PHP, CSS, and JavaScript with no build step or external frontend dependencies.

## CLI help console

The console displays help only. It never runs commands or submits input to the server. Command matching is case-sensitive and requires the `lima` prefix. Recognized commands always display their help; unknown input displays general help. Arrow keys recall input and Tab completes command names. Shift+Tab leaves the input normally.

`resources/cli-help.json` contains a snapshot imported on September 30, 2026 from the `dev_dj` branch:

- https://github.com/DanielRJohansen/LIMA/blob/dev_dj/code/LIMA/include/CliDefinitions.h
- https://github.com/DanielRJohansen/LIMA/blob/dev_dj/code/LIMA/lima.cpp

To refresh it, copy the raw command help strings and command summaries from `CliDefinitions.h`, and reproduce `PrintGeneralHelp()` from `lima.cpp`. Keep help strings unchanged, including line breaks and option spacing. The data is embedded by PHP; it does not require a runtime request to GitHub.

Run the console checks:

```sh
node --test tests/cli-console.test.cjs
```

Downloads use the existing `downloads/download.php` endpoint and its counters. Package files and the existing email subscription endpoint remain available.
