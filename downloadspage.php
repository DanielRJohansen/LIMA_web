<?php $pageTitle = 'Download — LIMA MD'; include('header.php'); ?>
        <main class="content-page">
            <div class="eyebrow">Get LIMA MD</div>
            <h1>Install. Verify.<br>Get to work.</h1>
            <p>Choose your Linux distribution. These packages require an NVIDIA graphics card, RTX 3000 series or newer.</p>
            <div class="download-grid">
                <section class="download-option">
                    <h2>Debian / Ubuntu</h2>
                    <a class="download" href="downloads/download.php?file=lima_1.0-1_amd64.deb">↓ Download .deb</a>
                    <ol>
                        <li>Download the package and open a terminal in the download directory.</li>
                        <li>Install the package and dependencies:<code>sudo apt install ./lima_1.0-1_amd64.deb</code></li>
                        <li>Reboot if your graphics driver was updated.</li>
                        <li>Verify the installation:<code>lima selftest</code></li>
                    </ol>
                </section>
                <section class="download-option">
                    <h2>Arch Linux</h2>
                    <a class="download" href="downloads/download.php?file=PKGBUILD">↓ Download PKGBUILD</a>
                    <ol>
                        <li>Download the PKGBUILD and open a terminal in the download directory.</li>
                        <li>Build and install the package and dependencies:<code>makepkg -si</code></li>
                        <li>Reboot if your graphics driver was updated.</li>
                        <li>Verify the installation:<code>lima selftest</code></li>
                    </ol>
                </section>
                <section class="download-option">
                    <h2>Windows</h2>
                    <button class="download" type="button" disabled aria-describedby="windows-status">Windows · Coming soon</button>
                    <p id="windows-status">We’re actively working on a Windows installer. It isn’t available yet.</p>
                </section>
            </div>
            <p>Need help? Send your terminal output to <a href="mailto:support@lima-dynamics.com">support@lima-dynamics.com</a>.</p>
        </main>
<?php include('footer.php'); ?>
