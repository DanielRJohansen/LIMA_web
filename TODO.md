# TODO

## Proper deploy mechanism
Deploying is copying the whole `public/` folder to one.com by hand. Replace it with a script (eg. FTP/SFTP upload of `public/`)
that never deletes server-only files (`database/download_counts.csv`, `emails.csv`, `release_cache.json`), skips unchanged files
(the 82 MB `benchdata/` would otherwise be re-uploaded every time), and can be run with a dry-run listing first.

## `lima gromacscompare` dataset hosting
`public/benchdata/v1/` holds what `lima gromacscompare` downloads from https://lima-dynamics.com/benchdata/v1/: one `.tar.gz`
per system, 82 MB (built by `LIMA_data/salad_gromacs_bench/bundle/make_bundle.py`, see its README). It is not in git (too
large), so copy it into `public/benchdata/` from LIMA_data on any machine that deploys.
- A dataset version folder is never changed after a LIMA release points at it: `resources/gromacscompare/gromacscompare.py`
  pins each archive's SHA-256. A new dataset goes in `v2/`.
- Fine on one.com for now (unlimited traffic). If storage or speed becomes a problem, move the archives to a GitHub release
  asset (eg. tag `benchdata-v1`, 2 GB per file, free traffic) behind a redirect, as `downloads/download.php` does for the binaries.
