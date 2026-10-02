<?php
// Tests that the download buttons and pages follow the latest LIMA release on GitHub.
// Starts a fake GitHub API and the site on local ports, so nothing touches GitHub or the real download counts.
// Run: php tests/downloads.test.php

const SITE_ROOT = __DIR__ . '/../public';
const ASSET_BASE = 'https://github.com/DanielRJohansen/LIMA/releases/download/v1.3.0/';
const RELEASES_PAGE = 'https://github.com/DanielRJohansen/LIMA/releases';

$failures = 0;
function check(string $name, bool $condition, string $detail = ''): void {
    global $failures;
    echo ($condition ? '  ok    ' : '  FAIL  ') . $name . (!$condition && $detail !== '' ? "  ($detail)" : '') . "\n";
    if (!$condition) $failures++;
}

function free_port(): int {
    $socket = stream_socket_server('tcp://127.0.0.1:0');
    $port = (int)substr(strrchr(stream_socket_get_name($socket, false), ':'), 1);
    fclose($socket);
    return $port;
}

function start_server(string $docroot, array $env, ?string $router = null): array {
    $port = free_port();
    $command = [PHP_BINARY, '-S', "127.0.0.1:$port", '-t', $docroot];
    if ($router !== null) $command[] = $router;
    $log = sys_get_temp_dir() . "/lima-web-test-server-$port.log";
    $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['file', $log, 'w'], 2 => ['file', $log, 'w']], $pipes, $docroot, $env + getenv());
    for ($tries = 0; $tries < 100; $tries++) {
        $connection = @fsockopen('127.0.0.1', $port, $errno, $error, 0.1);
        if ($connection) { fclose($connection); return [$process, $port]; }
        usleep(50000);
    }
    throw new RuntimeException("Server did not start, see $log");
}

// Returns [status, headers (lower case names), body]. Never follows redirects
function get(int $port, string $path): array {
    $context = stream_context_create(['http' => ['follow_location' => 0, 'ignore_errors' => true, 'timeout' => 10]]);
    $body = (string)@file_get_contents("http://127.0.0.1:$port$path", false, $context);
    $status = 0;
    $headers = [];
    foreach ($http_response_header ?? [] as $line) {
        if (preg_match('#^HTTP/\S+\s+(\d+)#', $line, $match)) $status = (int)$match[1];
        elseif (strpos($line, ':') !== false) {
            [$key, $value] = explode(':', $line, 2);
            $headers[strtolower($key)] = trim($value);
        }
    }
    return [$status, $headers, $body];
}

function release_json(string $assetBase = ASSET_BASE): string {
    $asset = function (string $name) use ($assetBase) {
        return ['name' => $name, 'browser_download_url' => $assetBase . $name];
    };
    return json_encode([
        'tag_name' => 'v1.3.0',
        'html_url' => RELEASES_PAGE . '/tag/v1.3.0',
        'assets' => [
            $asset('lima-1.3.0-windows-x64.zip'), $asset('lima-1.3.0-linux-x86_64.tar.gz'),
            $asset('lima_1.3.0_amd64.deb'), $asset('PKGBUILD'), $asset('release-notes.md'),
        ],
    ]);
}

// A scenario runs the site against a fake API, whose answer the test controls through $fixtureDir/mode
function scenario(string $title, int $cacheSeconds, callable $run): void {
    global $failures;
    echo "\n$title\n";
    $temporary = sys_get_temp_dir() . '/lima-web-test-' . bin2hex(random_bytes(4));
    mkdir("$temporary/data", 0777, true);
    mkdir("$temporary/fixture", 0777, true);
    file_put_contents("$temporary/fixture/router.php", <<<'ROUTER'
<?php
$dir = getenv('FIXTURE_DIR');
file_put_contents("$dir/hits", (int)@file_get_contents("$dir/hits") + 1);
$mode = trim((string)@file_get_contents("$dir/mode"));
if ($mode === 'ok') { header('Content-Type: application/json'); echo file_get_contents("$dir/release.json"); }
elseif ($mode === 'none') { http_response_code(404); echo '{"message":"Not Found"}'; }
else { http_response_code(500); echo 'boom'; }
ROUTER);
    file_put_contents("$temporary/fixture/release.json", release_json());
    file_put_contents("$temporary/fixture/hits", '0');
    $setMode = function (string $mode) use ($temporary) { file_put_contents("$temporary/fixture/mode", $mode); };
    $setMode('ok');

    [$api, $apiPort] = start_server("$temporary/fixture", ['FIXTURE_DIR' => "$temporary/fixture"], "$temporary/fixture/router.php");
    [$site, $sitePort] = start_server(realpath(SITE_ROOT), [
        'LIMA_DATA_DIR' => "$temporary/data",
        'LIMA_RELEASES_API_URL' => "http://127.0.0.1:$apiPort/latest",
        'LIMA_RELEASE_CACHE_SECONDS' => (string)$cacheSeconds,
    ]);
    try {
        $run($sitePort, $setMode, "$temporary/data", "$temporary/fixture");
    } catch (Throwable $error) {
        check('scenario ran without errors', false, $error->getMessage());
    } finally {
        proc_terminate($site);
        proc_terminate($api);
        proc_close($site);
        proc_close($api);
        usleep(100000);
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($temporary, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file)
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        @rmdir($temporary);
    }
}

