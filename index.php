<?php include('header.php'); ?>
        <main class="workspace">
            <section class="intro" aria-labelledby="headline">
                <div class="eyebrow">Molecular dynamics</div>
                <h1 id="headline">1000s of mdrun's.<br>1 interface.</h1>
                <p class="muted">LIMA MD is a molecular dynamics engine for researchers and their agents. Ultra-low latency in system preparation keeps setup overhead low—even across thousands of small simulations.</p>
                <div class="download-actions">
                    <a class="download" href="downloads/download.php?file=lima_1.0-1_amd64.deb">↓ Debian / Ubuntu</a>
                    <a class="download" href="downloads/download.php?file=PKGBUILD">↓ Arch Linux</a>
                    <button class="download" type="button" disabled aria-describedby="windows-status">Windows · Coming soon</button>
                </div>
                <p class="note">Linux packages · NVIDIA GPU required</p>
                <details class="installation">
                    <summary>Installation</summary>
                    <div class="install-option"><span>Debian / Ubuntu</span><code>sudo apt install ./lima_1.0-1_amd64.deb</code></div>
                    <div class="install-option"><span>Arch Linux</span><code>makepkg -si</code></div>
                    <div class="install-option"><span>Verify the installation</span><code>lima selftest</code></div>
                    <a href="downloadspage.php">Full installation instructions ↗</a>
                </details>
            </section>
            <section class="console-section" aria-label="Explore LIMA's CLI">
                <div class="console-window">
                    <div class="window-bar"><span>~/toy_console</span><span class="accent">no commands actually run</span></div>
                    <pre class="console-output" id="lima-console-output" aria-label="LIMA CLI help"><?php
                        $cliJson = file_get_contents(__DIR__ . '/resources/cli-help.json');
                        $cli = json_decode($cliJson, true, 512, JSON_THROW_ON_ERROR);
                        echo htmlspecialchars("❯ lima --help\n\n" . $cli['generalHelp'], ENT_QUOTES, 'UTF-8');
                    ?></pre>
                    <form class="console-form" id="lima-console-form">
                        <label for="lima-console-input" aria-label="Command prompt">❯</label>
                        <input id="lima-console-input" aria-label="LIMA command" placeholder="lima buildmembrane" autocomplete="off" autocapitalize="off" spellcheck="false" maxlength="4096">
                        <!--<button type="submit">Enter</button>-->
                    </form>
                    <p class="note console-status" id="lima-console-status" aria-live="polite">Try 'lima mdrun' · ↑ history · Tab complete</p>
                    <noscript><p class="note">Enable JavaScript to explore command help.</p></noscript>
                </div>
            </section>
        </main>
        <script type="application/json" id="lima-cli-data"><?php echo json_encode($cli, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR); ?></script>
        <script src="cli-console.js" defer></script>
<?php include('footer.php'); ?>
