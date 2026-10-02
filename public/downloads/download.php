<?php
// Counts a download, then sends the visitor to the file in the latest LIMA release on GitHub.
// Usage: download.php?platform=deb|pkgbuild|windows|linux
require __DIR__ . '/../releases.php';

$platform = $_GET['platform'] ?? '';
if ($platform === '' && isset($_GET['file'])) {
    // Links from before the downloads came from GitHub releases
    $legacyFile = basename($_GET['file']);
    if (preg_match('/\.deb$/', $legacyFile)) $platform = 'deb';
    elseif ($legacyFile === 'PKGBUILD') $platform = 'pkgbuild';
}
if (!array_key_exists($platform, LIMA_ASSET_PATTERNS)) {
    http_response_code(404);
    echo 'Unknown download.';
    exit;
}

$release = lima_latest_release();
$asset = $release['assets'][$platform] ?? null;
if ($asset === null) {
    // No release, or none for this platform. Don't count it, and let the visitor find out why
    header('Location: ' . LIMA_RELEASES_PAGE, true, 302);
    exit;
}

// Count by file name, so every version gets its own count
$countsFile = lima_data_dir() . '/download_counts.csv';
if (($handle = @fopen($countsFile, 'c+')) !== false) {
    if (flock($handle, LOCK_EX)) {	// Two overlapping downloads must not lose a count
        $counts = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[0], $row[1])) $counts[$row[0]] = $row[1];
        }
        $counts[$asset['name']] = isset($counts[$asset['name']]) ? (int)$counts[$asset['name']] + 1 : 1;

        ftruncate($handle, 0);
        rewind($handle);
        foreach ($counts as $filename => $count) fputcsv($handle, [$filename, $count]);
        fflush($handle);
        flock($handle, LOCK_UN);
    }
    fclose($handle);
}

header('Location: ' . $asset['url'], true, 302);
