<?php include('header.php'); ?>

<main class="container mt-5" style="padding-top: 50px; padding-bottom: 150px;">
    <!-- Title Section -->
    <section class="mb-4 text-light" style="padding-bottom: 40px;">

        <div class="row">
            <div class="col-md-8">
                <h1>Mission</h1>

                <p>
                    <code>LIMA</code> Dynamics was conceived with the intention of empowering researchers working to understand 
                    and treat complex diseases, with the technological advancements in the field of Artificial Intelligence and Machine Learning.
                </p>
                <p>
                    The goal is to build a generative AI model that can design novel drugs and predict possible side effects before these candidates move on 
                    to clinical testing. Although this is an ambitious goal, significant progress has already been made by organizations 
                    like <a href="https://alphafold.com/">Google DeepMind</a> and <a href="https://jcheminf.biomedcentral.com/articles/10.1186/s13321-024-00812-5">AstraZeneca</a>.
                </p>
                <br>
            </div>
            <div class="col-md-4">
                <img src="resources/a100.png" alt="A100 GPU" class="img-fluid">
            </div>
        </div>

         <div class="row">
            <div class="col-md-4">
                <img src="resources/IMD.png" alt="Generative AI Model" class="img-fluid">
            </div>
            <div class="col-md-8">
                <h2>Generative AI in Drug Discovery</h2>

                <p>
                    In classical drug discovery, researchers sift through a large database of known compounds until they find one that has a desired effect on a target virus/protein.
                    This approach has proven very effective so far, but it is only feasible to maintain a database of a few tens of thousands of compounds. But where we are going,
                    we don't need thousands; we need billions.
                </p>
                <p>
                    A new method gaining traction is called Inverse Molecular Design (IMD). With this approach, researchers specify the desired behavior or effect, 
                    and generative AI constructs the corresponding drug molecules.
                    Given access to a "digital laboratory," these models could even predict and optimize to remove unwanted side effects. This would not only allow researchers
                    to discover much more complex drugs but could also shorten the clinical trials phase if the model warns about the probability of various side effects.
                </p>
                <p>
                    One way IMD works is through a diffusion model. A model is given a target protein to inhibit (green molecule in the figure). 
                    The model generates an initial structure that meets the criteria and iteratively refines it to produce a viable drug candidate.
                </p>
                <br>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <h2>Where does <code>LIMA</code> fit in?</h2>
                <p>
                    Generative models for drug design navigate a configurational space vastly larger than their counterparts in image generation,
                    necessitating massive volumes of highly specific training data.
                    LIMA is the first Molecular Dynamics engine built for generative AI. It is the first to both plug into existing AI training frameworks (PyTorch) and
                    leverage NVIDIA GPUs to generate massive amounts of data. The idea is to have the model train itself by making a physical prediction, e.g., "this drug will
                    inhibit this protein." The model will then request a simulation of the drug-protein interaction and use the results to update its own weights.
                </p>
                <br>

                <h4>So, what <i>is</i> <code>LIMA</code>?</h4>
                <p>
                    It is a physics oracle that tells an AI model if a prediction is correct or not.
                    Lima is not the model, but an on-demand infinite dataset.
                </p>
            </div>
            <div class="col-md-4">
                <img src="resources/limalogo/iconwhite.svg" alt="LIMA Symbol" class="img-fluid">
            </div>
        </div>

    </section>
</main>

<?php include('footer.php'); ?>
