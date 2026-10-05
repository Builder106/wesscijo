(() => {
  "use strict";

  const view = new URLSearchParams(window.location.search).get("view");
  if (!["research", "feature", "nohero"].includes(view)) return;

  document.body.classList.add("article-route");

  const titles = {
    research: "Hydroxylation Pattern of Bile Salt Governs Mixed Micelle Architecture and Peptide Association: Insights from Molecular Dynamics",
    feature: "The Birds of Wesleyan and How To Find Them",
    nohero: "The Math Behind LLMs",
  };
  const labels = { research: "Research", feature: "Feature", nohero: "Essay" };

  function section(id, title, content) {
    return `<section class="reading-section" id="${id}" tabindex="-1" aria-labelledby="${id}-title" data-od-id="${view}-${id}">
      <h2 id="${id}-title" data-od-id="${view}-${id}-title">${title}</h2>${content}</section>`;
  }

  function figure(number, photography = false) {
    const label = `${photography ? "Image" : "Figure"} ${number}`;
    const title = photography
      ? (number === 1 ? "Field Observation: Habitat Survey" : "Field Observation: Canopy Specimen")
      : (number === 1 ? "Micellar Radial Distribution Function" : "Peptide Association Interface");
    const desc = photography
      ? (number === 1 ? "Photographic census of foraging territory at the Long Lane farmland perimeter." : "High-resolution telephoto capture of nesting pair within mature oak canopy.")
      : (number === 1 ? "Radial density profiles of cholate and deoxycholate headgroups across 150 ns equilibrium simulation." : "Cross-sectional visualization of peptide hydrophobic residue insertion into the micellar core.");
    const caption = photography
      ? `${label}. Specimen observations recorded across campus arboretum habitats. <span>Wesleyan Naturalist Field Archive.</span>`
      : `${label}. Equilibrium trajectory analysis at 300 K under physiological ionic strength. <span>Computational Biophysics Laboratory, Wesleyan University.</span>`;

    return `<figure class="evidence full-evidence" data-od-id="${view}-figure-${number}">
      <div class="evidence-space"><span>${label}</span><div>
        <strong>${title}</strong>
        <p>${desc}</p>
      </div></div>
      <figcaption>${caption}</figcaption></figure>`;
  }

  function register(id, label, items) {
    return `<details class="register" data-od-id="${view}-${id}">
      <summary data-od-id="${view}-${id}-toggle">${label}</summary>
      <div class="register-content"><ul>${items.map((item) => `<li>${item}</li>`).join("")}</ul></div>
    </details>`;
  }

  const researchChapters = [
    ["introduction", "Abstract & Introduction"], ["figure-one", "Figure 1"],
    ["figure-two", "Figure 2"], ["table-one", "Table 1"],
    ["evidence-register", "Supplementary figures & tables"], ["references", "References"],
  ];
  const featureChapters = [
    ["introduction", "Field Notes"], ["photography", "Observations"],
    ["image-register", "Sightings catalog"], ["references", "Field records"],
  ];
  const essayChapters = [
    ["introduction", "Introduction"], ["reading", "The argument"],
    ["references", "Selected bibliography"],
  ];

  const researchBody =
    section("introduction", "Abstract & Introduction", `<p>Mixed bile salt micelles serve as crucial biological surfactant systems facilitating lipid digestion and peptide transport. Understanding how specific hydroxylation patterns influence micelle morphology remains fundamental to modeling biological interfacial phenomena.</p>
      <p>Here we employ all-atom molecular dynamics simulations to quantify the structural reorganization, hydrodynamic dimensions, and association kinetics of representative therapeutic peptides with distinct bile salt assemblies.</p>`) +
    section("figure-one", "Micellar Structure Analysis", figure(1)) +
    section("figure-two", "Peptide Association Interface", `<div class="adjacent-evidence">${figure(2)}<div>
      <p>Equilibrium radial distribution functions demonstrate preferential localization of hydrophobic residues within the micelle core, while charged termini extend into the hydration shell.</p>
      <p>Solvation free energy profiles corroborate the stabilization observed during continuous 200 ns trajectory segments across all simulation replicates.</p>
    </div></div>`) +
    section("table-one", "Structural Dimensions & Diffusion Constants", `<table class="data-table" data-od-id="research-table-one">
      <caption>Table 1. Equilibrium structural parameters of mixed micelle assemblies at 300 K.</caption>
      <thead><tr><th scope="col">System</th><th scope="col">Aggregation Number (N)</th><th scope="col">Hydrodynamic Radius (nm)</th></tr></thead>
      <tbody><tr><th scope="row">Trihydroxy Bile Salt</th><td>48 ± 3</td><td>2.42 ± 0.08</td></tr>
      <tr><th scope="row">Dihydroxy Bile Salt</th><td>62 ± 4</td><td>2.88 ± 0.11</td></tr></tbody>
    </table>`) +
    section("evidence-register", "Supplementary Data & Extended Figures", register("remaining-evidence", "Figures 3–7 / Tables 2–4", [
      "Figure 3: Solvent-accessible surface area across simulation trajectories.",
      "Figure 4: Root-mean-square fluctuation (RMSF) of bound peptide backbone atoms.",
      "Figure 5: Electrostatic potential map of micellar surface at neutral pH.",
      "Figure 6: Hydrogen-bond occupancy between bile salt hydroxyl groups and peptide donors.",
      "Figure 7: Cluster analysis of representative micellar conformational states.",
      "Table 2: Force field parameters and partial charges for novel bile salt analogs.",
      "Table 3: Solvation free energy calculations via thermodynamic integration.",
      "Table 4: Diffusion coefficients determined from mean square displacement.",
    ])) +
    section("references", "References", `<p>Literature citations documenting computational methods, micellar mechanics, and peptide transport kinetics.</p>
      <details class="register" data-od-id="research-reference-register">
        <summary data-od-id="research-references-toggle">41 Citations &amp; Bibliography</summary>
        <div class="register-content"><ul class="reference-grid">${[
          "1. Small, D. M. (1971). The Physical Chemistry of Cholanic Acids. Plenum Press, New York.",
          "2. Carey, M. C., & Small, D. M. (1970). Micelle formation by bile salts. Arch. Intern. Med., 130(4), 506–527.",
          "3. Hofmann, A. F. (1999). Bile acids: the good, the bad, and the ugly. News Physiol. Sci., 14, 24–29.",
          "4. Nichols, J. W., & Ozarowski, J. (1990). Sizing of bile salt-phospholipid mixed micelles. Biochemistry, 29(19), 4600–4606.",
          "5. Marrink, S. J., et al. (2007). The MARTINI force field: coarse grained model for biomolecular simulations. J. Phys. Chem. B, 111(27), 7812–7824.",
          "6. Hess, B., Kutzner, C., van der Spoel, D., & Lindahl, E. (2008). GROMACS 4: Algorithms for highly efficient, relaxed, and flexible molecular simulations. J. Chem. Theory Comput., 4(3), 435–447.",
          "7. Darden, T., York, D., & Pedersen, L. (1993). Particle mesh Ewald: An N·log(N) method for Ewald sums in large systems. J. Chem. Phys., 98, 10089–10092.",
          "8. Jorgensen, W. L., et al. (1983). Comparison of simple potential functions for simulating liquid water. J. Chem. Phys., 79, 926–935.",
          ...Array.from({ length: 33 }, (_, i) => `${i + 9}. Additional reference cited in full manuscript (Wesleyan University Library Repository).`)
        ].map((r) => `<li>${r}</li>`).join("")}</ul></div>
      </details>`);

  const featureBody =
    section("introduction", "Room for observation", `<p>A field note begins with attention. Across the hills and forest margins of the Wesleyan campus, resident and migratory bird species adapt to seasonal microclimates and mature hardwood canopies.</p>
      <p>This field guide tracks nesting habits, foraging corridors, and seasonal movements across the Long Lane farmland and the Wadsworth arboretum border.</p>`) +
    section("photography", "Field Observations", `${figure(1, true)}
      <p>Early morning vantage points along the tree line reveal active mixed-species foraging flocks navigating the lower oak canopy.</p>${figure(2, true)}`) +
    section("image-register", "Field Catalog & Sightings", register("remaining-images", "Sightings Catalog: Observations 3–6", [
      "Image 3: Northern Cardinal (Cardinalis cardinalis) perched in flowering dogwood.",
      "Image 4: Red-tailed Hawk (Buteo jamaicensis) thermaling above Long Lane athletic fields.",
      "Image 5: Tufted Titmouse (Baeolophus bicolor) at winter feeding station.",
      "Image 6: Black-capped Chickadee (Poecile atricapillus) nesting cavity in decaying birch.",
    ])) +
    section("references", "Field Records & Methodology", `<p>Field observations compiled by Wesleyan Naturalist Society student researchers and faculty advisors during the 2025–2026 academic year.</p>`);

  const essayBody =
    section("introduction", "Begin with the question", `<p>A text-first opening gives an idea space to develop. Large language models operate not through genuine comprehension, but through high-dimensional geometric proximity in latent semantic space.</p>
      <p>Examining the linear algebra and probabilistic foundations that govern transformer attention mechanisms reveals how statistical associations emulate syntactic structure.</p>`) +
    section("reading", "Geometry of Attention", `<p>At the heart of the transformer architecture lies the scaled dot-product attention calculation. By projecting token representations into query, key, and value vectors, the model computes context-dependent affinities across arbitrary sequence lengths.</p>
      <p>Understanding these mathematical transforms demystifies the behavior of modern generative systems, locating their power within verifiable linear transformations and continuous optimization.</p>`) +
    section("references", "Selected Bibliography", `<p>Foundational works on deep learning architectures, attention mechanisms, and high-dimensional representations in mathematical statistics.</p>`);

  const chapters = view === "research" ? researchChapters : view === "feature" ? featureChapters : essayChapters;
  const body = view === "research" ? researchBody : view === "feature" ? featureBody : essayBody;
  const related = Object.keys(titles).filter((key) => key !== view).map((key) =>
    `<p><a class="read-link return-link" href="?view=${key}" data-od-id="${view}-read-${key}">${titles[key]}</a></p>`).join("");

  document.getElementById("main").innerHTML = `
    <header class="article-cover ${view === "research" ? "research-cover" : ""}" data-od-id="${view}-cover">
      <nav class="breadcrumb" aria-label="Breadcrumb"><a href="?view=home" data-od-id="${view}-home">Home</a></nav>
      <div class="article-publication-imprint">
        <img class="imprint-seal" src="assets/logo-192.png" alt="" width="26" height="26" aria-hidden="true">
        <span>Wesleyan Science Journal • Volume 14</span>
      </div>
      <h1 data-od-id="${view}-title">${titles[view]}</h1>
      <p class="article-context">${labels[view]}</p>
    </header>
    <div class="reading-surface" data-od-id="${view}-reading-surface"><div class="reading-grid">
      <nav class="chapter-map" aria-labelledby="contents-title" data-od-id="${view}-contents">
        <h2 id="contents-title">Contents</h2>
        <ol>${chapters.map(([id, label], i) => `<li><a href="?view=${view}#${id}" data-od-id="${view}-chapter-${id}"><span aria-hidden="true">${String(i + 1).padStart(2, "0")}</span>${label}</a></li>`).join("")}</ol>
      </nav>
      <article class="article-body" data-od-id="${view}-body">
        ${body}
        ${section("continue-reading", "Other branches", `<nav aria-label="Other stories">${related}<p><a class="read-link return-link" href="?view=home" data-od-id="${view}-return-home">Return to the homepage</a></p></nav>`)}
      </article>
    </div></div>`;

  document.title = `${titles[view]} — WesSciJo`;
  document.querySelector(".skip-link").setAttribute("href", `?view=${view}#main`);
  if (typeof window.initReadingInteractions === "function") {
    window.initReadingInteractions();
  }
})();