function counts(string $dataDir): array {
    $counts = [];
    if (($handle = @fopen("$dataDir/download_counts.csv", 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) $counts[$row[0]] = (int)$row[1];
        fclose($handle);
    }
    return $counts;
}

scenario('A published release', 600, function ($port, $setMode, $dataDir, $fixtureDir) {
    $expected = [
        'deb' => 'lima_1.3.0_amd64.deb', 'pkgbuild' => 'PKGBUILD',
        'windows' => 'lima-1.3.0-windows-x64.zip', 'linux' => 'lima-1.3.0-linux-x86_64.tar.gz',
    ];
    foreach ($expected as $platform => $file) {
        [$status, $headers] = get($port, "/downloads/download.php?platform=$platform");
        check("$platform redirects to the release file", $status === 302 && ($headers['location'] ?? '') === ASSET_BASE . $file,
            "$status " . ($headers['location'] ?? ''));
    }
    get($port, '/downloads/download.php?platform=deb');
    $counts = counts($dataDir);
    check('counts are saved in the data directory, not in the site folder', !file_exists(SITE_ROOT . '/database/download_counts.csv'));
    check('downloads are counted per file', ($counts['lima_1.3.0_amd64.deb'] ?? 0) === 2 && ($counts['PKGBUILD'] ?? 0) === 1, json_encode($counts));

    [$status, $headers] = get($port, '/downloads/download.php?file=lima_1.0-1_amd64.deb');
    check('old .deb links still work', $status === 302 && ($headers['location'] ?? '') === ASSET_BASE . 'lima_1.3.0_amd64.deb');
    [$status, $headers] = get($port, '/downloads/download.php?file=PKGBUILD');
    check('old PKGBUILD links still work', $status === 302 && ($headers['location'] ?? '') === ASSET_BASE . 'PKGBUILD');

    foreach (['/downloads/download.php', '/downloads/download.php?platform=nonsense', '/downloads/download.php?platform=../../etc/passwd', '/downloads/download.php?file=../releases.php'] as $path)
        check("rejects $path", get($port, $path)[0] === 404);

    $post = stream_context_create(['http' => ['method' => 'POST', 'header' => 'Content-Type: application/x-www-form-urlencoded', 'content' => 'email=test%40example.com', 'ignore_errors' => true]]);
    $reply = (string)@file_get_contents("http://127.0.0.1:$port/SaveEmail.php", false, $post);
    check('subscriber emails are saved in the data directory, not in the site folder',
        strpos($reply, 'saved') !== false && strpos((string)@file_get_contents("$dataDir/emails.csv"), 'test@example.com') !== false
        && !file_exists(SITE_ROOT . '/database/emails.csv'));

    $hits = (int)file_get_contents("$fixtureDir/hits");
    check('GitHub is asked once, then the answer is cached', $hits === 1, "$hits requests");

    [$status, , $page] = get($port, '/downloadspage.php');
    check('downloads page shows the version and real file name',
        $status === 200 && strpos($page, 'Latest release: v1.3.0') !== false && strpos($page, 'sudo apt install ./lima_1.3.0_amd64.deb') !== false);
    check('downloads page has a working Windows download', strpos($page, 'download.php?platform=windows') !== false && strpos($page, 'Coming soon') === false);
    check('downloads page states the right GPU requirement', strpos($page, 'RTX 4000 series') !== false && strpos($page, '3000') === false);
    [$status, , $page] = get($port, '/index.php');
    check('front page buttons and install command follow the release',
        $status === 200 && strpos($page, 'platform=deb') !== false && strpos($page, 'platform=windows') !== false
        && strpos($page, './lima_1.3.0_amd64.deb') !== false && strpos($page, '1.0-1') === false && strpos($page, 'Coming soon') === false);
});

scenario('Nothing published yet (GitHub says 404)', 600, function ($port, $setMode, $dataDir) {
    $setMode('none');
    [$status, $headers] = get($port, '/downloads/download.php?platform=deb');
    check('downloads go to the releases page', $status === 302 && ($headers['location'] ?? '') === RELEASES_PAGE);
    check('nothing is counted', counts($dataDir) === []);
    foreach (['/downloadspage.php', '/index.php'] as $path) {
        [$status, , $page] = get($port, $path);
        check("$path still renders, with buttons to the releases page", $status === 200 && strpos($page, 'href="' . RELEASES_PAGE . '"') !== false);
    }
});

scenario('GitHub is down and nothing is cached', 600, function ($port, $setMode) {
    $setMode('error');
    [$status, $headers] = get($port, '/downloads/download.php?platform=windows');
    check('downloads go to the releases page', $status === 302 && ($headers['location'] ?? '') === RELEASES_PAGE);
    check('pages still render', get($port, '/downloadspage.php')[0] === 200 && get($port, '/index.php')[0] === 200);
});

scenario('GitHub goes down after a release was seen', 0, function ($port, $setMode) {
    [$status] = get($port, '/downloads/download.php?platform=deb');
    check('first download works', $status === 302);
    $setMode('error');
    [$status, $headers] = get($port, '/downloads/download.php?platform=deb');
    check('the last known release is still served', $status === 302 && ($headers['location'] ?? '') === ASSET_BASE . 'lima_1.3.0_amd64.deb');
    [$status, , $page] = get($port, '/downloadspage.php');
    check('the downloads page still shows the version', $status === 200 && strpos($page, 'v1.3.0') !== false);
});

scenario('A release whose files are not on the LIMA repository', 600, function ($port, $setMode, $dataDir, $fixtureDir) {
    file_put_contents("$fixtureDir/release.json", release_json('https://evil.example/download/'));
    [$status, $headers] = get($port, '/downloads/download.php?platform=deb');
    check('is never redirected to', $status === 302 && ($headers['location'] ?? '') === RELEASES_PAGE);
});

echo "\n" . ($failures === 0 ? 'All download tests passed' : "$failures download test(s) FAILED") . "\n";
exit($failures === 0 ? 0 : 1);
