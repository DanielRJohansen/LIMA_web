# LIMA MD website

The website uses plain PHP, CSS, and JavaScript with no build step or external frontend dependencies.

## Layout

| Folder | What it is |
|---|---|
| `public/` | **The website. This is the only thing uploaded to the web hotel.** |
| `tests/` | Tests. Not uploaded. |
| `legacy/` | Unused leftovers from the old design (bootstrap, font-awesome). Not uploaded, safe to delete. |
| `dev-data/` | Counts and emails from running locally. Created by `run.bat`, ignored by git, never uploaded. |

Everything else in this folder (`README.md`, `run.bat`, `.gitignore`, `LICENSE.txt`, `.git`) is for development and is not uploaded.

## Run locally

Run `run.bat`, which needs PHP 7.3 or newer on the PATH, and open http://localhost:8000. It serves `public/`, and keeps download counts and subscriber emails in `dev-data/`.

## Deploying

Upload the **contents** of `public/` to the web root of the web hotel, including the hidden `.htaccess` files, and nothing else.

- `public/database/` holds only a `.htaccess`. The real `download_counts.csv` and `emails.csv` exist only on the server, and are not in git, so uploading can't overwrite them.
- Don't use a "mirror" or "sync" mode that deletes files missing locally. It would delete those two files from the server.
- The server's `database/` directory must be writable by PHP: it holds the counts, the subscriber emails and the release cache.

## CLI help console

The console displays help only. It never runs commands or submits input to the server. Command matching is case-sensitive and requires the `lima` prefix. Recognized commands always display their help; unknown input displays general help. Arrow keys recall input and Tab completes command names. Shift+Tab leaves the input normally.

`public/resources/cli-help.json` contains a snapshot imported on September 30, 2026 from the `dev_dj` branch:

- https://github.com/DanielRJohansen/LIMA/blob/dev_dj/code/LIMA/include/CliDefinitions.h
- https://github.com/DanielRJohansen/LIMA/blob/dev_dj/code/LIMA/lima.cpp

To refresh it, copy the raw command help strings and command summaries from `CliDefinitions.h`, and reproduce `PrintGeneralHelp()` from `lima.cpp`. Keep help strings unchanged, including line breaks and option spacing. The data is embedded by PHP; it does not require a runtime request to GitHub.

Run the console checks:

```sh
node --test tests/cli-console.test.cjs
```

## Downloads

The site hosts no binaries. The download buttons go through `public/downloads/download.php`, which looks up the latest published release in https://github.com/DanielRJohansen/LIMA/releases, counts the download in `database/download_counts.csv` (per file name, so every version has its own count), and redirects to the file on GitHub. The version and file names shown on the pages come from the same release, so publishing a release in the LIMA repository updates the site by itself.

- `public/releases.php` finds the release and recognises each platform's file by its name: `lima_*_amd64.deb`, `PKGBUILD`, `*-windows-x64.zip` and `*-linux-x86_64.tar.gz`. Those names are made by `distribution/release.bat` in the LIMA repository. Only files in that repository's releases are ever redirected to.
- The answer is cached for 10 minutes in `database/release_cache.json`, since GitHub's API allows only 60 anonymous requests per hour per IP. The `database` directory must be writable by PHP. If GitHub is unreachable, the last known release is still used.
- If there is no release, or the lookup fails, the buttons link to the releases page instead. The reason is written to the PHP error log.
- Old `download.php?file=...` links keep working.

The lookup needs HTTPS from PHP: the `curl` or `openssl` extension, and a CA certificate bundle (`curl.cainfo` or `openssl.cafile` in `php.ini`). A bare PHP install on Windows has none of these, so locally the buttons fall back to the releases page.

Run the download checks, which use a fake GitHub API and never touch the real counts:

```sh
php tests/downloads.test.php
```
