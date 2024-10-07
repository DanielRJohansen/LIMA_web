<?php include('header.php'); ?>

<body>
    <main class="container mt-5" style="padding-top: 100px; padding-bottom: 150px;">
        <!-- Title Section -->
        <section class="mb-4 text-light" style="padding-bottom: 40px;">
            <h1 style="font-size: 3rem; font-weight: bold;">Molecular Dynamics <br> for the Generative AI era</h1>
        </section>

        <!-- Download Buttons Section -->
        <section class="mb-4" style="padding-left:0px;">
            <!-- Arch Linux Button: Official color #1793D1 -->
            <a href="downloads/download.php?file=PKGBUILD" class="btn btn-lg me-3" role="button" style="background-color: #1793D1; color: white;">Arch-linux</a>

            <!-- Ubuntu Button: Official color #E95420 -->
            <a href="downloads/download.php?file=lima_1.0-1_amd64.deb" class="btn btn-lg" role="button" style="background-color: #E95420; color: white;">Debian/Ubuntu</a>
        </section>

        <!-- Description Section -->
        <section>
            <p style="font-size: 1.25rem;">Install and run the Membranebuilder today - it's free!</p>
        </section>
    </main>


    <!-- Subscribe Section -->
    <section class="py-5" style="background-color: rgba(0,0,0,0.1); padding-top: 100px;">
        <div class="container">
            <div class="row">
                <!-- Left: Signup Form -->
                <div class="col-md-6">
                    <h4>Stay updated</h4>
                    <p>Sign up to receive devlogs and feature updates from us.</p>

                    <form id="emailForm" class="row gx-2">
                        <div class="col-auto">
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required style="min-width: 200px;">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Sign Up</button>
                        </div>
                    </form>

                </div>

                <!-- Right: Contact Information -->
                <div class="col-md-6 text-md-end">
                    <h5>LIMA Dynamics Aps</h5>
                    <ul class="list-unstyled">
                        <li>Denmark</li>
                        <li>Noerregade 18</li>
                        <li>5000 Odense</li>
                        <li>info@lima-dynamics.com</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</body>

<?php include('footer.php'); ?>

<script>
    document.getElementById('emailForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var email = this.email.value;

        // Send the email to SaveEmail.php via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'email/SaveEmail.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert('Thank you for signing up!'); // Display success message
                // Optionally clear the form
                document.getElementById('emailForm').reset();
            }
        };
        xhr.send('email=' + encodeURIComponent(email));
    });
</script>
