<?php
// Finds the latest published LIMA release on GitHub, so the site never hosts binaries or hardcodes version numbers.
// The GitHub API allows only 60 anonymous requests per hour per IP, so the answer is cached on disk.

const LIMA_REPO = 'DanielRJohansen/LIMA';
const LIMA_RELEASES_PAGE = 'https://github.com/' . LIMA_REPO . '/releases';
// Downloads only ever redirect to files in this repository's releases
const LIMA_ASSET_PREFIX = 'https://github.com/' . LIMA_REPO . '/releases/download/';
// The release file for each platform, as made by distribution/release.bat in the LIMA repository
const LIMA_ASSET_PATTERNS = [
    'deb' => '/^lima_.+_amd64\.deb$/',
    'pkgbuild' => '/^PKGBUILD$/',
    'windows' => '/-windows-x64\.zip$/',
    'linux' => '/-linux-x86_64\.tar\.gz$/',
];

// Holds the download counts and the release cache. LIMA_DATA_DIR is for tests
function lima_data_dir(): string {
    $dir = getenv('LIMA_DATA_DIR');
    return rtrim($dir !== false && $dir !== '' ? $dir : __DIR__ . '/database', '/\\');
}

// Returns [HTTP status, body]. Status 0 means GitHub could not be reached
function lima_http_get(string $url): array {
    $headers = ['User-Agent: lima-dynamics-website', 'Accept: application/vnd.github+json'];
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $body === false ? [0, ''] : [$status, $body];
    }

    if (stripos($url, 'https:') === 0 && !extension_loaded('openssl'))
        error_log('LIMA site: PHP has neither the curl nor the openssl extension, so GitHub cannot be reached over HTTPS');
    $context = stream_context_create(['http' => [
        'header' => implode("\r\n", $headers),
        'timeout' => 5,
        'ignore_errors' => true,
    ]]);
    $body = @file_get_contents($url, false, $context);
    if ($body === false) return [0, ''];
    $status = 0;
    if (isset($http_response_header[0]) && preg_match('#^HTTP/\S+\s+(\d+)#', $http_response_header[0], $match))
        $status = (int)$match[1];
    return [$status, $body];
}

// Turns the GitHub API's answer into ['version', 'tag', 'url', 'assets' => [kind => ['name', 'url']]]
function lima_parse_release(string $json): ?array {
    $data = json_decode($json, true);
    if (!is_array($data) || empty($data['tag_name']) || !is_array($data['assets'] ?? null)) return null;

    $release = [
        'version' => ltrim($data['tag_name'], 'v'),
        'tag' => $data['tag_name'],
        'url' => LIMA_RELEASES_PAGE . '/tag/' . rawurlencode($data['tag_name']),
        'assets' => [],
    ];
    foreach ($data['assets'] as $asset) {
        $name = $asset['name'] ?? '';
        $url = $asset['browser_download_url'] ?? '';
        if (strpos($url, LIMA_ASSET_PREFIX) !== 0) continue;
        foreach (LIMA_ASSET_PATTERNS as $kind => $pattern) {
            if (!isset($release['assets'][$kind]) && preg_match($pattern, $name))
                $release['assets'][$kind] = ['name' => $name, 'url' => $url];
        }
    }
    return $release;
}

function lima_save_release_cache(string $path, ?array $release, int $fetched): void {
    $temporary = $path . '.' . getmypid() . '.tmp';
    if (@file_put_contents($temporary, json_encode(['fetched' => $fetched, 'release' => $release])) === false) return;
    if (!@rename($temporary, $path)) @unlink($temporary);
}

// The latest published (not draft, not prerelease) release, or null if there is none or it can't be determined
function lima_latest_release(): ?array {
    static $resolved = false;
    static $release = null;
    if ($resolved) return $release;
    $resolved = true;

    $cachePath = lima_data_dir() . '/release_cache.json';
    $ttlSetting = getenv('LIMA_RELEASE_CACHE_SECONDS');
    $ttl = $ttlSetting !== false && $ttlSetting !== '' ? (int)$ttlSetting : 600;

    $cache = is_file($cachePath) ? json_decode((string)@file_get_contents($cachePath), true) : null;
    $hasCache = is_array($cache) && isset($cache['fetched']) && array_key_exists('release', $cache);
    if ($hasCache && time() - $cache['fetched'] < $ttl) return $release = $cache['release'];

    $apiUrl = getenv('LIMA_RELEASES_API_URL') ?: 'https://api.github.com/repos/' . LIMA_REPO . '/releases/latest';
    [$status, $body] = lima_http_get($apiUrl);
    if ($status === 200) {
        $fresh = lima_parse_release($body);
        if ($fresh !== null) {
            lima_save_release_cache($cachePath, $fresh, time());
            return $release = $fresh;
        }
    } elseif ($status === 404) {	// Nothing has been published yet
        lima_save_release_cache($cachePath, null, time());
        return null;
    }

    // GitHub is unreachable or misbehaving. Keep serving the last known release, and try again in a minute
    error_log("LIMA site: could not read the latest release from GitHub (status $status)" . ($hasCache ? ', using the cached one' : ''));
    if ($hasCache) {
        lima_save_release_cache($cachePath, $cache['release'], time() - $ttl + 60);
        return $release = $cache['release'];
    }
    return null;
}

// Where a download button goes. Through our counter if the file exists, otherwise to the releases page
function lima_download_href(?array $release, string $kind): string {
    return isset($release['assets'][$kind]) ? 'downloads/download.php?platform=' . $kind : LIMA_RELEASES_PAGE;
}

function lima_h(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
