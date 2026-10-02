<?php
require_once __DIR__ . '/releases.php';
$release = lima_latest_release();
$debName = $release['assets']['deb']['name'] ?? 'lima_VERSION_amd64.deb';
$pageTitle = 'Download — LIMA MD';
include('header.php');
?>
        <main class="content-page">
            <div class="eyebrow">Get LIMA MD</div>
            <h1>Install. Verify.<br>Get to work.</h1>
            <p>Choose your platform. LIMA requires an NVIDIA graphics card, RTX 4000 series or newer, with a recent NVIDIA driver.</p>
            <?php if ($release): ?>
                <p class="note">Latest release: <?php echo lima_h($release['tag']); ?> · <a href="<?php echo lima_h($release['url']); ?>" target="_blank" rel="noreferrer">Release notes ↗</a></p>
            <?php endif; ?>
            <div class="download-grid">
                <section class="download-option">
                    <h2>Debian / Ubuntu</h2>
                    <a class="download" href="<?php echo lima_h(lima_download_href($release, 'deb')); ?>">↓ Download .deb</a>
                    <ol>
                        <li>Download the package and open a terminal in the download directory.</li>
                        <li>Install the package and dependencies:<code>sudo apt install ./<?php echo lima_h($debName); ?></code></li>
                        <li>Reboot if your graphics driver was updated.</li>
                        <li>Verify the installation:<code>lima selftest</code></li>
                    </ol>
                </section>
                <section class="download-option">
                    <h2>Arch Linux</h2>
                    <a class="download" href="<?php echo lima_h(lima_download_href($release, 'pkgbuild')); ?>">↓ Download PKGBUILD</a>
                    <ol>
                        <li>Download the PKGBUILD and open a terminal in the download directory.</li>
                        <li>Build and install the package and dependencies:<code>makepkg -si</code></li>
                        <li>Reboot if your graphics driver was updated.</li>
                        <li>Verify the installation:<code>lima selftest</code></li>
                    </ol>
                </section>
                <section class="download-option">
                    <h2>Windows</h2>
                    <a class="download" href="<?php echo lima_h(lima_download_href($release, 'windows')); ?>">↓ Download .zip</a>
                    <ol>
                        <li>Unzip the download anywhere and keep the files together.</li>
                        <li>Open a terminal in that folder, or add the folder to your PATH to run LIMA from anywhere.</li>
                        <li>Verify the installation:<code>lima selftest</code></li>
                    </ol>
                </section>
            </div>
            <p>Other Linux distributions: <a href="<?php echo lima_h(lima_download_href($release, 'linux')); ?>">download the .tar.gz</a>, extract it, and run <code>bin/lima</code>. It needs glfw, OpenGL and oneTBB.</p>
            <p>LIMA is free for small companies, for noncommercial and academic use, and free for anyone to evaluate for 31 days. <a href="https://github.com/DanielRJohansen/LIMA/blob/main/LICENSE.txt" target="_blank" rel="noreferrer">License ↗</a></p>
            <p>Need help? Send your terminal output to <a href="mailto:support@lima-dynamics.com">support@lima-dynamics.com</a>.</p>
        </main>
<?php include('footer.php'); ?>
