<?php $pageTitle = 'Blog — LIMA MD'; include('header.php'); ?>
        <main class="content-page blog-page">
            <div class="eyebrow">LIMA MD / Blog</div>
            <div class="blog-intro">
                <h1>Notes from the molecular workspace.</h1>
                <p>Feature previews, development updates, and ideas from the work of making molecular dynamics more accessible.</p>
            </div>

            <article class="blog-post">
                <div class="blog-post-meta"><span>Development update</span> · Auto batching &amp; EM</div>
                <h2>More simulations per GPU. Faster energy minimization.</h2>
                <figure class="blog-video">
                    <iframe src="https://www.youtube.com/embed/RfBSzSnZFV0" title="Auto batching and energy minimization preview" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    <figcaption>Auto batching and faster energy minimization in LIMA.</figcaption>
                </figure>
                <div class="blog-post-copy">
                    <p>LIMA now automatically batches simulations on the GPU. Start multiple simulations, and LIMA runs them together instead of leaving most of the GPU idle on small systems. The result is 1.6-2.2x as much ns/day with a saturated GPU.</p>
                    <p>In this demo I'm running a water box at 28 different temperatures, to plot self-diffusion coefficient. Notice how simple the script to achieve this is.</p>
                    <p>The steps needed to complete energy minimization have also been drastically reduced. This matters most for large systems: the membrane and vesicle shown here minimize in about 25 seconds.</p>
                    <p>Feedback, feature ideas and interesting conversations are always welcome, so please do reach out if you're interested in trying LIMA or collaborating.</p>
                </div>
                <a class="blog-post-link" href="https://youtube.com/shorts/RfBSzSnZFV0?feature=share" target="_blank" rel="noreferrer">Watch on YouTube ↗</a>
            </article>

            <article class="blog-post">
                <div class="blog-post-meta"><span>Feature preview</span> · LiveEdit</div>
                <h2>Move atoms. Preview the simulation. Keep the momentum.</h2>
                <figure class="blog-video">
                    <iframe src="https://www.youtube.com/embed/Rr_1RiGzop0" title="LiveEdit feature preview" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    <figcaption>LiveEdit feature preview.</figcaption>
                </figure>
                <div class="blog-post-copy">
                    <p>LiveEdit is an in-progress LIMA feature for dragging atoms around and previewing an MD simulation before running it.</p>
                    <p>The goal is to make it easier to explore a molecular setup without rerunning energy minimization every time you make a small adjustment.</p>
                    <p>It is still a work in progress. Feedback, feature ideas, and conversations about computational-chemistry innovation are very welcome.</p>
                </div>
                <a class="blog-post-link" href="https://www.linkedin.com/feed/update/urn:li:activity:7448255953856090112" target="_blank" rel="noreferrer">See the original announcement on LinkedIn ↗</a>
            </article>
        </main>
<?php include('footer.php'); ?>
