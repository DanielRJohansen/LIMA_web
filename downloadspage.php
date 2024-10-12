<?php include('header.php'); ?>

<main class="container mt-5" style="padding-top: 50px; padding-bottom: 150px;">
    <section class="text-light mb-4 text-center">
        <h1 style="font-size: 3rem; font-weight: bold;">Download LIMA Dynamics</h1>
        <p style="font-size: 1.25rem;">Choose your distribution to get started:</p>
    </section>

    <div class="row justify-content-center">
        <!-- Arch Linux Column -->
        <div class="col-md-5 text-center mb-4">
            <div class="card bg-dark text-light h-100 d-flex flex-column flex-grow-1">
                <div>
                    <!-- Button at the top of the card body -->
                    <div class="card-body d-flex flex-column align-items-center" style="padding-top:30px;">                    
                        <a href="downloads/download.php?file=PKGBUILD" class="btn btn-lg" role="button" style="background-color: #1793D1; color: white;">Download for Arch Linux</a>
                    </div>

                    <!-- List content -->
                    <div class="card-body d-flex flex-column">                    
                        <ul class="form-text text-muted mt-3 mb-3" style="text-align: left; list-style-position: inside; padding-left: 1rem;">
                            <strong>Install Instructions:</strong>
                            <li>Make sure your pc has an NVIDIA graphicscard >= 3000 series</li>
                            <li>Download the PKGBUILD file.</li>
                            <li>Open a terminal and navigate to the download location.</li>
                            <li>Run <code>makepkg -si</code> to install the package + dependencies</li>
                            <li>Reboot your system if your driver was updated</li>
                            <li>Run <code>lima selftest</code> to verify the installation. </li>                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debian/Ubuntu Column -->
        <div class="col-md-5 mb-4">
            <div class="card bg-dark text-light h-100 d-flex flex-column flex-grow-1">
                <div>
                    <div class="card-body d-flex flex-column align-items-center" style="padding-top:30px;">
                        <!-- Button at the top of the card body -->
                        <a href="downloads/download.php?file=lima_1.0-1_amd64.deb" class="btn btn-lg" role="button" style="background-color: #E95420; color: white;">Download for Debian/Ubuntu</a>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- List content -->
                        <ul class="form-text text-muted mt-3 mb-3" style="text-align: left; list-style-position: inside; padding-left: 1rem;">
                            <strong>Install Instructions:</strong>
                            <li>Make sure your pc has an NVIDIA graphicscard >= 3000 series</li>
                            <li>Download the .deb file.</li>
                            <li>Open a terminal and navigate to the download location.</li>
                            <li>Run <code>sudo apt install ./lima_1.0-1_amd64.deb</code> to install the package + dependencies</li>
                            <li>Reboot your system if your driver was updated</li>
                            <li>Run <code>lima selftest</code> to verify the installation.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Help Card -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card bg-dark mb-3">
                <div class="card-body text-center">
                    <p>Experiencing issues? Please copy-paste the output of your terminal and send it to us in an email to <strong>support@lima-dynamics.com</strong>. We will get back to you ASAP.</p>
                </div>
            </div>
        </div>
    </div>
</main>


<?php include('footer.php'); ?>
